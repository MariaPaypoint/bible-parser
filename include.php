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
	
	return '';
}

function get_book_prename($voice, $book_index) {
	switch ($voice)
	{
		case 'bondarenko':
		{
			switch ($book_index)
			{
				case 1 : return 'Первая книга Моисеева Бытие';
				case 2 : return '';
				case 3 : return '';
				case 4 : return '';
				case 5 : return '';
				
				case 6 : return '';
				case 7 : return '';
				case 8 : return '';
				case 9 : return '';
				case 10: return '';
				case 11: return '';
				case 12: return '';
				case 13: return '';
				case 14: return '';
				case 15: return '';
				case 16: return '';
				case 17: return '';
				
				case 18: return '';
				case 19: return '';
				case 20: return '';
				case 21: return '';
				case 22: return '';
				
				case 23: return '';
				case 24: return '';
				case 25: return '';
				case 26: return '';
				case 27: return '';
				
				case 28: return '';
				case 29: return '';
				case 30: return '';
				case 31: return '';
				case 32: return '';
				case 33: return '';
				case 34: return '';
				case 35: return '';
				case 36: return '';
				case 37: return '';
				case 38: return '';
				case 39: return '';
				
				case 40: return '';
				case 41: return '';
				case 42: return '';
				case 43: return '';
				case 44: return '';
				
				case 45: return '';
				case 46: return '';
				case 47: return '';
				case 48: return '';
				case 49: return '';
				case 50: return '';
				case 51: return '';
				
				case 52: return '';
				case 53: return '';
				case 54: return '';
				case 55: return '';
				case 56: return '';
				case 57: return '';
				case 58: return '';
				case 59: return '';
				case 60: return '';
				case 61: return '';
				case 62: return '';
				case 63: return '';
				case 64: return '';
				case 65: return '';
				
				case 66: return '';
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

function determine_audio_translation()
{
	global $argv;
	
	if ( !isset($argv[1]) )
		die("\nERROR: Set translation var! \nExample usage: \n$ php timecodes.php syn syn-bondarenko\n\n");
	
	$translation = $argv[1];
	$filename = "bible/$translation.json";
	
	if ( !file_exists($filename) )
		die("Translation not found (expected: $filename)\n\n");
	
	return $translation;
}

function determine_voice_4bbl($translation)
{
	global $argv;
	
	if ( !isset($argv[2]) )
		die("\nERROR: Set voice var! \nExample usage: \n$ php timecodes.php syn syn-bondarenko\n\n");
	
	$voice = $argv[2];
	
	$url = get_chapter_audio_url($translation, $voice, '01', '01');
	
	if ( !file_get_contents($url) )
		die("Voice not found (example url: $url)\n\n");
	
	return $voice;
}

function determine_mode()
{
	global $argv;
	
	if ( !isset($argv[3]) )
		die("Mode is not set (wait one of: MODE_REPLACE, MODE_CHANGE)\n\n");
	
	$mode = $argv[3];
	
	if ( !in_array($mode, ['MODE_REPLACE', 'MODE_CHANGE']) )
		die("Unknown mode: $mode (wait one of: MODE_REPLACE, MODE_CHANGE)\n\n");
	
	return $mode;
}

function determine_step()
{
	global $argv;
	
	if ( !isset($argv[4]) )
		die("Step is not set (wait one of: 1, 2)\n\n");
	
	$step = $argv[4];
	
	if ( !in_array($step, ['1', '2']) )
		die("Unknown step: $step (wait one of: 1, 2)\n\n");
	
	return $step;
}

// для аудио


function get_translation_array($translation)
{
	$filename = "bible/$translation.json";
	$translationArray = json_decode(file_get_contents($filename), true);
	
	return $translationArray;
}

function get_chapter_name_1($digit) 
{
	switch ($digit)
	{
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
function get_chapter_name_3($digit) 
{
	switch ($digit)
	{
		case 2 : return 'двадцать';
		case 3 : return 'тридцать';
		case 4 : return 'сорок';
		case 5 : return 'пятьдесят';
		case 6 : return 'шестьдесят';
		case 7 : return 'семьдесят';
		case 8 : return 'восемьдесят';
		case 9 : return 'девяносто';
	}
}

function get_chapter_name($chapter)
{
	if ( $chapter <= 9 )
		return get_chapter_name_1($chapter);
	
	if ( $chapter <= 19 )
		return get_chapter_name_2($chapter);
	
	if ( $chapter <= 99 )
		return get_chapter_name_3( round($chapter / 10) ) . ' ' . get_chapter_name_1($chapter % 10);
	
	return 'сто ' . get_chapter_name_3( round($chapter / 10) ) . ' ' . get_chapter_name_1($chapter % 10);
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

function get_ps_name($chapter)
{
	if ( $chapter <= 9 )
		return get_ps_name_1($chapter);
	
	if ( $chapter <= 19 )
		return get_ps_name_2($chapter);
	
	if ( $chapter <= 99 )
		return get_chapter_name_3( round($chapter / 10) ) . ' ' . get_ps_name_1($chapter % 10);
	
	return 'сто ' . get_chapter_name_3( round($chapter / 10) ) . ' ' . get_ps_name_1($chapter % 10);
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
		$str .= $verse['text'] . "\n";
	}
	
	file_put_contents($filename, $str);
	
	// print("Plain $filename created\n");
}

function get_chapter_audio_url($translation, $voice, $book, $chapter)
{
	return 'https://4bbl.ru/data/' . $translation . '-' .$voice . '/' . str_pad($book, 2, '0', STR_PAD_LEFT) . '/' . str_pad($chapter, 2, '0', STR_PAD_LEFT) . '.mp3';
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
