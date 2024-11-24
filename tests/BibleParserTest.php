<?php
use PHPUnit\Framework\TestCase;

require "include.php";
require "parse_com.class.php";

class BibleParserTest extends TestCase
{
    public function test_BTI_1_1()
    {
        // Параметры для теста
        $translation = 'bti';
        $only_book = 1;         // Книга Бытие
        $only_chapter = 1;

        // Создаём экземпляр парсера для конкретной книги и главы
        $parser = new BibleParser($translation, $only_book, $only_chapter);

        // Вызываем метод парсинга
        $bibleData = $parser->parseForTest();

        // Проверяем, что данные не пустые
        $this->assertNotEmpty($bibleData, 'Parsed data should not be empty.');

        // Проверяем, что структура данных соответствует ожиданиям
        $this->assertArrayHasKey('books', $bibleData, 'Data should contain books.');

        // Проверяем, что книга есть
        $this->assertEquals($only_book, $bibleData['books'][0]['id'], "First book id should be $only_book.");

        // Проверяем, что глава есть
        $this->assertEquals($only_chapter, $bibleData['books'][0]['chapters'][0]['id'], "First chapter id should be $only_chapter.");

        // Проверяем, что есть стихи в главе
        $this->assertNotEmpty($bibleData['books'][0]['chapters'][0]['verses'], 'Chapter should contain verses.');

        // Проверяем конкретный стих, например, первый стих
        $firstVerse = $bibleData['books'][0]['chapters'][0]['verses'][0];

        // Проверяем, что id стиха равен 1
        $this->assertEquals(1, $firstVerse['id'], 'First verse id should be 1.');

        // Проверяем содержимое стиха
        $expectedTextStart = 'В начале сотворил Бог небо и землю';
        $this->assertStringStartsWith($expectedTextStart, $firstVerse['unformatedText'], 'First verse text should start with expected text.');

        // Стихи, которые должны быть началом параграфов
        $arrParagraphStart = [1, 6, 9, 11, 14, 20, 24, 26, 28, 31];

        foreach ($bibleData['books'][0]['chapters'][0]['verses'] as $verse) {
            $verseId = $verse['id'];
            $startParagraph = $verse['start_paragraph'];
            
            if (in_array($verseId, $arrParagraphStart)) {
                // Если стих должен быть началом параграфа
                $this->assertEquals(1, $startParagraph, "Verse $verseId must have start_paragraph=1.");
            } else {
                // Если стих не должен быть началом параграфа
                $this->assertEquals(0, $startParagraph, "Verse $verseId must have start_paragraph=0.");
            }
        }
    }

    public function test_BTI_40_1()
    {
        // Параметры для теста
        $translation = 'bti';
        $only_book = 40;         // от Матфея
        $only_chapter = 1;

        // Создаём экземпляр парсера для конкретной книги и главы
        $parser = new BibleParser($translation, $only_book, $only_chapter);

        // Вызываем метод парсинга
        $bibleData = $parser->parseForTest();

        // Проверяем содержимое стиха
        $text6 = $this->getVerseData($bibleData, 6, 'htmlText');
        $expectedTextStart = '<span class="quote">от Иессея родился Давид-царь.</span><span class="paragraph">Давид был отцом <em>царя</em> Соломона, мать которого <em>прежде</em> была за Урией;</span>';
        $this->assertStringStartsWith($expectedTextStart, $text6, '6 verse text should start with expected text.');

        // Стихи, которые должны быть началом параграфов
        $arrParagraphStart = [1, 17, 18, 19, 22, 24];

        foreach ($bibleData['books'][0]['chapters'][0]['verses'] as $verse) {
            $verseId = $verse['id'];
            $startParagraph = $verse['start_paragraph'];
            
            if (in_array($verseId, $arrParagraphStart)) {
                // Если стих должен быть началом параграфа
                $this->assertEquals(1, $startParagraph, "Verse $verseId must have start_paragraph=1.");
            } else {
                // Если стих не должен быть началом параграфа
                $this->assertEquals(0, $startParagraph, "Verse $verseId must have start_paragraph=0.");
            }
        }
    }

    protected function getVerseData($bibleData, $number, $param)
    {
        foreach ($bibleData['books'][0]['chapters'][0]['verses'] as $verse) {
            if ($verse['id'] == $number) {
                return $verse[$param];
            }
        }
        $this->fail("Verse with id $number is not found");
    }
}
