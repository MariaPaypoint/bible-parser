<?php

class BibleParser
{
    // Свойства
    private $only_book;
    private $only_chapter;
    private $translation;
    private $bible;

    // Константы
    const WRONG_TEXT = 'READER SETTINGSThis chapter is not available in this version. Please choose a different chapter or version.';

    // Конструктор
    public function __construct($translation, $only_book = false, $only_chapter = false)
    {
        $this->translation = $translation;
        $this->only_book = $only_book;
        $this->only_chapter = $only_chapter;

        setlocale(LC_ALL, 'ru_RU.utf8');
        libxml_use_internal_errors(true);
    }

    // Метод для получения URL
    private function get_url($book, $chapter)
    {
        $TRANSLATION = strtoupper($this->translation);
        $BOOK = strtoupper($book);
        $translation_code = $this->bible['bibleComDigitCode'];

        $url = "https://www.bible.com/bible/$translation_code/$BOOK.$chapter.$TRANSLATION";
        return $url;
    }

    // Дополнительные методы...
    // Например, processChildNodes, isNodeInQuote, isLetterOrDigit и т.д.

    // Метод для нормализации пробелов
    private function normalizeSpaces($text)
    {
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = preg_replace('/\s+([,.!?»“”])/u', '$1', $text);
        $text = preg_replace('/([«“„])\s+/u', '$1', $text);
        $text = preg_replace('/\s+([»”])/u', '$1', $text);
        $text = trim($text);
        return $text;
    }

    // Функция для определения, находится ли узел внутри цитаты
    private function isNodeInQuote($node) {
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
    private function isLetterOrDigit($char) {
        return preg_match('/\p{L}|\p{N}/u', $char);
    }
    private function isLetterOrDigitOrPunctuation($char) {
        return preg_match('/\p{L}|\p{N}|,|;|:|\.|—/u', $char);
    }

    // Метод парсинга заголовков
    // Метод для парсинга заголовков
    private function parse_titles($xpath)
    {
        $titles = [];
        $titleAccumulator = '';
        $verseNumber = 1;

        $nodes = $xpath->query("//div[contains(@class, 'ChapterContent_chapter__')]/div");

        foreach ($nodes as $node) {
            if (strpos($node->getAttribute('class'), 'ChapterContent_s1__') !== false) {
                $headingSpan = $xpath->query(".//span[contains(@class, 'ChapterContent_heading__')]", $node)->item(0);
                if ($headingSpan) {
                    $titleText = trim($headingSpan->textContent);
                    if (!empty($titleAccumulator)) {
                        $titleAccumulator .= ' ';
                    }
                    $titleAccumulator .= $titleText;
                }
            } else {
                if (!empty($titleAccumulator)) {
                    $verseNumber = null;
                    $currentNode = $node;
                    while ($currentNode) {
                        $verseSpan = $xpath->query(".//span[contains(@class, 'ChapterContent_verse__')][.//span[contains(@class, 'ChapterContent_label__')]]", $currentNode)->item(0);
                        if ($verseSpan) {
                            $usfm = $verseSpan->getAttribute('data-usfm');
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
                                $verseNumber = min($verseNumbers);
                            } else {
                                $verseNumber = 1;
                            }
                            break;
                        }
                        $currentNode = $currentNode->nextSibling;
                        while ($currentNode && $currentNode->nodeType !== XML_ELEMENT_NODE) {
                            $currentNode = $currentNode->nextSibling;
                        }
                    }
                    if ($verseNumber === null) {
                        $verseNumber = 1;
                    }
                    $titles[] = [
                        'before_verse_number' => $verseNumber,
                        'text' => $titleAccumulator
                    ];
                    $titleAccumulator = '';
                }
            }
        }

        if (!empty($titleAccumulator)) {
            $titles[] = [
                'before_verse_number' => $verseNumber ?? 1,
                'text' => $titleAccumulator
            ];
        }

        return $titles;
    }

    // Метод для парсинга главы
    private function parse_chapter($doc, $book, $chapter_id)
    {
        $verses = [];
        $notes = [];

        $xpath = new DOMXPath($doc);

        $result = [
            'id' => $chapter_id,
            'verses' => [],
            'notes' => [],
            'titles' => []
        ];

        // Извлекаем заголовок с названием книги и номером главы
        $headingNode = $xpath->query("//div[contains(@class, 'ChapterContent_reader__')]/h1")->item(0);
        if ($headingNode) {
            $heading = trim($headingNode->textContent); 
            // Предполагаем формат "НазваниеКниги НомерГлавы"
            // Например: "Начало 1"
            $parts = explode(' ', $heading);
            $extractedChapterNumber = array_pop($parts); 
            $extractedBookName = implode(' ', $parts);

            // Добавляем данные в итоговый массив
            $result['bookName'] = $extractedBookName;
            //$result['chapterNumber'] = $extractedChapterNumber;
        }

        $result['titles'] = $this->parse_titles($xpath);

        $processedVerses = [];

        $blockNodes = $xpath->query("//div[starts-with(@class, 'ChapterContent_p__') or starts-with(@class, 'ChapterContent_q1__') or starts-with(@class, 'ChapterContent_d__') or starts-with(@class, 'ChapterContent_m__')]");

        foreach ($blockNodes as $blockNode) {
            $this->processBlock($blockNode, $result, $xpath, $doc, $processedVerses);
        }
    
        // Process any remaining verses
        $verseNodes = $xpath->query("//span[contains(@class, 'ChapterContent_verse__') and @data-usfm]");
        $this->processVerses($verseNodes, $result, $xpath, $doc, $processedVerses);
        
        // Сортируем стихи по порядку
        usort($result['verses'], function ($a, $b) {
            return $a['id'] - $b['id'];
        });
        // перепозиционируем все примечания
        $this->fixNotesPositions($result);
        return $result;
    }

    private function fixNotesPositions(&$result) {
        foreach ($result['notes'] as $index => $note) {
            foreach($result['verses'] as $verse)
                if ($verse['id'] == $note['verse_number']) {
                    $unformatedText = $verse['unformatedText'];
                    $htmlText = $verse['htmlText'];
                }
            $position_text = $this->adjustTextPosition($unformatedText, $note['position_text']);
            $result['notes'][$index]['position_text'] = $position_text;
            $result['notes'][$index]['position_html'] = $this->getPositionInFormattedString($unformatedText, $htmlText, $position_text);
        } 
    }

    function adjustTextPosition($string, $notePosition) {
        $length = mb_strlen($string);
        
        // Проверяем, что позиция примечания находится в пределах строки
        if ($notePosition < 0 || $notePosition >= $length) {
            return $notePosition; // Возвращаем исходную позицию, если она некорректна
        }
        
        // Получаем часть строки от позиции примечания до конца
        $substring = mb_substr($string, $notePosition);
        
        // Используем регулярное выражение для поиска знаков препинания в начале подстроки
        if (preg_match('/^\p{P}+/u', $substring, $matches)) {
            // Если сразу после позиции примечания идет знак препинания, смещаем позицию за него
            $notePosition += mb_strlen($matches[0]);
        }
        
        return $notePosition;
    }
    

    private function getPositionInFormattedString($unformatted, $formatted, $positionInUnformatted) {
        if ($unformatted == $formatted) return $positionInUnformatted;

        $pos1 = 0; // Позиция в неформатированной строке
        $pos2 = 0; // Позиция в форматированной строке
        $lengthFormatted = strlen($formatted);
        $insideTag = false;
    
        while ($pos2 < $lengthFormatted) {
            //$char = $formatted[$pos2];
            $char = mb_substr($formatted, $pos2, 1);

            if ($char == '<') {
                $insideTag = true;
                //if ($positionInUnformatted==76) print("<");
            } 
            elseif ($char == '>') {
                $insideTag = false;
                //if ($positionInUnformatted==76) print(">");
            }
            elseif (!$insideTag) {
                //if ($positionInUnformatted==76) print($char."[$pos1]");
                if ($positionInUnformatted == 0)
                    return $pos2;
                elseif ($pos1 == $positionInUnformatted-1) {
                    // Найдена соответствующая позиция
                    //print($pos2." ".$formatted."\n");
                    return $pos2+1;
                }
                $pos1++;
            }
    
            $pos2++;
        }
    
        // Если позиция не найдена
        die("Position not found [$unformatted, $formatted, $positionInUnformatted]\n");
        //return -1; 
    }
    

    private function getParagraphNode($node) {
        while ($node && $node->nodeName !== 'body') {
            if ($node->nodeName === 'div' && (strpos($node->getAttribute('class'), 'ChapterContent_p__') === 0 ||
//                                            strpos($node->getAttribute('class'), 'ChapterContent_q1__') === 0 ||
                                              strpos($node->getAttribute('class'), 'ChapterContent_d__') === 0 ||
                                              strpos($node->getAttribute('class'), 'ChapterContent_m__') === 0)) {
                return $node;
            }
            $node = $node->parentNode;
        }
        return null;
    }

    private function processVerses($verseNodes, &$result, $xpath, $doc, &$processedVerses) {
        $prevParagraphNode = null;
        foreach ($verseNodes as $index => $verseNode) {
            // Получаем номер(а) стиха(ов) из атрибута data-usfm
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
    
            // Группируем узлы по родительским абзацам
            $nodesWithParagraphs = [];
            foreach ($nodesForVerse as $node) {
                $paragraphNode = $this->getParagraphNode($node);
                $nodesWithParagraphs[] = ['paragraphNode' => $paragraphNode, 'node' => $node];
            }
    
            // Создаём группы узлов по абзацам
            $groups = [];
            foreach ($nodesWithParagraphs as $item) {
                $paragraphNode = $item['paragraphNode'];
                $node = $item['node'];
    
                // Обрабатываем случай, когда $paragraphNode равен null
                if ($paragraphNode !== null) {
                    $key = spl_object_hash($paragraphNode);
                } else {
                    $key = 'null';
                }
    
                $groups[$key]['paragraphNode'] = $paragraphNode;
                $groups[$key]['nodes'][] = $node;
            }
    
            // Обработка каждой группы
            $htmlText = '';
            $unformattedText = '';
            $groupIndex = 0;
            foreach ($groups as $group) {
                $groupIndex++;
    
                $groupHtmlText = '';
                //$groupUnformattedText = '';
                $prevChar = '';
    
                foreach ($group['nodes'] as $node) {
                    $this->processVerseNode($node, $groupHtmlText, $unformattedText, $result, $verseId, $xpath, $doc, false, $prevChar);
                }
    
                // Нормализуем пробелы
                $groupHtmlText = $this->normalizeSpaces($groupHtmlText);
                $unformattedText = $this->normalizeSpaces($unformattedText);
    
                // Проверяем, есть ли текст в группе
                if (!empty(trim($groupHtmlText))) {
                    // Если это не первая группа, оборачиваем в span с классом 'paragraph'
                    if ($groupIndex > 1) {
                        $groupHtmlText = '<span class="paragraph">' . $groupHtmlText . '</span>';
                    }
    
                    // Добавляем к общему тексту
                    $htmlText .= $groupHtmlText;
                    //$unformattedText .= $groupUnformattedText;
                }
            }
    
            // Определяем, начинается ли стих с нового абзаца
            $firstGroup = reset($groups);
            $firstParagraphNode = $firstGroup['paragraphNode'];
            $isNewParagraph = ($firstParagraphNode !== $prevParagraphNode);
            $startParagraphValue = $isNewParagraph ? 1 : 0;
    
            // Обновляем $prevParagraphNode
            $lastGroup = end($groups);
            $prevParagraphNode = $lastGroup['paragraphNode'];
    
            // Добавляем стих в результат
            $result['verses'][] = [
                'id' => $verseId,
                'htmlText' => $htmlText,
                'unformatedText' => $unformattedText,
                'start_paragraph' => $startParagraphValue,
                'join' => $join
            ];
        }
    }
    
    function removeVerseReference($text) {
        // Используем регулярное выражение для поиска и удаления ссылки на стих в начале строки
        $pattern = '/^\s*\d+:\d+\s*/u';
        return preg_replace($pattern, '', $text);
    }

    // Рекурсивная функция для обработки узлов стиха и примечаний
    private function processVerseNode($node, &$htmlText, &$unformattedText, &$result, $verseId, $xpath, $doc, $insideQuote = false, &$prevChar = '') {
        if ($node->nodeName === 'span' && strpos($node->getAttribute('class'), 'ChapterContent_label__') !== false) {
            // Пропускаем номер стиха
            return;
        } elseif ($node->nodeName === 'span' && strpos($node->getAttribute('class'), 'ChapterContent_note__') !== false) {
            // Примечание
            $noteId = count($result['notes']) + 1;
            $noteTextNode = $xpath->query(".//span[contains(@class, 'ChapterContent_body__')]", $node)->item(0);
            if ($noteTextNode) {
                $noteText = $this->normalizeSpaces($this->removeVerseReference($noteTextNode->textContent));
                $positionText = mb_strlen($unformattedText);

                $result['notes'][] = [
                    'id' => $noteId,
                    'text' => $noteText,
                    'verse_number' => $verseId,
                    'position_text' => $positionText
                ];
            }
            // Не добавляем примечание в текст
        } else {
            $isQuote = $this->isNodeInQuote($node);
            $currentInsideQuote = $insideQuote || $isQuote;

            if ($node->nodeName === 'span' && strpos($node->getAttribute('class'), 'ChapterContent_add__') !== false) {
                // Обрабатываем курсив
                $emContent = '';
                //$emUnformatted = '';
                $this->processChildNodes($node, $emContent, $unformattedText, $result, $verseId, $xpath, $doc, $currentInsideQuote, $prevChar);

                $emHtml = '<em>' . $emContent . '</em>';

                if ($isQuote && !$insideQuote && trim($emHtml)) {
                    $emHtml = '<span class="quote">' . $emHtml . '</span>';
                }

                $htmlText .= $emHtml;
                //$unformattedText .= $emUnformatted;
                $prevChar = mb_substr($unformattedText, -1);
            } else {
                if ($node->hasChildNodes()) {
                    $childHtml = '';
                    //$childUnformatted = '';
                    $this->processChildNodes($node, $childHtml, $unformattedText, $result, $verseId, $xpath, $doc, $currentInsideQuote, $prevChar);

                    if ($isQuote && !$insideQuote && trim($childHtml)) {
                        $childHtml = '<span class="quote">' . $childHtml . '</span>';
                    }

                    $htmlText .= $childHtml;
                    //$unformattedText .= $childUnformatted;
                } else {
                    if ($node->nodeType === XML_TEXT_NODE) {
                        $text = ltrim($node->textContent);
                        $htmlEncodedText = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

                        // Проверяем, нужно ли добавить пробел
                        $needsSpace = false;
                        if ($prevChar !== '' && $this->isLetterOrDigitOrPunctuation($prevChar)) {
                            $firstChar = mb_substr($text, 0, 1);
                            if ($this->isLetterOrDigit($firstChar)) {
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
                            $textContent = ltrim($node->textContent);

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
                            $text = ltrim($node->textContent);
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


    // Функция для обработки блоков (абзацы и цитаты)
    private function processBlock($blockNode, &$result, $xpath, $doc, &$processedVerses) {
        // Определяем, является ли блок цитатой
        $class = $blockNode->getAttribute('class');
        $isQuote = strpos($class, 'ChapterContent_q1__') === 0;

        $verseNodes = $xpath->query(".//span[contains(@class, 'ChapterContent_verse__') and @data-usfm]", $blockNode);
        $this->processVerses($verseNodes, $result, $xpath, $doc, $processedVerses);
    }

    // Функция для обработки дочерних узлов
    private function processChildNodes($node, &$htmlText, &$unformattedText, &$result, $verseId, $xpath, $doc, $insideQuote, &$prevChar) {
        foreach ($node->childNodes as $childNode) {
            $this->processVerseNode($childNode, $htmlText, $unformattedText, $result, $verseId, $xpath, $doc, $insideQuote, $prevChar);
        }
    }

    // Метод для ручной корректировки
    private function manual_fix($book, $chapter_id, $chapterArray)
    {
        if ($this->translation == 'bti') {
            $deleteNext = false;
            $newVerses = [];
            foreach ($chapterArray['verses'] as $v) {
                if ($v['unformatedText'] == '—') {
                    $deleteNext = true;
                    continue;
                }
                if ($deleteNext) {
                    $v['id'] -= 1;
                    $v['join'] += 1;
                    $deleteNext = false;
                }
                array_push($newVerses, $v);
            }
            $chapterArray['verses'] = $newVerses;

            if ($book == 52 and $chapter_id == 16) {
                $newVerses = [];
                foreach ($chapterArray['verses'] as $v) {
                    if ($v['id'] == 24) {
                        $v['htmlText'] = '[]';
                        $v['unformatedText'] = '[]';
                    } elseif ($v['id'] == 25) {
                        $v['htmlText'] = '[' . $v['htmlText'];
                        $v['unformatedText'] = '[' . $v['unformatedText'];
                    }

                    array_push($newVerses, $v);
                }
                $chapterArray['verses'] = $newVerses;
            }
        }
        return $chapterArray;
    }

    // Метод для получения всех книг
    private function get_all_books()
    {
        $doc = new DOMDocument();
        $this->bible = [];
        $this->bible['code'] = $this->translation;
        $this->bible = array_merge_recursive($this->bible, get_translation_info($this->translation));

        $this->bible['books'] = [];

        $book = 0;

        while (true) {
            $book++;

            if ($this->only_book !== false && $book < $this->only_book) continue;
            if ($this->only_book !== false && $book > $this->only_book) break;

            $book_info = get_book_info($book);
            if ($book_info === false) break;

            print "Book $book. Chapters: ";

            $chapter_id = 0;
            $bookArray = [];
            $bookArray['id'] = $book;
            $bookArray['code'] = $book_info['code'];
            $bookArray['shortName'] = $book_info['shortName'][$this->bible['lang']];
            $bookArray['fullName'] = $book_info['fullName'][$this->bible['lang']];
            $bookArray['chapters'] = [];

            while (true) {
                $chapter_id++;

                if ($this->only_chapter !== false && $chapter_id < $this->only_chapter) continue;
                if ($this->only_chapter !== false && $chapter_id > $this->only_chapter) break;

                $url = $this->get_url($book_info['code'], $chapter_id);
                $doc->loadHTMLFile($url);
                if (strpos($doc->textContent, self::WRONG_TEXT) !== false) {
                    // print("$url\n");
                    // print(self::WRONG_TEXT."\n");
                    break;
                }
                print " $chapter_id";

                $chapterArray = $this->parse_chapter($doc, $book, $chapter_id);
                $chapterArray = $this->manual_fix($book, $chapter_id, $chapterArray);

                $bookArray['fullName'] = $chapterArray['bookName']; // подменяем название книги, на распарсенное со страницы
                unset($chapterArray['bookName']);

                array_push($bookArray['chapters'], $chapterArray);
            }

            array_push($this->bible['books'], $bookArray);

            print " OK\n";
        }
    }

    // Метод для подготовки окружения
    private function prepare_environment()
    {
        create_dir777_if_not_exists('text');
    }

    // Метод для сохранения в файл
    private function save_to_file()
    {
        $translation = $this->translation;
        $filename = 'text' . DIRECTORY_SEPARATOR . $translation . '.json';
        file_put_contents($filename, json_encode($this->bible, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        print "\nSuccess! File $filename saved.\n\n";
    }

    // Основной метод для запуска парсинга
    public function parse()
    {
        $this->prepare_environment();

        print "\nStart {$this->translation} downloading\n\n";

        $this->get_all_books();
        $this->save_to_file();
    }

    public function parseForTest()
    {
        $this->prepare_environment();

        print "\nStart {$this->translation} downloading\n\n";

        $this->get_all_books();
        
        // Не сохраняем данные в файл
        $this->save_to_file();

        return $this->bible; // Возвращаем данные для тестирования
    }
}