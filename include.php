<?php

function get_translation_info($translation)
{
	switch ($translation)
	{
		case 'syn' : return ['lang'=>'ru' , 'shortName'=>'SYNO' , 'fullName'=>'Синодальный перевод'];
		case 'nrt' : return ['lang'=>'ru' , 'shortName'=>'НРП'  , 'fullName'=>'Новый русский перевод'];
		case 'bti' : return ['lang'=>'ru' , 'shortName'=>'BTI'  , 'fullName'=>'Библия в переводе Кулаковых'];
		case 'kjv' : return ['lang'=>'ru' , 'shortName'=>'KJV'  , 'fullName'=>'King James Bible'];
	}
	
	//return '';
	die('Incorrect translation.');
}

function get_voice_info($translation)
{
	switch ($translation)
	{
		case 'bondarenko' : return ['name'=>'Александр Бондаренко', 'isMusic'=>1, 'description'=>'Текст читает Александр Викторович Бондаренко — украинский актёр театра и кино, народный артист Украины. Жил с нами в период с 1960 по 2013 год. Его голосом не озвучено лишь книги Паралипоменон и Песнь Песней. Эти книги заменены на чтение Игоря Козлова. Особенность этой озвучки — драматическое музыкальное сопровождение.'];
	}
	
	die('Incorrect voice.');
}

function get_book_prename($voice, $book_index) {
	switch ($voice)
	{
		case 'bondarenko':
		{
			switch ($book_index)
			{
				case 1 : return 'Первая книга Моисеева Бытие';
				case 2 : return 'Вторая книга Моисея Исход';
				case 3 : return 'Третья книга Моисеева Левит';
				case 4 : return 'Четвертая книга Моисеева Числа';
				case 5 : return 'Пятая книга Моисеева Второзаконие';
				
				case 6 : return 'Книга Иисуса Навина';
				case 7 : return 'Книга судей Израилевых';
				case 8 : return 'Книга Руфь';
				case 9 : return 'Первая книга Царств';
				case 10: return '';
				case 11: return 'Третья книга Царств';
				case 12: return 'Четвертая книга Царств';
				case 13: return 'Первая книга Паралипоменон';
				case 14: return 'Вторая книга Паралипоменон';
				case 15: return 'Книга Ездры';
				case 16: return 'Книга Неемии';
				case 17: return 'Книга Есфирь';
				
				case 18: return 'Книга Иова';
				case 19: return 'Псалтырь';
				case 20: return 'Книга Притчей Соломоновых';
				case 21: return 'Книга Екклесиаста или Проповедника';
				case 22: return 'Книга Песни Песней Соломона';
				
				case 23: return 'Книга пророка Исаии';
				case 24: return 'Книга пророка Иеремии';
				case 25: return 'Книга Плач Иеремии';
				case 26: return 'Книга пророка Иезекииля';
				case 27: return 'Книга пророка Даниила';
				
				case 28: return 'Малые пророки. Осия';
				case 29: return 'Книга пророка Иоиля';
				case 30: return 'Книга пророка Амоса';
				case 31: return 'Книга пророка Авдия';
				case 32: return 'Книга пророка Ионы';
				case 33: return 'Книга пророка Михея';
				case 34: return 'Книга пророка Наума';
				case 35: return 'Книга пророка Аввакума';
				case 36: return 'Книга пророка Софонии';
				case 37: return 'Книга пророка Аггея';
				case 38: return 'Книга пророка Захарии';
				case 39: return 'Книга пророка Малахии';
				
				case 40: return 'Евангелие от Матфея';
				case 41: return 'Евангелие от Марка';
				case 42: return 'Евангелие от Луки';
				case 43: return 'Евангелие от Иоанна';
				case 44: return 'Деяния Святых апостолов';
				
				case 45: return 'Соборные послания Святого апостола Иакова';
				case 46: return 'Первое соборное послание Святого апостола Петра';
				case 47: return 'Второе соборное послание Святого апостола Петра';
				case 48: return 'Первое соборное послание Святого апостола Иоанна Богослова';
				case 49: return 'Второе соборное послание Святого апостола Иоанна Богослова';
				case 50: return 'Третье соборное послание Святого апостола Иоанна Богослова';
				case 51: return 'Соборное послание Святого апостола Иуды';
				
				case 52: return 'Послание к Римлянам Святого апостола Павла';
				case 53: return 'Первое послание к Коринфянам Святого апостола Павла';
				case 54: return 'Второе послание к Коринфянам Святого апостола Павла';
				case 55: return 'Послание к Галатам Святого апостола Павла';
				case 56: return 'Послание к Ефесянам Святого апостола Павла';
				case 57: return 'Послание к Филиппийцам Святого апостола Павла';
				case 58: return 'Послание к Колоссянам Святого апостола Павла';
				case 59: return 'Первое послание к Фессалоникийцам Святого апостола Павла';
				case 60: return 'Второе послание к Фессалоникийцам Святого апостола Павла';
				case 61: return 'Первое послание к Тимофею Святого апостола Павла';
				case 62: return 'Второе послание к Тимофею Святого апостола Павла';
				case 63: return 'Послание к Титу Святого апостола Павла';
				case 64: return 'Филимону Святого апостола Павла';
				case 65: return 'Послание к Евреям Святого апостола Павла';
				
				case 66: return 'Откровение Святого Иоанна Богослова';
			}
		}
	}
	
	return '';
}

function get_book_info($book_index)
{
	switch ($book_index)
	{
		case 1 : return ['code'=>'gen', 'shortName'=>['en'=>'Gen'   , 'ru'=>'Быт'  ] , 'fullName'=>['en'=>'Genesis'         , 'ru'=>'Бытие']];
		case 2 : return ['code'=>'exo', 'shortName'=>['en'=>'Ex'    , 'ru'=>'Исх'  ] , 'fullName'=>['en'=>'Exodus'          , 'ru'=>'Исход']];
		case 3 : return ['code'=>'lev', 'shortName'=>['en'=>'Lev'   , 'ru'=>'Лев'  ] , 'fullName'=>['en'=>'Leviticus'       , 'ru'=>'Левит']];
		case 4 : return ['code'=>'num', 'shortName'=>['en'=>'Num'   , 'ru'=>'Чис'  ] , 'fullName'=>['en'=>'Numbers'         , 'ru'=>'Числа']];
		case 5 : return ['code'=>'deu', 'shortName'=>['en'=>'Deut'  , 'ru'=>'Втор' ] , 'fullName'=>['en'=>'Deuteronomy'     , 'ru'=>'Второзаконие']];

		case 6 : return ['code'=>'jos', 'shortName'=>['en'=>'Josh'  , 'ru'=>'Нав'  ] , 'fullName'=>['en'=>'Joshua'          , 'ru'=>'Иисус Навин']];
		case 7 : return ['code'=>'jdg', 'shortName'=>['en'=>'Judg'  , 'ru'=>'Суд'  ] , 'fullName'=>['en'=>'Judges'          , 'ru'=>'Судьи']];
		case 8 : return ['code'=>'rut', 'shortName'=>['en'=>'Ruth'  , 'ru'=>'Руфь' ] , 'fullName'=>['en'=>'Ruth'            , 'ru'=>'Руфь']];
		case 9 : return ['code'=>'1sa', 'shortName'=>['en'=>'1Sam'  , 'ru'=>'1Цар' ] , 'fullName'=>['en'=>'1 Samuel'        , 'ru'=>'1 Царств']];
		case 10: return ['code'=>'2sa', 'shortName'=>['en'=>'2Sam'  , 'ru'=>'2Цар' ] , 'fullName'=>['en'=>'2 Samuel'        , 'ru'=>'2 Царств']];
		case 11: return ['code'=>'1ki', 'shortName'=>['en'=>'1Kings', 'ru'=>'3Цар' ] , 'fullName'=>['en'=>'1 Kings'         , 'ru'=>'3 Царств']];
		case 12: return ['code'=>'2ki', 'shortName'=>['en'=>'2Kings', 'ru'=>'4Цар' ] , 'fullName'=>['en'=>'2 Kings'         , 'ru'=>'4 Царств']];
		case 13: return ['code'=>'1ch', 'shortName'=>['en'=>'1Chron', 'ru'=>'1Пар' ] , 'fullName'=>['en'=>'1 Chronicles'    , 'ru'=>'1 Паралипоменон']];
		case 14: return ['code'=>'2ch', 'shortName'=>['en'=>'2Chron', 'ru'=>'2Пар' ] , 'fullName'=>['en'=>'2 Chronicles'    , 'ru'=>'2 Паралипоменон']];
		case 15: return ['code'=>'ezr', 'shortName'=>['en'=>'Ezra'  , 'ru'=>'Езд'  ] , 'fullName'=>['en'=>'Ezra'            , 'ru'=>'Ездра']];
		case 16: return ['code'=>'neh', 'shortName'=>['en'=>'Neh'   , 'ru'=>'Неем' ] , 'fullName'=>['en'=>'Nehemiah'        , 'ru'=>'Неемия']];
		case 17: return ['code'=>'est', 'shortName'=>['en'=>'Esther', 'ru'=>'Есф'  ] , 'fullName'=>['en'=>'Esther'          , 'ru'=>'Есфирь']];

		case 18: return ['code'=>'job', 'shortName'=>['en'=>'Job'   , 'ru'=>'Иов'  ] , 'fullName'=>['en'=>'Job'             , 'ru'=>'Иов']];
		case 19: return ['code'=>'psa', 'shortName'=>['en'=>'Ps'    , 'ru'=>'Пс'   ] , 'fullName'=>['en'=>'Psalms'          , 'ru'=>'Псалтирь']];
		case 20: return ['code'=>'pro', 'shortName'=>['en'=>'Prov'  , 'ru'=>'Прит' ] , 'fullName'=>['en'=>'Proverbs'        , 'ru'=>'Притчи']];
		case 21: return ['code'=>'ecc', 'shortName'=>['en'=>'Eccles', 'ru'=>'Еккл' ] , 'fullName'=>['en'=>'Ecclesiastes'    , 'ru'=>'Екклесиаст']];
		case 22: return ['code'=>'sng', 'shortName'=>['en'=>'Song'  , 'ru'=>'Песн' ] , 'fullName'=>['en'=>'Song of Solomon' , 'ru'=>'Песни Песней']];

		case 23: return ['code'=>'isa', 'shortName'=>['en'=>'Is'    , 'ru'=>'Ис'   ] , 'fullName'=>['en'=>'Isaiah'          , 'ru'=>'Исаия']];
		case 24: return ['code'=>'jer', 'shortName'=>['en'=>'Jer'   , 'ru'=>'Иер'  ] , 'fullName'=>['en'=>'Jeremiah'        , 'ru'=>'Иеремия']];
		case 25: return ['code'=>'lam', 'shortName'=>['en'=>'Lam'   , 'ru'=>'Плач' ] , 'fullName'=>['en'=>'Lamentations'    , 'ru'=>'Плач Иеремии']];
		case 26: return ['code'=>'ezk', 'shortName'=>['en'=>'Ezek'  , 'ru'=>'Иез'  ] , 'fullName'=>['en'=>'Ezekiel'         , 'ru'=>'Иезекииль']];
		case 27: return ['code'=>'dan', 'shortName'=>['en'=>'Dan'   , 'ru'=>'Дан'  ] , 'fullName'=>['en'=>'Daniel'          , 'ru'=>'Даниил']];

		case 28: return ['code'=>'hos', 'shortName'=>['en'=>'Hos'   , 'ru'=>'Ос'   ] , 'fullName'=>['en'=>'Hosea'           , 'ru'=>'Осия']];
		case 29: return ['code'=>'jol', 'shortName'=>['en'=>'Joel'  , 'ru'=>'Иоиль'] , 'fullName'=>['en'=>'Joel'            , 'ru'=>'Иоиль']];
		case 30: return ['code'=>'amo', 'shortName'=>['en'=>'Amos'  , 'ru'=>'Амос' ] , 'fullName'=>['en'=>'Amos'            , 'ru'=>'Амос']];
		case 31: return ['code'=>'oba', 'shortName'=>['en'=>'Obad'  , 'ru'=>'Авд'  ] , 'fullName'=>['en'=>'Obadiah'         , 'ru'=>'Авдий']];
		case 32: return ['code'=>'jon', 'shortName'=>['en'=>'Jon'   , 'ru'=>'Иона' ] , 'fullName'=>['en'=>'Jonah'           , 'ru'=>'Иона']];
		case 33: return ['code'=>'mic', 'shortName'=>['en'=>'Mic'   , 'ru'=>'Мих'  ] , 'fullName'=>['en'=>'Micah'           , 'ru'=>'Михей']];
		case 34: return ['code'=>'nam', 'shortName'=>['en'=>'Nahum' , 'ru'=>'Наум' ] , 'fullName'=>['en'=>'Nahum'           , 'ru'=>'Наум']];
		case 35: return ['code'=>'hab', 'shortName'=>['en'=>'Hab'   , 'ru'=>'Авв'  ] , 'fullName'=>['en'=>'Habakkuk'        , 'ru'=>'Аввакум']];
		case 36: return ['code'=>'zep', 'shortName'=>['en'=>'Zeph'  , 'ru'=>'Соф'  ] , 'fullName'=>['en'=>'Zephaniah'       , 'ru'=>'Софония']];
		case 37: return ['code'=>'hag', 'shortName'=>['en'=>'Hag'   , 'ru'=>'Агг'  ] , 'fullName'=>['en'=>'Haggai'          , 'ru'=>'Аггей']];
		case 38: return ['code'=>'zec', 'shortName'=>['en'=>'Zech'  , 'ru'=>'Зах'  ] , 'fullName'=>['en'=>'Zechariah'       , 'ru'=>'Захария']];
		case 39: return ['code'=>'mal', 'shortName'=>['en'=>'Mal'   , 'ru'=>'Мал'  ] , 'fullName'=>['en'=>'Malachi'         , 'ru'=>'Малахия']];

		case 40: return ['code'=>'mat', 'shortName'=>['en'=>'Mt'    , 'ru'=>'Мф'   ] , 'fullName'=>['en'=>'Matthew'         , 'ru'=>'Евангелие от Матфея']];
		case 41: return ['code'=>'mrk', 'shortName'=>['en'=>'Mk'    , 'ru'=>'Мк'   ] , 'fullName'=>['en'=>'Mark'            , 'ru'=>'Евангелие от Марка']];
		case 42: return ['code'=>'luk', 'shortName'=>['en'=>'Lk'    , 'ru'=>'Лк'   ] , 'fullName'=>['en'=>'Luke'            , 'ru'=>'Евангелие от Луки']];
		case 43: return ['code'=>'jhn', 'shortName'=>['en'=>'Jn'    , 'ru'=>'Ин'   ] , 'fullName'=>['en'=>'John'            , 'ru'=>'Евангелие от Иоанна']];
		case 44: return ['code'=>'act', 'shortName'=>['en'=>'Acts'  , 'ru'=>'Деян' ] , 'fullName'=>['en'=>'Acts'            , 'ru'=>'Деяния апостолов']];

		case 45: return ['code'=>'jas', 'shortName'=>['en'=>'Jas'   , 'ru'=>'Иак'  ] , 'fullName'=>['en'=>'James'           , 'ru'=>'Иакова']];
		case 46: return ['code'=>'1pe', 'shortName'=>['en'=>'1Pet'  , 'ru'=>'1Пет' ] , 'fullName'=>['en'=>'1 Peter'         , 'ru'=>'1 Петра']];
		case 47: return ['code'=>'2pe', 'shortName'=>['en'=>'2Pet'  , 'ru'=>'2Пет' ] , 'fullName'=>['en'=>'2 Peter'         , 'ru'=>'2 Петра']];
		case 48: return ['code'=>'1jn', 'shortName'=>['en'=>'1Jn'   , 'ru'=>'1Ин'  ] , 'fullName'=>['en'=>'1 John'          , 'ru'=>'1 Иоанна']];
		case 49: return ['code'=>'2jn', 'shortName'=>['en'=>'2Jn'   , 'ru'=>'2Ин'  ] , 'fullName'=>['en'=>'2 John'          , 'ru'=>'2 Иоанна']];
		case 50: return ['code'=>'3jn', 'shortName'=>['en'=>'3Jn'   , 'ru'=>'3Ин'  ] , 'fullName'=>['en'=>'3 John'          , 'ru'=>'3 Иоанна']];
		case 51: return ['code'=>'jud', 'shortName'=>['en'=>'Jude'  , 'ru'=>'Иуд'  ] , 'fullName'=>['en'=>'Jude'            , 'ru'=>'Иуды']];

		case 52: return ['code'=>'rom', 'shortName'=>['en'=>'Rom'   , 'ru'=>'Рим'  ] , 'fullName'=>['en'=>'Romans'          , 'ru'=>'Римлянам']];
		case 53: return ['code'=>'1co', 'shortName'=>['en'=>'1Cor'  , 'ru'=>'1Кор' ] , 'fullName'=>['en'=>'1 Corinthians'   , 'ru'=>'1 Коринфянам']];
		case 54: return ['code'=>'2co', 'shortName'=>['en'=>'2Cor'  , 'ru'=>'2Кор' ] , 'fullName'=>['en'=>'2 Corinthians'   , 'ru'=>'2 Коринфянам']];
		case 55: return ['code'=>'gal', 'shortName'=>['en'=>'Gal'   , 'ru'=>'Гал'  ] , 'fullName'=>['en'=>'Galatians'       , 'ru'=>'Галатам']];
		case 56: return ['code'=>'eph', 'shortName'=>['en'=>'Eph'   , 'ru'=>'Еф'   ] , 'fullName'=>['en'=>'Ephesians'       , 'ru'=>'Ефесянам']];
		case 57: return ['code'=>'php', 'shortName'=>['en'=>'Phil'  , 'ru'=>'Фил'  ] , 'fullName'=>['en'=>'Philippians'     , 'ru'=>'Филиппийцам']];
		case 58: return ['code'=>'col', 'shortName'=>['en'=>'Col'   , 'ru'=>'Кол'  ] , 'fullName'=>['en'=>'Colossians'      , 'ru'=>'Колоссянам']];
		case 59: return ['code'=>'1th', 'shortName'=>['en'=>'1Thess', 'ru'=>'1Фес' ] , 'fullName'=>['en'=>'1 Thessalonians' , 'ru'=>'1 Фессалоникийцам']];
		case 60: return ['code'=>'2th', 'shortName'=>['en'=>'2Thess', 'ru'=>'2Фес' ] , 'fullName'=>['en'=>'2 Thessalonians' , 'ru'=>'2 Фессалоникийцам']];
		case 61: return ['code'=>'1ti', 'shortName'=>['en'=>'1Tim'  , 'ru'=>'1Тим' ] , 'fullName'=>['en'=>'1 Timothy'       , 'ru'=>'1 Тимофею']];
		case 62: return ['code'=>'2ti', 'shortName'=>['en'=>'2Tim'  , 'ru'=>'2Тим' ] , 'fullName'=>['en'=>'2 Timothy'       , 'ru'=>'2 Тимофею']];
		case 63: return ['code'=>'tit', 'shortName'=>['en'=>'Tit'   , 'ru'=>'Тит'  ] , 'fullName'=>['en'=>'Titus'           , 'ru'=>'Титу']];
		case 64: return ['code'=>'phm', 'shortName'=>['en'=>'Philem', 'ru'=>'Флм'  ] , 'fullName'=>['en'=>'Philemon'        , 'ru'=>'Филимону']];
		case 65: return ['code'=>'heb', 'shortName'=>['en'=>'Heb'   , 'ru'=>'Евр'  ] , 'fullName'=>['en'=>'Hebrews'         , 'ru'=>'Евреям']];

		case 66: return ['code'=>'rev', 'shortName'=>['en'=>'Rev'   , 'ru'=>'Откр' ] , 'fullName'=>['en'=>'Revelation'      , 'ru'=>'Откровение']];
	}
	
	return 'unknown';
}

// получение входящих параметров

function determine_text_translation($position=1)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("\nERROR: Set translation var! \nExample usage: \n$ php timecodes.php syn syn-bondarenko\n\n");
	
	$translation = $argv[$position];
	$filename = "text/$translation.json";
	
	if ( !file_exists($filename) )
		die("Translation not found (expected: $filename)\n\n");
	
	return $translation;
}

function determine_voice_4bbl($translation, $position=2)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("\nERROR: Set voice var! \nExample usage: \n$ php timecodes.php syn syn-bondarenko\n\n");
	
	$voice = $argv[$position];
	
	$url = get_chapter_audio_url($translation, $voice, '01', '01');
	
	if ( !file_get_contents($url) )
		die("Voice not found (example url: $url)\n\n");
	
	return $voice;
}

function determine_mode($position=3)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("Mode is not set (wait one of: MODE_REPLACE, MODE_CHANGE)\n\n");
	
	$mode = $argv[$position];
	
	if ( !in_array($mode, ['MODE_REPLACE', 'MODE_CHANGE']) )
		die("Unknown mode: $mode (wait one of: MODE_REPLACE, MODE_CHANGE)\n\n");
	
	return $mode;
}

function determine_step($position=4)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("Step is not set (wait one of: 1, 2)\n\n");
	
	$step = $argv[$position];
	
	if ( !in_array($step, ['1', '2']) )
		die("Unknown step: $step (wait one of: 1, 2)\n\n");
	
	return $step;
}

function determine_export_type($position=2)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("Export type is not set (wait one of: TEXT, TIMECODES)\n\n");
	
	$step = $argv[$position];
	
	if ( !in_array($step, ['TEXT', 'TIMECODES']) )
		die("Unknown export type: $step (wait one of: TEXT, TIMECODES)\n\n");
	
	return $step;
}

// для аудио


function get_translation_array($translation)
{
	$filename = "text/$translation.json";
	$translationArray = json_decode(file_get_contents($filename), true);
	
	return $translationArray;
}

function get_voice_array($translation, $voice)
{
	$filename = "audio/$translation/$voice/timecodes.json";
	$voiceArray = json_decode(file_get_contents($filename), true);
	
	return $voiceArray;
}

function get_chapter_name_1($digit) 
{
	switch ($digit)
	{
		case 0 : return '';
		case 1 : return 'первая';
		case 2 : return 'вторая';
		case 3 : return 'третья';
		case 4 : return 'четвертая';
		case 5 : return 'пятая';
		case 6 : return 'шестая';
		case 7 : return 'седьмая';
		case 8 : return 'восьмая';
		case 9 : return 'девятая';
	}
}
function get_chapter_name_2($digit) 
{
	switch ($digit)
	{
		case 10 : return 'десятая';
		case 11 : return 'одиннадцатая';
		case 12 : return 'двенадцатая';
		case 13 : return 'тринадцатая';
		case 14 : return 'четырнадцатая';
		case 15 : return 'пятнадцатая';
		case 16 : return 'шестнадцатая';
		case 17 : return 'семнадцатая';
		case 18 : return 'восемнадцатая';
		case 19 : return 'девятнадцатая';
	}
}
function get_chapter_name_3($digit, $zero) 
{
	switch ($digit)
	{
		case 2 : return $zero ? 'двадцатая'     : 'двадцать';
		case 3 : return $zero ? 'тридцатая'     : 'тридцать';
		case 4 : return $zero ? 'сороковая'     : 'сорок';
		case 5 : return $zero ? 'пятидесятая'   : 'пятьдесят';
		case 6 : return $zero ? 'шестидесятая'  : 'шестьдесят';
		case 7 : return $zero ? 'семидесятая'   : 'семьдесят';
		case 8 : return $zero ? 'восьмидесятая' : 'восемьдесят';
		case 9 : return $zero ? 'девяностая'    : 'девяносто';
	}
}

function get_chapter_name($chapter)
{
	if ( $chapter <= 9 )
		return get_chapter_name_1($chapter);
	
	elseif ( $chapter <= 19 )
		return get_chapter_name_2($chapter);
	
	elseif ( $chapter == 100 )
		return 'сотая';
	
	else
		return ($chapter > 100 ? 'сто ' : '') . get_chapter_name_3( round($chapter / 10), $chapter%10==0 ) . ' ' . get_chapter_name_1($chapter % 10);
}

function get_ps_name_1($digit) 
{
	switch ($digit)
	{
		case 1 : return 'первый';
		case 2 : return 'второй';
		case 3 : return 'третий';
		case 4 : return 'четвертый';
		case 5 : return 'пятый';
		case 6 : return 'шестой';
		case 7 : return 'седьмой';
		case 8 : return 'восьмой';
		case 9 : return 'девятый';
	}
}
function get_ps_name_2($digit) 
{
	switch ($digit)
	{
		case 10 : return 'десятый';
		case 11 : return 'одиннадцатый';
		case 12 : return 'двенадцатый';
		case 13 : return 'тринадцатый';
		case 14 : return 'четырнадцатый';
		case 15 : return 'пятнадцатый';
		case 16 : return 'шестнадцатый';
		case 17 : return 'семнадцатый';
		case 18 : return 'восемнадцатый';
		case 19 : return 'девятнадцатый';
	}
}

function get_ps_name_3($digit, $zero) 
{
	switch ($digit)
	{
		case 2 : return $zero ? 'двадцатый'     : 'двадцать';
		case 3 : return $zero ? 'тридцатый'     : 'тридцать';
		case 4 : return $zero ? 'сороковой'     : 'сорок';
		case 5 : return $zero ? 'пятидесятый'   : 'пятьдесят';
		case 6 : return $zero ? 'шестидесятый'  : 'шестьдесят';
		case 7 : return $zero ? 'семидесятый'   : 'семьдесят';
		case 8 : return $zero ? 'восьмидесятый' : 'восемьдесят';
		case 9 : return $zero ? 'девяностый'    : 'девяносто';
	}
}
function get_ps_name($chapter)
{
	if ( $chapter <= 9 )
		return get_ps_name_1($chapter);
	
	elseif ( $chapter <= 19 )
		return get_ps_name_2($chapter);
	
	elseif ( $chapter == 100 )
		return 'сотый';
	
	else
		return ($chapter > 100 ? 'сто ' : '') . get_ps_name_3( round($chapter / 10), $chapter%10==0 ) . ' ' . get_ps_name_1($chapter % 10);
}

function create_chapter_plain($voice, $translationArrayBookChapter, $book, $chapter, $lang, $filename)
{
	$str = '';
	
	if ( $chapter == 1 ) {
		$book_info = get_book_info($book);
		// вообще для каждого перевода своя система походу, как чтец называет книги
		// if ( $book_info['ru_audio'] )
			// $str .= $book_info['ru_audio'] . ".\n";
		// else
			// print_r($book_info);
		$prename = get_book_prename($voice, $book);
		$str .= ($prename ? $prename : $book_info['fullName'][$lang]) . ".\n";
	}
	if ( $book == 19 ) // псалом
		$str .= 'Псалом ' . get_ps_name($chapter) . ".\n";
	else
		$str .= 'Глава ' . get_chapter_name($chapter) . ".\n";
	
	foreach ($translationArrayBookChapter as $verse)
	{
		$str .= $verse['unformatedText'] . "\n";
	}
	
	file_put_contents($filename, $str);
	
	// print("Plain $filename created\n");
}

function get_chapter_audio_url($translation, $voice, $book, $chapter)
{
	return 'https://4bbl.ru/data/' . $voice . '/' . str_pad($book, 2, '0', STR_PAD_LEFT) . '/' . str_pad($chapter, 2, '0', STR_PAD_LEFT) . '.mp3';
}

function download_chapter_audio($translation, $voice, $book, $chapter, $mode)
{
	$filename = "audio/$translation/$voice/mp3/$book/$chapter.mp3";
	
	if ( !file_exists($filename) or $mode == 'MODE_REPLACE' )
	{
		$url = get_chapter_audio_url($translation, $voice, $book, $chapter);
		
		if ( !file_exists(dirname($filename)) )
			mkdir(dirname($filename), 0755, true);

		file_put_contents($filename, file_get_contents($url));
		// print("Audio $filename downloaded\n");
	}
	else {
		// print("Audio $filename already exists\n");
	}
}

function convert_mp3_to_vaw($translation, $voice, $book, $chapter, $mode)
{
	$filename_source = "audio/$translation/$voice/mp3/$book/$chapter.mp3";
	$filename_destination = "audio/$translation/$voice/mfa_input/${book}/${chapter}.wav";
	
	$file_exists = file_exists($filename_destination);
	
	if ( $file_exists and $mode == 'MODE_REPLACE' )
	{
		unlink($filename_destination);
		// print "File $filename_destination deleted\n";
	}
	
	if ( !$file_exists or $mode == 'MODE_REPLACE' ) 
	{
		if ( !file_exists(dirname($filename_destination)) )
			mkdir(dirname($filename_destination), 0777, true);
		
		$cmd_ffmpeg = "docker run --name ffmpeg --rm --volume " . __DIR__ . "/audio:/audio linuxserver/ffmpeg -hide_banner -loglevel error -i /$filename_source /$filename_destination";
		// echo $cmd_ffmpeg . "\n";
		
		$exec_result = exec($cmd_ffmpeg, $output, $retval);
		if ( $exec_result or $retval==0 ) { //
			// print("File $filename_destination created\n"); 
		}
		else {
			if ( $output )
				print_r($output);
		}
	}
	else {
		// print("File $filename_destination already exists\n");
	}
}

function rmdir_recursive($path) {
	if (is_file($path)) return unlink($path);
	if (is_dir($path)) {
		foreach(scandir($path) as $p) if (($p!='.') && ($p!='..'))
			rmdir_recursive($path.DIRECTORY_SEPARATOR.$p);
		return rmdir($path); 
    }
	return false;
}

function exec_and_print($cmd, $return_error=False) 
{
	print "CMD: $cmd...";
	
	$result = exec($cmd, $output);
	
	print ($result ? "OK" : "ERROR") . "\n";
	foreach($output as $str) {
		print "    " . $str . "\n\n";
	}
	
	if ( $result )
		return True;
	else
	{
		if ( $return_error )
			return False;
		else
			die();
	}

}
