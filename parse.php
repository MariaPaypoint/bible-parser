<?php

require 'include.php';

setlocale(LC_ALL, 'ru_RU.utf8');
libxml_use_internal_errors(true);
DEFINE('WRONG_TEXT', 'Если это кодекс, то возможно данный текст просто отсутсвует.');

function determine_translation() 
{
	global $argv;
	
	if ( !isset($argv[1]) )
		die("\nERROR: Set translation var! \nExample usage: \n$ php parse.php syn\n\n");
	
	$translation = $argv[1];
	
	$url = "https://bible.by/$translation/1/1/";
	$doc = new DOMDocument();
	$doc->loadHTMLFile($url);
	
	if ( strpos($doc->textContent, WRONG_TEXT) )
		die("Translation not found (example url: $url)\n\n");
	
	return $translation;
}

function get_chapter($doc, $book) 
{	
	$result = [];
	$id = 1; 
	
	while ( $element = $doc->getElementById($id) ) {
		
		$sub = $element->childNodes->item(0)->textContent;
		$text = '';
		for ( $counter = 1; $counter < $element->childNodes->length; $counter ++ )
		{
			$value = $element->childNodes->item($counter)->textContent;
			if ( (string)intval($value) !== $value )
				$text .= $value;
		}
		array_push($result, ['id'=>intval($sub), 'text'=>trim($text)]);
		
		$id++;
	}
	
	return $result;
}

function get_all_books($translation) 
{
	$doc = new DOMDocument();
	$bible = [];
	$bible['code'] = $translation;
	$bible = array_merge_recursive($bible, get_translation_info($translation));
	
	$bible['books'] = [];
	
	$book = 0;
	
	while ( True ) 
	{
		$book++;
		
		//if ( $book > 2 ) break; // отладка
		// if ( $book < 40 ) continue; // Только НЗ
		// if ( $book > 43 ) break; // Только Евангелия
		
		$doc->loadHTMLFile("https://bible.by/$translation/$book/1/");
		
		if ( strpos($doc->textContent, WRONG_TEXT) )
			break;
		print "Book $book. Chapters: ";
		
		$chapter = 0;
		$bookArray = [];
		$bookArray['id'] = $book;
		$book_info = get_book_info($book);
		$bookArray['code'] = $book_info['code'];
		$bookArray['shortName'] = $book_info['shortName'][$bible['lang']];
		$bookArray['fullName'] = $book_info['fullName'][$bible['lang']];
		
		$bookArray['chapters'] = [];
		
		while ( True ) 
		{
			$chapter++;
			
			//if ( $chapter > 2 ) break; // отладка
			
			$doc->loadHTMLFile("https://bible.by/$translation/$book/$chapter/");
			if ( strpos($doc->textContent, WRONG_TEXT) )
				break;
			
			print " $chapter";
			
			$chapterArray = ['id' => $chapter, 'verses' => get_chapter($doc, $book, $chapter)];
			
			array_push($bookArray['chapters'], $chapterArray);
			
		}
		
		array_push($bible['books'], $bookArray);
		
		// добавляем запись по книгам
		$filename = 'bible' .DIRECTORY_SEPARATOR . $translation . '_' . $book_info['code'] . '.json';
		file_put_contents($filename, json_encode($bible, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ));
		
		print " OK\n";
	}
	return $bible;
}

$translation = determine_translation();

print "\nStart $translation downloading\n\n";

$bible = get_all_books($translation);
$filename = 'bible' .DIRECTORY_SEPARATOR . $translation . '.json';
file_put_contents($filename, json_encode($bible, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ));

print "\nDone! File $filename saved.\n\n";

?>