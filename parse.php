<?php

$books_limit    = 999;
$chapters_limit = 999;

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

function get_note_text($doc, $note_number) 
{
	$link = $doc->getElementById("n$note_number");
	$fullText = $link->parentNode->textContent; # 26 [1] — В знач.: «человеческий род»; евр. ада́м.
	
	if ( preg_match('/^\d+ \[\d+\] — (.+)$/', $fullText, $matches, PREG_OFFSET_CAPTURE) )
		return $matches[1][0];
	else 
		return "Error in note searching";
}

function get_titles($doc)
{
	$titles = [];
	
	$parentNode = $doc->getElementById("1")->parentNode;
	
	for ( $counter = 1; $counter < $parentNode->childNodes->length; $counter ++ )
	{
		$titles_element = $parentNode->childNodes->item($counter);
		if ( !method_exists($titles_element, 'getAttribute') )
			continue;
		if ( $titles_element->getAttribute('class') == 'top-paragraph' )
		{
			for ( $counter_titles = 1; $counter_titles < $titles_element->childNodes->length; $counter_titles ++ )
			{
				$title_element = $titles_element->childNodes->item($counter_titles); // это может быть номер стиха, а может быть и текст заголовка
				
				if ( $counter_titles % 2 == 1 )
				{
					$sub = $title_element->childNodes->item(0)->textContent;
					continue;
				}
				
				$title_text = trim($title_element->textContent);
				$title_text = explode(';', $title_text)[0]; // баг именно в bible.by
				array_push($titles, [
					'before_verse_number' => intval($sub),
					'text'                => trim($title_text, " .;")
				]);
			}
			break;
		}
	}
	// print_r($titles);
	return $titles;
}

function parse_chapter($doc, $book, $chapter_id) 
{	
	$verses = [];
	$notes = [];
	
	$id = 1;
	while ( $element = $doc->getElementById($id) ) 
	{
		$sub = $element->childNodes->item(0)->textContent;
		$start_paragraph = $element->getAttribute('class') == 'paragraph' ? 1 : 0;
		$htmlText = '';
		$unformatedText = '';
		for ( $counter = 1; $counter < $element->childNodes->length; $counter ++ )
		{
			$textContent = $element->childNodes->item($counter)->textContent;
			$nodeName = $element->childNodes->item($counter)->nodeName;
			
			// разбираем форматирование
			if ( $nodeName == '#text' )      # обычный текст
			{
				$htmlText .= $textContent;
				$unformatedText .= $textContent;
			}
			elseif ( $nodeName == 'em' )     # курсив
			{
				$htmlText .= "<em>$textContent</em>";
				$unformatedText .= $textContent;
			}
			elseif ( $nodeName == 'br' )     # разрыв строки - пока не знаю что с ним делать
			{
				continue;
			}
			elseif ( $nodeName == 'e' )     # скорее всего слово, к которому примечание (см. стих 20 тут https://bible.by/nrt/13/15/)
			{
				continue;
			}
			elseif ( $nodeName == 'span' )   # сноска?
			{
				if ( preg_match('/^\[(\d+)\]$/', $textContent, $matches, PREG_OFFSET_CAPTURE) ) 
				{
					$note_number = $matches[1][0];
					array_push($notes, [
						'id'           => intval($note_number), 
						'text'         => trim(get_note_text($doc, $note_number)),
						'verse_number' => intval($sub),
						'position'     => mb_strlen(trim($unformatedText))
					]);
				}
			}
			else                             # что-то новенькое
			{
				print "NOT FOUND LOGIC!\n";
				print "chapter: [$chapter_id], verse_number: [$sub], nodeName: [$nodeName], text: [$textContent]\n";
				print_r($element->childNodes->item($counter));
				// print_r($notes);
				die();
			}
			
			// if ( (string)intval($textContent) !== $textContent )
				// $text .= $textContent;
		}
		array_push($verses, [
			'id'              => intval($sub), 
			'htmlText'        => trim($htmlText), 
			'unformatedText'  => trim($unformatedText),
			'start_paragraph' => $start_paragraph
		]);
		
		$id++;
	}
	return [
		'id'     => $chapter_id, 
		'verses' => $verses, 
		'notes'  => $notes, 
		'titles' => get_titles($doc)
	];
}

function get_all_books($translation) 
{
	global $books_limit;
	global $chapters_limit;
	
	$doc = new DOMDocument();
	$bible = [];
	$bible['code'] = $translation;
	$bible = array_merge_recursive($bible, get_translation_info($translation));
	
	$bible['books'] = [];
	
	$book = 0;
	
	while ( True ) 
	{
		$book++;
		
		if ( $book > $books_limit ) break; // отладка
		// if ( $book < 40 ) continue; // Только НЗ
		// if ( $book > 43 ) break; // Только Евангелия
		
		$doc->loadHTMLFile("https://bible.by/$translation/$book/1/");
		
		if ( strpos($doc->textContent, WRONG_TEXT) )
			break;
		print "Book $book. Chapters: ";
		
		$chapter_id = 0;
		$bookArray = [];
		$bookArray['id'] = $book;
		$book_info = get_book_info($book);
		$bookArray['code'] = $book_info['code'];
		$bookArray['shortName'] = $book_info['shortName'][$bible['lang']];
		$bookArray['fullName'] = $book_info['fullName'][$bible['lang']];
		
		$bookArray['chapters'] = [];
		
		while ( True ) 
		{
			$chapter_id++;
			
			if ( $chapter_id > $chapters_limit ) break; // отладка
			
			$doc->loadHTMLFile("https://bible.by/$translation/$book/$chapter_id/");
			if ( strpos($doc->textContent, WRONG_TEXT) )
				break;
			
			print " $chapter_id";
			
			$chapterArray = parse_chapter($doc, $book, $chapter_id);
			
			array_push($bookArray['chapters'], $chapterArray);
			
		}
		
		array_push($bible['books'], $bookArray);
		
		// добавляем запись по книгам
		// $filename = 'text' .DIRECTORY_SEPARATOR . $translation . '_' . $book_info['code'] . '.json';
		// file_put_contents($filename, json_encode($bible, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ));
		
		print " OK\n";
	}
	
	return $bible;
}

function prepare_environment() 
{
	create_dir777_if_not_exists('text');
}

function save_to_file($translation, $bible) 
{
	$filename = 'text' .DIRECTORY_SEPARATOR . $translation . '.json';
	file_put_contents($filename, json_encode($bible, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ));

	print "\nSuccess! File $filename saved.\n\n";
}

prepare_environment();

$translation = determine_translation();

print "\nStart $translation downloading\n\n";

$bible = get_all_books($translation);
save_to_file($translation, $bible);

?>