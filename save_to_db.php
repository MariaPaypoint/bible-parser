<?php

require 'include.php';

function clear_db_text($mysqli, $translation_code) 
{
	// очистка примечаний
	$mysqli->query("
		DELETE FROM bible_notes
		WHERE bible_verse IN (SELECT code FROM bible_verses WHERE bible_book IN (SELECT code FROM bible_books WHERE bible_translation = '$translation_code'))
	");
	// printf("Затронутые строки (DELETE/bible_notes): %d\n", $mysqli->affected_rows);
	
	// очистка стихов
	$mysqli->query("
		DELETE FROM bible_verses
		WHERE bible_book IN (SELECT code FROM bible_books WHERE bible_translation = '$translation_code')
	");
	// printf("Затронутые строки (DELETE/bible_verses): %d\n", $mysqli->affected_rows);
	
	// очистка книг
	$mysqli->query("
		DELETE FROM bible_books
		WHERE bible_translation = '$translation_code'
	");
	// printf("Затронутые строки (DELETE/bible_books): %d\n", $mysqli->affected_rows);
	
	// еще?
}

function save_text_chapter_verses($mysqli, $book_code, $chapter) 
{
	$verses_str = '';
	foreach ( $chapter['verses'] as $verse ) 
	{
		$verses_str .= sprintf(
			"($verse[id], $chapter[id], $book_code, '%s', $verse[start_paragraph]),",
			$mysqli->real_escape_string($verse['htmlText'])
		);
	}
	$verses_str = substr_replace($verses_str, '', -1);
	$mysqli->query("
		INSERT INTO bible_verses
		  (verse_number, chapter_number, bible_book, text, start_paragraph)
		VALUES 
		  $verses_str
	");
	// printf("Затронутые строки (INSERT/bible_verses): %d\n", $mysqli->affected_rows);
}
function select_verse_code($mysqli, $verse_number, $chapter_number, $book_code) 
{
	$query = "
		SELECT code
		FROM bible_verses
		WHERE verse_number = $verse_number
		  AND chapter_number = $chapter_number
		  AND bible_book = $book_code
	";
	if ($result = $mysqli->query($query)) 
	{
		$obj = $result->fetch_object();
		return $obj->code;
	}
	else 
		die($query);
}

function save_text_chapter_notes($mysqli, $book_code, $chapter) 
{
	$notes_str = '';
	foreach ( $chapter['notes'] as $note ) {
		$verse_code = select_verse_code($mysqli, $note['verse_number'], $chapter['id'], $book_code);
		$notes_str .= sprintf(
			"($verse_code, $note[position], $note[id], '%s'),",
			$mysqli->real_escape_string($note['text'])
		);
	}
	if ( $notes_str ) {
		$notes_str = substr_replace($notes_str, '', -1);
		$mysqli->query("
			INSERT INTO bible_notes
			  (bible_verse, position, note_number, text)
			VALUES 
			  $notes_str
		");
		// printf("Затронутые строки (INSERT/bible_notes): %d\n", $mysqli->affected_rows);
	}
}

function save_text_book($mysqli, $translation_code, $book) 
{
	$mysqli->query("
		INSERT INTO bible_books
		SET 
		  book_number       = '$book[id]',
		  bible_translation = '$translation_code',
		  name              = '$book[fullName]'
	");
	$book_code = $mysqli->insert_id;
	// printf("Затронутые строки (INSERT/bible_books): %d\n", $mysqli->affected_rows);
	
	foreach ( $book['chapters'] as $chapter ) {
		save_text_chapter_verses($mysqli, $book_code, $chapter);
		save_text_chapter_notes($mysqli, $book_code, $chapter);
	}
	
	print "Book $book[id] inserted to db with code $book_code\n";
}

function insert_or_update_translation($mysqli, $translation, $translationArray)
{
	$new_fields_str = "
		alias       = '$translation',
		name        = '$translationArray[shortName]',
		description = '$translationArray[fullName]',
		language    = '$translationArray[lang]'
	";
	
	// проверка наличия этого перевода и апдейт
	$result = $mysqli->query("
		SELECT code
		FROM bible_translations
		WHERE alias = '$translation'
	");
	
	if ( $obj = $result->fetch_object() )
	{
		$translation_code = $obj->code;
		printf("Translation already exists, code: %d\n", $translation_code);
		
		// добавление
		$mysqli->query("
			UPDATE bible_translations
			SET $new_fields_str
			WHERE code = $translation_code
		");
		// printf("Затронутые строки (UPDATE/bible_translations): %d\n", $mysqli->affected_rows);
	}
	else 
	{
		// добавление
		$mysqli->query("
			INSERT INTO bible_translations
			SET $new_fields_str
		");
		$translation_code = $mysqli->insert_id;
		// printf("Затронутые строки (INSERT/bible_translations): %d\n", $mysqli->affected_rows);
	}
	
	print "Translation $translation is in db with code $translation_code\n";
	
	return $translation_code;
}

function save_text_to_db($mysqli, $translation) 
{	
	$translationArray = get_translation_array($translation);

	$translation_code = insert_or_update_translation($mysqli, $translation, $translationArray);
	clear_db_text($mysqli, $translation_code);
	
	foreach ( $translationArray['books'] as $book ) {
		save_text_book($mysqli, $translation_code, $book);
	}
}

///////////////////

function clear_db_timecodes($mysqli, $voice_code) 
{
	$mysqli->query("
		DELETE FROM audio_alignments
		WHERE audio_voice = '$voice_code'
	");
	// printf("Затронутые строки (DELETE/audio_alignments): %d\n", $mysqli->affected_rows);
}

function insert_or_update_voice($mysqli, $translation_code, $voice, $voiceInfo)
{
	$new_fields_str = "
		alias             = '$voice',
		name              = '$voiceInfo[name]',
		description       = '$voiceInfo[description]',
		is_music          = '$voiceInfo[isMusic]',
		bible_translation = '$translation_code'
	";
	
	// проверка наличия этого перевода и апдейт
	$result = $mysqli->query("
		SELECT code
		FROM audio_voices
		WHERE alias = '$voice'
	");
	
	if ( $obj = $result->fetch_object() )
	{
		$voice_code = $obj->code;
		printf("Voice already exists, code: %d\n", $translation_code);
		
		// добавление
		$mysqli->query("
			UPDATE audio_voices
			SET $new_fields_str
			WHERE code = $voice_code
		");
		// printf("Затронутые строки (UPDATE/audio_voices): %d\n", $mysqli->affected_rows);
	}
	else 
	{
		// добавление
		$mysqli->query("
			INSERT INTO audio_voices
			SET $new_fields_str
		");
		$voice_code = $mysqli->insert_id;
		// printf("Затронутые строки (INSERT/audio_voices): %d\n", $mysqli->affected_rows);
	}
	
	print "Voice $voice is in db with code $voice_code\n";
	
	return $voice_code;
}

function select_all_books($mysqli, $translation_code)
{
	$query = "
		SELECT code, book_number
		FROM bible_books
		WHERE bible_translation = $translation_code
	";
	if ($result = $mysqli->query($query)) 
	{
		$books_codes = [];
		$rows = $result->fetch_all(MYSQLI_ASSOC);
		foreach ($rows as $row)
			$books_codes[$row['book_number']] = $row['code'];
		return $books_codes;
	}
	else 
		die($query);
}

function select_all_verses($mysqli, $translation_code, $books_codes)
{
	$book_codes_str = implode( ',', $books_codes );
	$query = "
		SELECT code, verse_number, chapter_number, bible_book
		FROM bible_verses
		WHERE bible_book IN ($book_codes_str)
		  ORDER BY bible_book, chapter_number
	";
	if ($result = $mysqli->query($query)) 
	{
		$verses_codes = [];
		$rows = $result->fetch_all(MYSQLI_ASSOC);
		foreach ($rows as $row)
			$verses_codes[$row['bible_book']][$row['chapter_number']][$row['verse_number']] = $row['code'];
		return $verses_codes;
	}
	else 
		die($query);
}

function insert_alignment($mysqli, $voice_code, $books_codes, $verses_codes, $voiceArray)
{
	$str = '';
	foreach ( $voiceArray['books'] as $book ) {
		// if ($book['id'] >= 2) break;
		$book_code = $books_codes[$book['id']];
		foreach ( $book['chapters'] as $chapter ) {
			// if ($chapter['id'] >= 2) break;
			// косяки выравнивания выявляем
			if ( !$chapter['verses'] )
				print "Error: book $book[id] / chapter $chapter[id] is empty!\n";
			else
				foreach ( $chapter['verses'] as $verse ) {
					$bible_verse = $verses_codes[$book_code][$chapter['id']][$verse['id']];
					$str .= "($voice_code, $bible_verse, $verse[begin], $verse[end], NULL),";
				}
		}
	}
	$str = substr_replace($str, '', -1);
	$query = "
		INSERT INTO audio_alignments
		  (audio_voice, bible_verse, begin, end, is_correct)
		VALUES
		  $str
	";
	// print $query;
	$mysqli->query($query);
	printf("Затронутые строки (INSERT/audio_alignments): %d\n", $mysqli->affected_rows);
}

function save_timecodes_to_db($mysqli, $translation, $voice) 
{
	$translationArray = get_translation_array($translation);
	$voiceArray = get_voice_array($translation, $voice);
	$voiceInfo = get_voice_info($voice);
	
	$translation_code = insert_or_update_translation($mysqli, $translation, $translationArray);
	$voice_code = insert_or_update_voice($mysqli, $translation_code, $voice, $voiceInfo);
	
	clear_db_timecodes($mysqli, $voice_code);
	
	$books_codes = select_all_books($mysqli, $translation_code);
	$verses_codes = select_all_verses($mysqli, $translation_code, $books_codes);
	insert_alignment($mysqli, $voice_code, $books_codes, $verses_codes, $voiceArray);
}

///////////////////

$export_type = determine_export_type(1);
$translation = determine_text_translation(2);

$mysqli = new mysqli("127.0.0.1", "root", "XSNx0evIpDBXUPSthhHq", "bible_pause", "3307");

if ( $export_type == 'TEXT' )
	save_text_to_db($mysqli, $translation, $export_type);

elseif ( $export_type == 'TIMECODES' )
{
	$voice = determine_voice_4bbl($translation, 3);
	save_timecodes_to_db($mysqli, $translation, $voice);
}

?>