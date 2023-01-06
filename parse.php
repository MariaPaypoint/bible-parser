<?php

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
			$text .= $element->childNodes->item($counter)->textContent;
		
		array_push($result, ['id'=>intval($sub), 'text'=>trim($text)]);
		
		$id++;
	}
	
	return $result;
}

function get_translation_info($translation)
{
	switch ($translation)
	{
		case 'syn' : return ['lang'=>'ru', 'fullName'=>'Синодальный перевод'];
		case 'nrt' : return ['lang'=>'ru', 'fullName'=>'Новый русский перевод'];
	}
	
	return '';
}

function get_book_info($book_index)
{
	switch ($book_index)
	{
		case 1 : return ['code'=>'gen', 'shortName'=>'Быт'  , 'fullName'=>'Бытие'];
		case 2 : return ['code'=>'exo', 'shortName'=>'Исх'  , 'fullName'=>'Исход'];
		case 3 : return ['code'=>'lev', 'shortName'=>'Лев'  , 'fullName'=>'Левит'];
		case 4 : return ['code'=>'num', 'shortName'=>'Чис'  , 'fullName'=>'Числа'];
		case 5 : return ['code'=>'deu', 'shortName'=>'Втор' , 'fullName'=>'Второзаконие'];

		case 6 : return ['code'=>'jos', 'shortName'=>'Нав'  , 'fullName'=>'Иисус Навин'];
		case 7 : return ['code'=>'jdg', 'shortName'=>'Суд'  , 'fullName'=>'Судьи'];
		case 8 : return ['code'=>'rut', 'shortName'=>'Руфь' , 'fullName'=>'Руфь'];
		case 9 : return ['code'=>'1sa', 'shortName'=>'1Цар' , 'fullName'=>'1 Царств'];
		case 10: return ['code'=>'2sa', 'shortName'=>'2Цар' , 'fullName'=>'2 Царств'];
		case 11: return ['code'=>'1ki', 'shortName'=>'3Цар' , 'fullName'=>'3 Царств'];
		case 12: return ['code'=>'2ki', 'shortName'=>'4Цар' , 'fullName'=>'4 Царств'];
		case 13: return ['code'=>'1ch', 'shortName'=>'1Пар' , 'fullName'=>'1 Паралипоменон'];
		case 14: return ['code'=>'2ch', 'shortName'=>'2Пар' , 'fullName'=>'2 Паралипоменон'];
		case 15: return ['code'=>'ezr', 'shortName'=>'Езд'  , 'fullName'=>'Ездра'];
		case 16: return ['code'=>'neh', 'shortName'=>'Неем' , 'fullName'=>'Неемия'];
		case 17: return ['code'=>'est', 'shortName'=>'Есф'  , 'fullName'=>'Есфирь'];

		case 18: return ['code'=>'job', 'shortName'=>'Иов'  , 'fullName'=>'Иов'];
		case 19: return ['code'=>'psa', 'shortName'=>'Пс'   , 'fullName'=>'Псалтирь'];
		case 20: return ['code'=>'pro', 'shortName'=>'Прит' , 'fullName'=>'Притчи'];
		case 21: return ['code'=>'ecc', 'shortName'=>'Еккл' , 'fullName'=>'Екклесиаст'];
		case 22: return ['code'=>'sng', 'shortName'=>'Песн' , 'fullName'=>'Песни Песней'];

		case 23: return ['code'=>'isa', 'shortName'=>'Ис'   , 'fullName'=>'Исаия'];
		case 24: return ['code'=>'jer', 'shortName'=>'Иер'  , 'fullName'=>'Иеремия'];
		case 25: return ['code'=>'lam', 'shortName'=>'Плач' , 'fullName'=>'Плач Иеремии'];
		case 26: return ['code'=>'ezk', 'shortName'=>'Иез'  , 'fullName'=>'Иезекииль'];
		case 27: return ['code'=>'dan', 'shortName'=>'Дан'  , 'fullName'=>'Даниил'];

		case 28: return ['code'=>'hos', 'shortName'=>'Ос'   , 'fullName'=>'Осия'];
		case 29: return ['code'=>'jol', 'shortName'=>'Иоиль', 'fullName'=>'Иоиль'];
		case 30: return ['code'=>'amo', 'shortName'=>'Амос' , 'fullName'=>'Амос'];
		case 31: return ['code'=>'oba', 'shortName'=>'Авд'  , 'fullName'=>'Авдий'];
		case 32: return ['code'=>'jon', 'shortName'=>'Иона' , 'fullName'=>'Иона'];
		case 33: return ['code'=>'mic', 'shortName'=>'Мих'  , 'fullName'=>'Михей'];
		case 34: return ['code'=>'nam', 'shortName'=>'Наум' , 'fullName'=>'Наум'];
		case 35: return ['code'=>'hab', 'shortName'=>'Авв'  , 'fullName'=>'Аввакум'];
		case 36: return ['code'=>'zep', 'shortName'=>'Соф'  , 'fullName'=>'Софония'];
		case 37: return ['code'=>'hag', 'shortName'=>'Агг'  , 'fullName'=>'Аггей'];
		case 38: return ['code'=>'zec', 'shortName'=>'Зах'  , 'fullName'=>'Захария'];
		case 39: return ['code'=>'mal', 'shortName'=>'Мал'  , 'fullName'=>'Малахия'];

		case 40: return ['code'=>'mat', 'shortName'=>'Мф'   , 'fullName'=>'Евангелие от Матфея'];
		case 41: return ['code'=>'mrk', 'shortName'=>'Мк'   , 'fullName'=>'Евангелие от Марка'];
		case 42: return ['code'=>'luk', 'shortName'=>'Лк'   , 'fullName'=>'Евангелие от Луки'];
		case 43: return ['code'=>'jhn', 'shortName'=>'Ин'   , 'fullName'=>'Евангелие от Иоанна'];
		case 44: return ['code'=>'act', 'shortName'=>'Деян' , 'fullName'=>'Деяния апостолов'];

		case 45: return ['code'=>'jas', 'shortName'=>'Иак'  , 'fullName'=>'Иакова'];
		case 46: return ['code'=>'1pe', 'shortName'=>'1Пет' , 'fullName'=>'1 Петра'];
		case 47: return ['code'=>'2pe', 'shortName'=>'2Пет' , 'fullName'=>'2 Петра'];
		case 48: return ['code'=>'1jn', 'shortName'=>'1Ин'  , 'fullName'=>'1 Иоанна'];
		case 49: return ['code'=>'2jn', 'shortName'=>'2Ин'  , 'fullName'=>'2 Иоанна'];
		case 50: return ['code'=>'3jn', 'shortName'=>'3Ин'  , 'fullName'=>'3 Иоанна'];
		case 51: return ['code'=>'jud', 'shortName'=>'Иуд'  , 'fullName'=>'Иуды'];

		case 52: return ['code'=>'rom', 'shortName'=>'Рим'  , 'fullName'=>'Римлянам'];
		case 53: return ['code'=>'1co', 'shortName'=>'1Кор' , 'fullName'=>'1 Коринфянам'];
		case 54: return ['code'=>'2co', 'shortName'=>'2Кор' , 'fullName'=>'2 Коринфянам'];
		case 55: return ['code'=>'gal', 'shortName'=>'Гал'  , 'fullName'=>'Галатам'];
		case 56: return ['code'=>'eph', 'shortName'=>'Еф'   , 'fullName'=>'Ефесянам'];
		case 57: return ['code'=>'php', 'shortName'=>'Фил'  , 'fullName'=>'Филиппийцам'];
		case 58: return ['code'=>'col', 'shortName'=>'Кол'  , 'fullName'=>'Колоссянам'];
		case 59: return ['code'=>'1th', 'shortName'=>'1Фес' , 'fullName'=>'1 Фессалоникийцам'];
		case 60: return ['code'=>'2th', 'shortName'=>'2Фес' , 'fullName'=>'2 Фессалоникийцам'];
		case 61: return ['code'=>'1ti', 'shortName'=>'1Тим' , 'fullName'=>'1 Тимофею'];
		case 62: return ['code'=>'2ti', 'shortName'=>'2Тим' , 'fullName'=>'2 Тимофею'];
		case 63: return ['code'=>'tit', 'shortName'=>'Тит'  , 'fullName'=>'Титу'];
		case 64: return ['code'=>'phm', 'shortName'=>'Флм'  , 'fullName'=>'Филимону'];
		case 65: return ['code'=>'heb', 'shortName'=>'Евр'  , 'fullName'=>'Евреям'];

		case 66: return ['code'=>'rev', 'shortName'=>'Откр' , 'fullName'=>'Откровение'];
	}
	
	return 'unknown';
}

function get_all_books($translation) 
{
	$doc = new DOMDocument();
	$bible = get_translation_info($translation);
	$bible['code'] = $translation;
	$bible['books'] = [];
	
	$book = 0;
	
	while ( True ) 
	{
		$book++;
		
		// if ( $book < 40 ) continue; // Только НЗ
		// if ( $book > 43 ) break; // Только Евангелия
		
		$doc->loadHTMLFile("https://bible.by/syn/$book/1/");
		
		if ( strpos($doc->textContent, WRONG_TEXT) )
			break;
		print "Book $book. Chapters: ";
		
		$chapter = 0;
		$bookArray = get_book_info($book);
		$bookArray['id'] = $book;
		$bookArray['chapters'] = [];
		
		while ( True ) 
		{
			$chapter++;
			
			// if ( $chapter > 2 ) break;
			
			$doc->loadHTMLFile("https://bible.by/syn/$book/$chapter/");
			if ( strpos($doc->textContent, WRONG_TEXT) )
				break;
			
			print " $chapter";
			
			$chapterArray = ['id' => $chapter, 'verses' => get_chapter($doc, $book, $chapter)];
			
			array_push($bookArray['chapters'], $chapterArray);
		}
		
		array_push($bible['books'], $bookArray);
		
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