<?php

require 'include.php';

function clear_db_text($mysqli, $translation_code) 
{
	// очистка заголовков
	$mysqli->query("
		DELETE FROM translation_titles
		WHERE before_translation_verse IN (SELECT code FROM translation_verses WHERE translation_book IN (SELECT code FROM translation_books WHERE translation = '$translation_code'))
	");
	//printf("Затронутые строки (DELETE/translation_notes): %d\n", $mysqli->affected_rows);
	
	// очистка примечаний
	$mysqli->query("
		DELETE FROM translation_notes
		WHERE translation_verse IN (SELECT code FROM translation_verses WHERE translation_book IN (SELECT code FROM translation_books WHERE translation = '$translation_code'))
	");
	//printf("Затронутые строки (DELETE/translation_notes): %d\n", $mysqli->affected_rows);
	
	// очистка стихов
	$mysqli->query("
		DELETE FROM translation_verses
		WHERE translation_book IN (SELECT code FROM translation_books WHERE translation = '$translation_code')
	");
	//printf("Затронутые строки (DELETE/translation_verses): %d\n", $mysqli->affected_rows);
	
	// очистка книг
	$mysqli->query("
		DELETE FROM translation_books
		WHERE translation = '$translation_code'
	");
	//printf("Затронутые строки (DELETE/translation_books): %d\n", $mysqli->affected_rows);
}

function save_text_chapter_verses($mysqli, $book_code, $chapter) 
{
	$verses_str = '';
	$prev_join = 0;
	foreach ( $chapter['verses'] as $verse ) 
	{
		//if ( $prev_join > 0 ) {
		//	$prev_join -= 1;
		//	continue;
		//}
		$verses_str .= sprintf(
			"($verse[id], $verse[join], $chapter[id], $book_code, '$verse[unformatedText]', '%s', $verse[start_paragraph]),\n",
			$mysqli->real_escape_string($verse['htmlText'])
		);
		$prev_join = max($prev_join, $verse['join']);
	}
	$verses_str = substr_replace($verses_str, '', -2);
	//print $verses_str;
	$mysqli->query("
		INSERT INTO translation_verses
		  (verse_number, verse_number_join, chapter_number, translation_book, text, html, start_paragraph)
		VALUES 
		  $verses_str
	");
	//printf("Затронутые строки (INSERT/translation_verses): %d\n", $mysqli->affected_rows);
}
function select_verse_code($mysqli, $verse_number, $chapter_number, $book_code) 
{
	$query = "
		SELECT code
		FROM translation_verses
		WHERE verse_number = $verse_number
		  AND chapter_number = $chapter_number
		  AND translation_book = $book_code
	";
	$result = $mysqli->query($query);
	
	if ( $obj = $result->fetch_object() )
		return $obj->code;
	else 
		die("Verse not found ($query)\n");
}

function save_text_chapter_titles($mysqli, $book_code, $chapter) 
{
	$titles_str = '';
	foreach ( $chapter['titles'] as $title ) {
		$verse_code = select_verse_code($mysqli, $title['before_verse_number'], $chapter['id'], $book_code);
		$titles_str .= sprintf(
			"($verse_code, '%s'),",
			$mysqli->real_escape_string($title['text'])
		);
	}
	if ( $titles_str ) {
		$titles_str = substr_replace($titles_str, '', -1);
		$mysqli->query("
			INSERT INTO translation_titles
			  (before_translation_verse, text)
			VALUES 
			  $titles_str
		");
		//printf("Затронутые строки (INSERT/translation_titles): %d\n", $mysqli->affected_rows);
	}
}
function save_text_chapter_notes($mysqli, $book_code, $chapter) 
{
	$notes_str = '';
	foreach ( $chapter['notes'] as $note ) {
		$verse_code = select_verse_code($mysqli, $note['verse_number'], $chapter['id'], $book_code);
		$notes_str .= sprintf(
			"($verse_code, $note[position_text], $note[position_html], $note[id], '%s'),",
			$mysqli->real_escape_string($note['text'])
		);
	}
	if ( $notes_str ) {
		$notes_str = substr_replace($notes_str, '', -1);
		$mysqli->query("
			INSERT INTO translation_notes
			  (translation_verse, position_text, position_html, note_number, text)
			VALUES 
			  $notes_str
		");
		//printf("Затронутые строки (INSERT/translation_notes): %d\n", $mysqli->affected_rows);
	}
}

function save_text_book($mysqli, $translation_code, $book) 
{
	$mysqli->query("
		INSERT INTO translation_books
		SET 
		  book_number       = '$book[id]',
		  translation = '$translation_code',
		  name              = '$book[fullName]'
	");
	$book_code = $mysqli->insert_id;
	//printf("Затронутые строки (INSERT/translation_books): %d\n", $mysqli->affected_rows);
	
	foreach ( $book['chapters'] as $chapter ) {
		save_text_chapter_verses($mysqli, $book_code, $chapter);
		save_text_chapter_notes($mysqli, $book_code, $chapter);
		save_text_chapter_titles($mysqli, $book_code, $chapter);
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
		FROM translations
		WHERE alias = '$translation'
	");
	
	if ( $obj = $result->fetch_object() )
	{
		$translation_code = $obj->code;
		print "Translation $translation already exists, code: $translation_code\n";
		
		// добавление
		$mysqli->query("
			UPDATE translations
			SET $new_fields_str
			WHERE code = $translation_code
		");
		//printf("Затронутые строки (UPDATE/translations): %d\n", $mysqli->affected_rows);
	}
	else 
	{
		// добавление
		$mysqli->query("
			INSERT INTO translations
			SET $new_fields_str
		");
		$translation_code = $mysqli->insert_id;
		//printf("Затронутые строки (INSERT/translations): %d\n", $mysqli->affected_rows);

		print "Translation $translation inserted to db with code $translation_code\n";
	}
	
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
		DELETE FROM voice_alignments
		WHERE voice = '$voice_code'
	");
	//printf("Затронутые строки (DELETE/voice_alignments): %d\n", $mysqli->affected_rows);
}

function insert_or_update_voice($mysqli, $translation_code, $voice, $voiceInfo)
{
	$new_fields_str = "
		alias         = '$voice',
		name          = '$voiceInfo[name]',
		description   = '$voiceInfo[description]',
		translation   = '$translation_code',
		is_music      = '$voiceInfo[isMusic]',
		link_template = '$voiceInfo[link_template]'
	";
	
	// проверка наличия этого перевода и апдейт
	$result = $mysqli->query("
		SELECT code
		FROM voices
		WHERE alias = '$voice'
	");
	
	if ( $obj = $result->fetch_object() )
	{
		$voice_code = $obj->code;
		print "Voice $voice already exists, code: $voice_code\n";
		
		// добавление
		$mysqli->query("
			UPDATE voices
			SET $new_fields_str
			WHERE code = $voice_code
		");
		//printf("Затронутые строки (UPDATE/voices): %d\n", $mysqli->affected_rows);
	}
	else 
	{
		// добавление
		$mysqli->query("
			INSERT INTO voices
			SET $new_fields_str
		");
		$voice_code = $mysqli->insert_id;
		//printf("Затронутые строки (INSERT/voices): %d\n", $mysqli->affected_rows);

		print "Voice $voice inserted to db with code $voice_code\n";
	}
	
	return $voice_code;
}

function select_all_books($mysqli, $translation_code)
{
	$query = "
		SELECT code, book_number
		FROM translation_books
		WHERE translation = $translation_code
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
		die('Error in query:'.$query);
}

function select_all_verses($mysqli, $translation_code, $books_codes)
{
	$book_codes_str = implode( ',', $books_codes );
	$query = "
		SELECT code, verse_number, chapter_number, translation_book
		FROM translation_verses
		WHERE translation_book IN ($book_codes_str)
		  ORDER BY translation_book, chapter_number
	";
	if ($result = $mysqli->query($query)) 
	{
		$verses_codes = [];
		$rows = $result->fetch_all(MYSQLI_ASSOC);
		foreach ($rows as $row)
			$verses_codes[$row['translation_book']][$row['chapter_number']][$row['verse_number']] = $row['code'];
		return $verses_codes;
	}
	else 
		die('Error in query:'.$query);
}

function insert_alignment($mysqli, $voice_code, $books_codes, $verses_codes, $voiceArray, $voiceInfo, $translationArray)
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
			else {
				$shift = 0;
				foreach ( $chapter['verses'] as $verse ) {
					$verses_codes_chapter = $verses_codes[$book_code][$chapter['id']];
					/*
					while ( !array_key_exists($verse['id'] + $shift, $verses_codes_chapter) )
					{
						// похоже объединенный стих, нужно сдвинуть
						$shift += 1;
						if ( $shift > 10 ) {
							die("Error: verse $verse[id] (".print_r($verse,1).") is not found in book $book[id] ($book[fullName]) / chapter $chapter[id]! verses_codes_chapter:(".print_r($verses_codes_chapter,1).")\n");
						}
					}
					*/

					$translation_verse = $verses_codes_chapter[$verse['id'] + $shift];
					$str .= "($voice_code, $translation_verse, $verse[begin], $verse[end], NULL),";
				}
			}
		}
	}
	$str = substr_replace($str, '', -1);
	$query = "
		INSERT INTO voice_alignments
		  (voice, translation_verse, begin, end, is_correct)
		VALUES
		  $str
	";
	//print $query;
	$mysqli->query($query);
	printf("Затронутые строки (INSERT/voice_alignments): %d\n", $mysqli->affected_rows);
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
	insert_alignment($mysqli, $voice_code, $books_codes, $verses_codes, $voiceArray, $voiceInfo, $translationArray);
}

///////////////////

$export_type = determine_export_type(1);
$translation = determine_text_translation(2);


$mysqli = get_db_cursor();

if ( $export_type == 'TEXT' )
	save_text_to_db($mysqli, $translation);

elseif ( $export_type == 'TIMECODES' )
{
	$voice = determine_voice_4bbl($translation, 3);
	save_timecodes_to_db($mysqli, $translation, $voice);
}
