<?php

require 'include.php';

function clear_translation_in_db($mysqli, $bible_translation_code) 
{
	// очистка примечаний
	$mysqli->query("
		DELETE FROM bible_notes
		WHERE bible_verse IN (SELECT code FROM bible_verses WHERE bible_book IN (SELECT code FROM bible_books WHERE bible_translation = '$bible_translation_code'))
	");
	// printf("Затронутые строки (DELETE/bible_notes): %d\n", $mysqli->affected_rows);
	
	// очистка стихов
	$mysqli->query("
		DELETE FROM bible_verses
		WHERE bible_book IN (SELECT code FROM bible_books WHERE bible_translation = '$bible_translation_code')
	");
	// printf("Затронутые строки (DELETE/bible_verses): %d\n", $mysqli->affected_rows);
	
	// очистка книг
	$mysqli->query("
		DELETE FROM bible_books
		WHERE bible_translation = '$bible_translation_code'
	");
	// printf("Затронутые строки (DELETE/bible_books): %d\n", $mysqli->affected_rows);
	
	// еще?
}

function chapter_verses_to_db($mysqli, $book_code, $chapter) 
{
	$verses_str = '';
	foreach ( $chapter['verses'] as $verse ) {
		$start_paragraph = 0; // !!!!!!!!!!!!!! дописать логику
		
		$verses_str .= sprintf(
			"($verse[id], $chapter[id], $book_code, '%s', $start_paragraph),",
			$mysqli->real_escape_string($verse['text'])
		);
	}
	$verses_str = substr_replace($verses_str, '', -1);
	$mysqli->query("
		INSERT INTO bible_verses
		  (verse_number, chapter_number, bible_book, text, start_paragraph)
		VALUES 
		  $verses_str
	");
	$chapter_code = $mysqli->insert_id;
	// printf("Затронутые строки (INSERT/bible_verses): %d\n", $mysqli->affected_rows);
}
function get_verse_id($mysqli, $verse_number, $chapter_number, $book_code) 
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

function chapter_notes_to_db($mysqli, $book_code, $chapter) 
{
	$notes_str = '';
	foreach ( $chapter['notes'] as $note ) {
		$verse_id = get_verse_id($mysqli, $note['verse_number'], $chapter['id'], $book_code);
		$notes_str .= sprintf(
			"($verse_id, $note[position], $note[id], '%s'),",
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
		$chapter_code = $mysqli->insert_id;
		// printf("Затронутые строки (INSERT/bible_notes): %d\n", $mysqli->affected_rows);
	}
}

function book_to_db($mysqli, $bible_translation_code, $book) 
{
	// добавление
	$mysqli->query("
		INSERT INTO bible_books
		SET 
		  book_number       = '$book[id]',
		  bible_translation = '$bible_translation_code',
		  name              = '$book[fullName]'
	");
	$book_code = $mysqli->insert_id;
	// printf("Затронутые строки (INSERT/bible_books): %d\n", $mysqli->affected_rows);
	
	foreach ( $book['chapters'] as $chapter ) {
		// print_r($chapter);
		chapter_verses_to_db($mysqli, $book_code, $chapter);
		chapter_notes_to_db($mysqli, $book_code, $chapter);
	}
	
	print "Book $book[id] inserted to db with code $book_code\n";
}

function translation_to_db($mysqli, $translation, $translationArray)
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
		$bible_translation_code = $obj->code;
		printf("Translation already exists, code: %d\n", $bible_translation_code);
		
		// добавление
		$mysqli->query("
			UPDATE bible_translations
			SET $new_fields_str
			WHERE code = $bible_translation_code
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
		$bible_translation_code = $mysqli->insert_id;
		// printf("Затронутые строки (INSERT/bible_translations): %d\n", $mysqli->affected_rows);
	}
	
	print "Translation $translation is in db with code $bible_translation_code\n";
	
	return $bible_translation_code;
}

function save_to_db($mysqli, $translation, $translationArray) 
{	
	$bible_translation_code = translation_to_db($mysqli, $translation, $translationArray);
	clear_translation_in_db($mysqli, $bible_translation_code);
	
	foreach ( $translationArray['books'] as $book ) {
		book_to_db($mysqli, $bible_translation_code, $book);
	}
}

$translation = determine_audio_translation();
$translationArray = get_translation_array($translation);

$mysqli = new mysqli("127.0.0.1", "root", "XSNx0evIpDBXUPSthhHq", "bible_pause", "3307");

save_to_db($mysqli, $translation, $translationArray);

?>