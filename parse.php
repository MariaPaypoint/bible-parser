<?php

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
		die("Translation not found (example url: $url)");
	
	return $translation;
}

function get_chapter($doc, $book) {
	
	$result = Array();
	$id = 1; 
	
	while ( $element = $doc->getElementById($id) ) {
		
		$sub = $element->childNodes->item(0)->textContent;
		$text = '';
		for ( $counter = 1; $counter < $element->childNodes->length; $counter ++ )
			$text .= $element->childNodes->item($counter)->textContent;
		
		$result[$sub] = $text;
		
		$id++;
	}
	
	return $result;
}

function get_all_books($translation) 
{
	$doc = new DOMDocument();
	$bible = Array();
	$book = 1;

	while ( True ) {
		$doc->loadHTMLFile("https://bible.by/syn/$book/1/");
		
		if ( strpos($doc->textContent, WRONG_TEXT) )
			break;
		print "Book $book. Chapters: ";
		
		$chapter = 1;
		while ( True ) {
			$doc->loadHTMLFile("https://bible.by/syn/$book/$chapter/");
			
			if ( strpos($doc->textContent, WRONG_TEXT) )
				break;
			print " $chapter";
			
			$bible[$book][$chapter] = get_chapter($doc, $book, $chapter);
			
			$chapter++;
		}
		print " OK\n";
		
		$book++;
		//break;
	}
	return $bible;
}

setlocale(LC_ALL, 'ru_RU.utf8');
libxml_use_internal_errors(true);
DEFINE('WRONG_TEXT', 'Если это кодекс, то возможно данный текст просто отсутсвует.');

$translation = determine_translation();

print "\nStart $translation downloading\n\n";

$bible = get_all_books($translation);
$filename = 'bible' .DIRECTORY_SEPARATOR . $translation . '.json';
file_put_contents($filename, json_encode($bible, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ));

print "\nDone! File $filename saved.\n\n";

?>