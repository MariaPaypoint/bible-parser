<?php

$only_book = false; // false
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
	return preg_match('/\p{L}|\p{N}/u', $char);
}
function isLetterOrDigitOrPunctuation($char) {
	return preg_match('/\p{L}|\p{N}|,|;|:|\.|—/u', $char);
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
					if ($prevChar !== '' && isLetterOrDigitOrPunctuation($prevChar)) {
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
						if ($prevChar !== '' && isLetterOrDigitOrPunctuation($prevChar)) {
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
						if ($prevChar !== '' && isLetterOrDigitOrPunctuation($prevChar)) {
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

function processVerses($verseNodes, &$result, $xpath, $doc, &$processedVerses, $startParagraph = 1) {
    foreach ($verseNodes as $index => $verseNode) {
        // Получаем номер(а) стиха(ов) из data-usfm
        $usfm = $verseNode->getAttribute('data-usfm');

        // Разбиваем usfm по '+', если есть объединённые стихи
        $usfmParts = explode('+', $usfm);

        // Извлекаем номера стихов
        $verseIds = [];
        foreach ($usfmParts as $usfmPart) {
            $parts = explode('.', $usfmPart);
            $verseIds[] = intval(end($parts));
        }

        // Основной verseId — первый в списке
        $verseId = $verseIds[0];

        // Вычисляем значение для поля 'join'
        $join = count($verseIds) - 1;

        // Проверяем, не были ли уже обработаны эти стихи
        $alreadyProcessed = false;
        foreach ($verseIds as $vid) {
            if (in_array($vid, $processedVerses)) {
                $alreadyProcessed = true;
                break;
            }
        }
        if ($alreadyProcessed) {
            continue;
        }

        // Помечаем все стихи как обработанные
        $processedVerses = array_merge($processedVerses, $verseIds);

        // Собираем все узлы, относящиеся к этому стиху (или объединённым стихам)
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
        $startParagraphValue = ($index === 0) ? $startParagraph : 0;

        $result['verses'][] = [
            'id' => $verseId,
            'htmlText' => $htmlText,
            'unformatedText' => $unformattedText,
            'start_paragraph' => $startParagraphValue,
            'join' => $join
        ];
    }
}


// Функция для обработки блоков (абзацы и цитаты)
function processBlock($blockNode, &$result, $xpath, $doc, &$processedVerses) {
	// Определяем, является ли блок цитатой
	$class = $blockNode->getAttribute('class');
	$isQuote = strpos($class, 'ChapterContent_q1__') === 0;

	$verseNodes = $xpath->query(".//span[contains(@class, 'ChapterContent_verse__') and @data-usfm]", $blockNode);

	$startParagraph = 1;
	processVerses($verseNodes, $result, $xpath, $doc, $processedVerses, $startParagraph);
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

	// Parsing titles
	$titleAccumulator = '';
	$verseNumber = 1; // Default to 1

	// Get all child nodes within the chapter, preserving order
	$nodes = $xpath->query("//div[contains(@class, 'ChapterContent_chapter__')]/div");

	foreach ($nodes as $node) {
		// Check if node is a title node
		if (strpos($node->getAttribute('class'), 'ChapterContent_s1__') !== false) {
			// This is a title node
			$headingSpan = $xpath->query(".//span[contains(@class, 'ChapterContent_heading__')]", $node)->item(0);
			if ($headingSpan) {
				$titleText = trim($headingSpan->textContent);
				// Accumulate title text
				if (!empty($titleAccumulator)) {
					$titleAccumulator .= ' ';
				}
				$titleAccumulator .= $titleText;
			}
		} else {
			// This is not a title node
			// If there is accumulated title, add it to results
			if (!empty($titleAccumulator)) {
				// Try to find the first verse number after the title
				$verseNumber = null;
				$currentNode = $node;
				while ($currentNode) {
					// Look for verseSpan with 'ChapterContent_label__' inside
					$verseSpan = $xpath->query(".//span[contains(@class, 'ChapterContent_verse__')][.//span[contains(@class, 'ChapterContent_label__')]]", $currentNode)->item(0);
					if ($verseSpan) {
						$usfm = $verseSpan->getAttribute('data-usfm');
						// Split data-usfm on '+'
						$usfmVerseParts = explode('+', $usfm);
						$verseNumbers = array();
						foreach ($usfmVerseParts as $usfmVerse) {
							$usfmParts = explode('.', $usfmVerse);
							$verseNum = intval(end($usfmParts));
							if ($verseNum > 0) {
								$verseNumbers[] = $verseNum;
							}
						}
						if (!empty($verseNumbers)) {
							// Take the minimum verse number
							$verseNumber = min($verseNumbers);
						} else {
							$verseNumber = 1; // Default if no verse number found
						}
						break;
					}
					// Move to the next sibling node
					$currentNode = $currentNode->nextSibling;
					// Skip text nodes and comments
					while ($currentNode && $currentNode->nodeType !== XML_ELEMENT_NODE) {
						$currentNode = $currentNode->nextSibling;
					}
				}
				if ($verseNumber === null) {
					// If no verse found, default to 1
					$verseNumber = 1;
				}
				$result['titles'][] = [
					'before_verse_number' => $verseNumber,
					'text' => $titleAccumulator
				];
				// Reset accumulator
				$titleAccumulator = '';
			}
		}
	}

	// After the loop, if there is any accumulated title left, add it to results
	if (!empty($titleAccumulator)) {
		$result['titles'][] = [
			'before_verse_number' => $verseNumber ?? 1,
			'text' => $titleAccumulator
		];
	}

	// Массив для отслеживания уже обработанных стихов
	$processedVerses = [];

	// Парсим блоки (абзацы и цитаты)
	$blockNodes = $xpath->query("//div[starts-with(@class, 'ChapterContent_p__') or starts-with(@class, 'ChapterContent_q1__') or starts-with(@class, 'ChapterContent_d__') or starts-with(@class, 'ChapterContent_m__')]");

	foreach ($blockNodes as $blockNode) {
		processBlock($blockNode, $result, $xpath, $doc, $processedVerses);
	}

	// Обработка оставшихся стихов вне блоков
	$verseNodes = $xpath->query("//span[contains(@class, 'ChapterContent_verse__') and @data-usfm]");
	processVerses($verseNodes, $result, $xpath, $doc, $processedVerses, $startParagraph = 1);

	// Выводим результат в формате JSON
	//echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

	return $result;
}

function get_all_books($translation) 
{
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

			if ( $only_chapter!==false && $chapter_id<$only_chapter ) continue;
			if ( $only_chapter!==false && $chapter_id>$only_chapter ) break;
			
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