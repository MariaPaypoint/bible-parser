<?php

$books_limit    = 999;
$chapters_limit = 999;

$only_book = false;
$only_chapter = false;

require 'include.php';

setlocale(LC_ALL, 'ru_RU.utf8');
libxml_use_internal_errors(true);
DEFINE('WRONG_TEXT', 'READER SETTINGSThis chapter is not available in this version. Please choose a different chapter or version.');

function get_url($translation, $book, $chapter)
{
	// тут все капсом
	$TRANSLATION = strtoupper($translation);
	$BOOK = strtoupper($book);

	switch ( $translation ) {
		case 'bti': $translation_code = '313'; break;
		case 'syn': $translation_code = '400'; break;
		default: die("Incorrect translation ($translation)");
	}

	
	
	$url = "https://www.bible.com/bible/$translation_code/$BOOK.$chapter.$TRANSLATION";
	return $url;
}

function determine_translation() 
{
	global $argv;
	
	if ( !isset($argv[1]) )
		die("\nERROR: Set translation var! \nExample usage: \n$ php parse.php syn\n\n");
	
	$translation = $argv[1];

	$url = get_url($translation, 'MAT', 1); 
	$doc = new DOMDocument();
	$doc->loadHTMLFile($url);
	
	if ( strpos($doc->textContent, WRONG_TEXT) )
		die("Translation not found (example url: $url)\n\n" . $doc->textContent . "\n\n");
	
	return $translation;
}

function get_note_text($doc, $note_number) 
{
	$element = $doc->getElementById("n$note_number");
	if ( $element ) {
		$fullText = $element->parentNode->textContent; 
		
		# 26 [1] — В знач.: «человеческий род»; евр. ада́м.
		if ( preg_match('/^\d+ \[\d+\] — (.+)$/', $fullText, $matches, PREG_OFFSET_CAPTURE) )
			return $matches[1][0];
		else 
			die("Error in note searching");
	}
	else {
		$element = $doc->getElementById("note_main")->childNodes->item(7);
		$nodeName = $element->nodeName;
		
		for ( $counter = 0; $counter < $element->childNodes->length; $counter ++ ) {
			$fullText = $element->childNodes->item($counter)->textContent; 
			
			# 1  [20] — Букв.: (римскую) милю — мера длины, около 1,5 км.
			if ( preg_match('/^\d+  \['.$note_number.'\] — (.+)$/', $fullText, $matches, PREG_OFFSET_CAPTURE) ) {
				//print $fullText . "\n";
				return $matches[1][0];
			}
		}
		die();
	}
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

function get_text_from_children($element, $chapter_id, $doc, $sub=0) 
{
	$start_paragraph = $element->getAttribute('class') == 'paragraph' ? 1 : 0;
	$htmlText = '';
	$unformatedText = '';
	$notes = [];
	$join = 0;

	for ( $counter = 0; $counter < $element->childNodes->length; $counter ++ )
	{
		$item = $element->childNodes->item($counter);
		$textContent = $item->textContent;
		$nodeName = $item->nodeName;

		if ( $nodeName == '#text' )      # обычный текст
		{
			$htmlText .= $textContent;
			$unformatedText .= $textContent;
			continue;
		}

		$className = $item->getAttribute('class');
		//print "[SET_$className]";

		// разбираем форматирование
		
		if ( $nodeName == 'em' )     # курсив
		{
			$htmlText .= "<em>$textContent</em>";
			$unformatedText .= $textContent;
		}
		elseif ( $nodeName == 'br' )     # разрыв строки - пока не знаю что с ним делать
		{
			$htmlText .= "<br>";
		}
		
		elseif ( $nodeName == 'e' )     # скорее всего слово, к которому примечание (см. стих 20 тут https://bible.by/nrt/13/15/)
		{
			continue;
		}
		elseif ( $nodeName == 'sup' )     # скорее всего слово, к которому примечание (см. стих 20 тут https://bible.by/nrt/13/15/)
		{
			$sub = $textContent;
		}
		elseif ( $nodeName == 'span' and $className == 'sub' )   # сноска?
		{
			//print $textContent;
			preg_match('/^\[(\d+)\]$/', $textContent, $matches, PREG_OFFSET_CAPTURE);
			$note_number = $matches[1][0];
			$n = [
				'id'           => intval($note_number), 
				'text'         => trim(get_note_text($doc, $note_number)),
				'verse_number' => intval($sub),
				'position'     => mb_strlen(trim($unformatedText))
			];
			//print_r($n);
			array_push($notes, $n);
		}
		elseif ( $nodeName == 'p' or $nodeName == 'span' )     # Например, bti 1/1/27 - тоже не понятно пока
		{
			//print "GOTO";
			list($v, $n) = get_text_from_children($item, $chapter_id, $doc, $sub);
			$notes = array_merge($notes, $n);
			if ( $join < $v['join'] )
				$join = $v['join'];
			
			if ( !$className or $className == 'paragraph' )
				$htmlText .= $v['htmlText'];
			elseif ( $className == 'jesus' or $className == 'quote' or $className == 'e' or $className == 'gray' )
				$htmlText .= "<span class='$className'>" . $v['htmlText'] . "</span>";
			elseif ( $className == 'note' ) { # объединение стихов [20-21]
				if ( preg_match('/^\[\d+-\d+\]$/', $v['htmlText']) ) {
					list($first, $second) = explode('-', trim($v['htmlText'], '[]'));
					$join = (int)$second - (int)$first;
					// print " join:$v[htmlText]:[$join]";
					continue;
				}
				elseif ( preg_match('/^\[\d+\]$/', $v['htmlText']) ) {
					$value = trim($v['htmlText'], '[]');
					print " NEED_MANUAL_FIX[$value]";
					continue;
				}
				else
					die("Некорректный формат строки. Ожидаемый формат: [число-число], а оно: $v[htmlText]\n");
			}
			else 
				die("Unknown span className: $className, value: $v[htmlText]\n");

			//$unformatedText .= $textContent;
			$unformatedText .= $v['unformatedText'];
			//if ( $chapter_id == 5 ) {
			//	print_r($v);
			//	die();
			//}
		}
		else                             # что-то новенькое
		{
			print "NOT FOUND LOGIC!\n";
			print "chapter: [$chapter_id], verse_number: [$sub], nodeName: [$nodeName], text: [$textContent]\n";
			print_r($item);
			die();
		}
		
		// if ( (string)intval($textContent) !== $textContent )
			// $text .= $textContent;
	}

	return [
		[
			'id'              => intval($sub), 
			'htmlText'        => trim($htmlText), 
			'unformatedText'  => trim($unformatedText),
			'start_paragraph' => $start_paragraph,
			'join'            => $join
		],
		$notes
	];
}


// Функция для обработки дочерних узлов
function processChildNodes($node, &$htmlText, &$unformattedText, &$result, $verseId, $xpath, $doc, $insideQuote, &$prevChar) {
	foreach ($node->childNodes as $childNode) {
		processVerseNode($childNode, $htmlText, $unformattedText, $result, $verseId, $xpath, $doc, $insideQuote, $prevChar);
	}
}

// Функция для определения, находится ли узел внутри цитаты
function isNodeInQuote($node) {
	while ($node && $node->nodeName !== 'body') {
		if ($node->nodeName === 'div' && 
			(strpos($node->getAttribute('class'), 'ChapterContent_q1__') === 0 or strpos($node->getAttribute('class'), 'ChapterContent_q2__') === 0)
		) {
			return true;
		}
		$node = $node->parentNode;
	}
	return false;
}

// Функция для проверки, является ли символ буквой или цифрой
function isLetterOrDigit($char) {
	return preg_match('/\p{L}|\p{N}|,|;|\.|—/u', $char);
}

// Рекурсивная функция для обработки узлов стиха и примечаний
function processVerseNode($node, &$htmlText, &$unformattedText, &$result, $verseId, $xpath, $doc, $insideQuote = false, &$prevChar = '') {
	if ($node->nodeName === 'span' && strpos($node->getAttribute('class'), 'ChapterContent_label__') !== false) {
		// Пропускаем номер стиха
		return;
	} elseif ($node->nodeName === 'span' && strpos($node->getAttribute('class'), 'ChapterContent_note__') !== false) {
		// Примечание
		$noteId = count($result['notes']) + 1;
		$noteTextNode = $xpath->query(".//span[contains(@class, 'ChapterContent_body__')]", $node)->item(0);
		if ($noteTextNode) {
			$noteText = $noteTextNode->textContent;
			$position = mb_strlen($unformattedText);

			$result['notes'][] = [
				'id' => $noteId,
				'text' => $noteText,
				'verse_number' => $verseId,
				'position' => $position
			];
		}
		// Не добавляем примечание в текст
	} else {
		$isQuote = isNodeInQuote($node);
		$currentInsideQuote = $insideQuote || $isQuote;

		if ($node->nodeName === 'span' && strpos($node->getAttribute('class'), 'ChapterContent_add__') !== false) {
			// Обрабатываем курсив
			$emContent = '';
			$emUnformatted = '';
			processChildNodes($node, $emContent, $emUnformatted, $result, $verseId, $xpath, $doc, $currentInsideQuote, $prevChar);

			$emHtml = '<em>' . $emContent . '</em>';

			if ($isQuote && !$insideQuote && trim($emHtml)) {
				$emHtml = '<span class="quote">' . $emHtml . '</span>';
			}

			$htmlText .= $emHtml;
			$unformattedText .= $emUnformatted;
			$prevChar = mb_substr($unformattedText, -1);
		} else {
			if ($node->hasChildNodes()) {
				$childHtml = '';
				$childUnformatted = '';
				processChildNodes($node, $childHtml, $childUnformatted, $result, $verseId, $xpath, $doc, $currentInsideQuote, $prevChar);

				if ($isQuote && !$insideQuote && trim($childHtml)) {
					$childHtml = '<span class="quote">' . $childHtml . '</span>';
				}

				$htmlText .= $childHtml;
				$unformattedText .= $childUnformatted;
			} else {
				if ($node->nodeType === XML_TEXT_NODE) {
					$text = $node->textContent;
					$htmlEncodedText = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

					// Проверяем, нужно ли добавить пробел
					$needsSpace = false;
					if ($prevChar !== '' && isLetterOrDigit($prevChar)) {
						$firstChar = mb_substr($text, 0, 1);
						if (isLetterOrDigit($firstChar)) {
							$needsSpace = true;
						}
					}

					if ($needsSpace) {
						$htmlText .= ' ';
						$unformattedText .= ' ';
					}

					if ($isQuote && !$insideQuote && trim($htmlEncodedText)) {
						$htmlEncodedText = '<span class="quote">' . $htmlEncodedText . '</span>';
					}

					$htmlText .= $htmlEncodedText;
					$unformattedText .= $text;
					$prevChar = mb_substr($text, -1);
				} else {
					// Обрабатываем разрешенные теги
					$allowedTags = ['em', 'strong', 'sup', 'sub', 'u', 'i', 'b', 'br'];
					if (in_array($node->nodeName, $allowedTags)) {
						$nodeHtml = $doc->saveHTML($node);
						$textContent = $node->textContent;

						// Проверяем, нужно ли добавить пробел
						$needsSpace = false;
						if ($prevChar !== '' && isLetterOrDigit($prevChar)) {
							$firstChar = mb_substr($textContent, 0, 1);
							if (isLetterOrDigit($firstChar)) {
								$needsSpace = true;
							}
						}

						if ($needsSpace) {
							$htmlText .= ' ';
							$unformattedText .= ' ';
						}

						if ($isQuote && !$insideQuote && trim($nodeHtml)) {
							$nodeHtml = '<span class="quote">' . $nodeHtml . '</span>';
						}

						$htmlText .= $nodeHtml;
						$unformattedText .= $textContent;
						$prevChar = mb_substr($textContent, -1);
					} else {
						// Добавляем текстовое содержимое
						$text = $node->textContent;
						$htmlEncodedText = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

						// Проверяем, нужно ли добавить пробел
						$needsSpace = false;
						if ($prevChar !== '' && isLetterOrDigit($prevChar)) {
							$firstChar = mb_substr($text, 0, 1);
							if (isLetterOrDigit($firstChar)) {
								$needsSpace = true;
							}
						}

						if ($needsSpace) {
							$htmlText .= ' ';
							$unformattedText .= ' ';
						}

						if ($isQuote && !$insideQuote && trim($htmlEncodedText)) {
							$htmlEncodedText = '<span class="quote">' . $htmlEncodedText . '</span>';
						}

						$htmlText .= $htmlEncodedText;
						$unformattedText .= $text;
						$prevChar = mb_substr($text, -1);
					}
				}
			}
		}
	}
}

// Функция для обработки блоков (абзацы и цитаты)
function processBlock($blockNode, &$result, $xpath, $doc, &$processedVerses) {
	// Определяем, является ли блок цитатой
	$class = $blockNode->getAttribute('class');
	$isQuote = strpos($class, 'ChapterContent_q1__') === 0;

	$verseNodes = $xpath->query(".//span[contains(@class, 'ChapterContent_verse__') and @data-usfm]", $blockNode);

	// Отслеживаем, какие стихи уже обработаны внутри этого блока
	$versesInBlock = [];
	foreach ($verseNodes as $verseNode) {
		// Получаем номер стиха из data-usfm
		$usfm = $verseNode->getAttribute('data-usfm');
		$parts = explode('.', $usfm);
		$verseId = intval(end($parts));

		// Проверяем, не обработали ли мы уже этот стих
		if (in_array($verseId, $processedVerses)) {
			continue;
		}
		$processedVerses[] = $verseId;
		$versesInBlock[] = $verseId;

		// Собираем все узлы, относящиеся к этому стиху по всему документу
		$nodesForVerse = $xpath->query("//*[contains(@class, 'ChapterContent_verse__') and @data-usfm='$usfm']");

		// Инициализируем переменные
		$htmlText = '';
		$unformattedText = '';
		$prevChar = '';

		foreach ($nodesForVerse as $node) {
			processVerseNode($node, $htmlText, $unformattedText, $result, $verseId, $xpath, $doc, false, $prevChar);
		}

		// Нормализуем пробелы
		$htmlText = normalizeSpaces($htmlText);
		$unformattedText = normalizeSpaces($unformattedText);

		// Устанавливаем start_paragraph
		$startParagraph = ($verseId === $versesInBlock[0]) ? 1 : 0;

		$result['verses'][] = [
			'id' => $verseId,
			'htmlText' => $htmlText,
			'unformatedText' => $unformattedText,
			'start_paragraph' => $startParagraph,
			'join' => 0 // Если не требуется объединение со следующим стихом
		];
	}
}

// Функция для нормализации пробелов
function normalizeSpaces($text) {
	// Заменяем множественные пробелы на один
	$text = preg_replace('/\s+/u', ' ', $text);
	// Убираем пробелы перед знаками пунктуации
	$text = preg_replace('/\s+([,.!?»“”])/u', '$1', $text);
	// Убираем пробелы после открывающих кавычек
	$text = preg_replace('/([«“„])\s+/u', '$1', $text);
	// Убираем пробелы перед закрывающими кавычками
	$text = preg_replace('/\s+([»”])/u', '$1', $text);
	// Триммируем текст
	$text = trim($text);
	return $text;
}

function parse_chapter($doc, $book, $chapter_id) 
{	
	$verses = [];
	$notes = [];

	// Создаем объект XPath для навигации по DOM
	$xpath = new DOMXPath($doc);

	// Массив для хранения результатов
	$result = [
		'id' => $chapter_id, // Номер главы
		'verses' => [],
		'notes' => [],
		'titles' => []
	];

	// Парсим заголовки
	$headingNodes = $xpath->query("//span[contains(@class, 'ChapterContent_heading__')]");
	foreach ($headingNodes as $headingNode) {
		$titleText = trim($headingNode->textContent);

		// Инициализируем beforeVerseNumber для каждого заголовка
		$beforeVerseNumber = null;

		// Ищем следующий элемент с номером стиха
		$nextNode = $headingNode->parentNode->nextSibling;
		while ($nextNode) {
			if ($nextNode->nodeType !== XML_ELEMENT_NODE) {
				$nextNode = $nextNode->nextSibling;
				continue;
			}

			// Ищем внутри $nextNode элемент с классом 'ChapterContent_verse__' и 'ChapterContent_label__'
			$verseSpans = $xpath->query(".//span[contains(@class, 'ChapterContent_verse__')]", $nextNode);
			foreach ($verseSpans as $verseSpan) {
				$labelSpan = $xpath->query(".//span[contains(@class, 'ChapterContent_label__')]", $verseSpan)->item(0);
				if ($labelSpan) {
					$labelText = trim($labelSpan->textContent);
					if (preg_match('/^\d+$/', $labelText)) {
						$verseNumber = intval($labelText);
						$beforeVerseNumber = $verseNumber;
						break 2; // Выходим из обоих циклов
					}
				}
			}

			$nextNode = $nextNode->nextSibling;
		}

		$result['titles'][] = [
			'before_verse_number' => $beforeVerseNumber ?? 1,
			'text' => $titleText
		];
	}

	// Массив для отслеживания уже обработанных стихов
	$processedVerses = [];

	// Парсим блоки (абзацы и цитаты)
	$blockNodes = $xpath->query("//div[starts-with(@class, 'ChapterContent_p__') or starts-with(@class, 'ChapterContent_q1__') or starts-with(@class, 'ChapterContent_d__')]");

	foreach ($blockNodes as $blockNode) {
		processBlock($blockNode, $result, $xpath, $doc, $processedVerses);
	}

	// Выводим результат в формате JSON
	//echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

	return $result;
}

function get_all_books($translation) 
{
	global $books_limit, $chapters_limit;
	global $only_book, $only_chapter;
	
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
		if ( $only_book!==false && $book<$only_book ) continue;
		if ( $only_book!==false && $book>$only_book ) break;
		
		$book_info = get_book_info($book);
		if ( $book_info === false ) break;

		print "Book $book. Chapters: ";
		
		$chapter_id = 0;
		$bookArray = [];
		$bookArray['id'] = $book;
		$bookArray['code'] = $book_info['code'];
		$bookArray['shortName'] = $book_info['shortName'][$bible['lang']];
		$bookArray['fullName'] = $book_info['fullName'][$bible['lang']];
		
		$bookArray['chapters'] = [];
		
		while ( True ) 
		{
			$chapter_id++;

			if ( $only_chapter!==false && $book<$only_chapter ) continue;
			if ( $only_chapter!==false && $book>$only_chapter ) break;
			
			if ( $chapter_id > $chapters_limit ) break; // отладка
			
			$url = get_url($translation, $book_info['code'], $chapter_id);
			$doc->loadHTMLFile($url);
			if ( strpos($doc->textContent, WRONG_TEXT) )
				break;
			
			print " $chapter_id";
			
			$chapterArray = parse_chapter($doc, $book, $chapter_id);

			//$chapterArray = manual_fix($translation, $book, $chapter_id, $chapterArray);
			
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

function manual_fix($translation, $book, $chapter_id, $chapterArray)
{
	if ( $translation == 'bti' )
	{
		if ( $book == 2 and $chapter_id == 16 ) # исход 16
		{
			$newVerses = [];
			foreach ( $chapterArray['verses'] as $v )
			{
				if ( $v['id'] == 34 )
				{
					$v['htmlText'] = '(Омер — это десятая часть эфы.) Аарон сделал всё, как ГОСПОДЬ повелел Моисею: он поставил сосуд перед ковчегом со скрижалями Закона. Там он и хранился. И ели сыны Израилевы манну все сорок лет, пока не пришли в землю, где смогли поселиться. Питались они этой манной до тех пор, пока не достигли Ханаана.';
					$v['unformatedText'] = $v['htmlText'];
					$v['join'] = 2;
				}
				elseif ( $v['id'] == 35 or $v['id'] == 36 )
				{
					// $v['htmlText'] = "<span class='empty'></span>";
					$v['htmlText'] = $v['unformatedText'] = '';
				}
				array_push($newVerses, $v);
			}
			$chapterArray['verses'] = $newVerses;

			$newNotes = [];
			foreach ( $chapterArray['notes'] as $n )
			{
				if ( $n['verse_number'] == 36 )
				{
					$n['verse_number'] = 34;
				}
				array_push($newNotes, $n);
			}
			$chapterArray['notes'] = $newNotes;
		}
		if ( $book == 52 and $chapter_id == 3 ) # Рим 3
		{
			$newVerses = [];
			foreach ( $chapterArray['verses'] as $v )
			{
				if ( $v['id'] == 25 )
				{
					$v['join'] = 0;
				}
				array_push($newVerses, $v);
			}
			$chapterArray['verses'] = $newVerses;
		}
	}
	return $chapterArray;
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