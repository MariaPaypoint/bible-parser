<?php

require "config.php";

function get_db_cursor()
{
	$mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_SCHEMA, MYSQL_PORT);
	return $mysqli;
}

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

function get_voice_info($voice)
{
	$link_4bbl = 'https://4bbl.ru/data/%voice$s/%book0$s/%chapter0$s.mp3';
	// $link_onlybible = 'https://mp3.only.bible/%translation$s/%voice$s/%bookCode$s-%chapter$s.mp3';
	
	switch ($voice)
	{
		case 'bondarenko' : 
			return [
				'name'                     => 'Александр Бондаренко', 
				'isMusic'                  => 1, 
				'description'              => 'Текст читает Александр Викторович Бондаренко — украинский актёр театра и кино, народный артист Украины. Жил с нами в период с 1960 по 2013 год. Его голосом не озвучено лишь книги Паралипоменон и Песнь Песней. Эти книги заменены на чтение Игоря Козлова. Особенность этой озвучки — драматическое музыкальное сопровождение.',
				'readBookNames'            => 1,
				'readBookNamesAllChapters' => 0,
				'readChapterNumbers'       => 1,
				'readTitles'               => 0,
				'link'                     => $link_4bbl
			];
		case 'nrt' : 
			return [
				'name'                     => 'Новый русский перевод', 
				'isMusic'                  => 1, 
				'description'              => '',
				'readBookNames'            => 0,
				'readBookNamesAllChapters' => 0,
				'readChapterNumbers'       => 0,
				'readTitles'               => 1,
				'link'                     => $link_4bbl
			];
		case 'prudovsky' : 
			return [
				'name'                     => 'Илья Прудовский', 
				'isMusic'                  => 1, 
				'description'              => 'Текст читает Илья Ефимович Прудовский - легендарный диктор советского радио.',
				'readBookNames'            => 1,
				'readBookNamesAllChapters' => 1, // еще и разные названия в главах
				'readChapterNumbers'       => 1,
				'readTitles'               => 0,
				'link'                     => 'https://mp3.only.bible/rst/%voice$s/%bookCode$s-%chapter$s.mp3'
			];
		case 'bti-prozorovsky' : 
			return [
				'name'                     => 'Никита Семёнов-Прозоровский', 
				'isMusic'                  => 1, 
				'description'              => 'Текст читает Никита Юрьевич Семёнов-Прозоровский - советский и российский актёр театра, кино и дубляжа, диктор, бард, певец. В разное время являлся «голосом» телеканалов «НТВ», киноканалов «НТВ-Плюс», «Discovery», «Восьмого канала».',
				'readBookNames'            => 1,
				'readBookNamesAllChapters' => 0,
				'readChapterNumbers'       => 1,
				'readTitles'               => 0,
				'link'                     => $link_4bbl
			];
		default:
			die("Incorrect voice [$voice].\n");
	}
	
	
}

function get_book_prename($voice, $book_index) {
	$base_ru_template = [
		1  => 'Первая книга Моисеева Бытие',
		2  => 'Вторая книга Моисеева Исход',
		3  => 'Третья книга Моисеева Левит',
		4  => 'Четвертая книга Моисеева Числа',
		5  => 'Пятая книга Моисеева Второзаконие',
		
		6  => 'Книга Иисуса Навина',
		7  => 'Книга судей Израилевых',
		8  => 'Книга Руфь',
		9  => 'Первая книга Царств',
		10 => 'Вторая книга Царств',
		11 => 'Третья книга Царств',
		12 => 'Четвертая книга Царств',
		13 => 'Первая книга Паралипоменон',
		14 => 'Вторая книга Паралипоменон',
		15 => 'Книга Ездры',
		16 => 'Книга Неемии',
		17 => 'Книга Есфирь',
		
		18 => 'Книга Иова',
		19 => 'Псалтырь',
		20 => 'Книга Притчей Соломоновых',
		21 => 'Книга Екклесиаста или Проповедника',
		22 => 'Книга Песни Песней Соломона',
		
		23 => 'Книга пророка Исаии',
		24 => 'Книга пророка Иеремии',
		25 => 'Книга Плач Иеремии',
		26 => 'Книга пророка Иезекииля',
		27 => 'Книга пророка Даниила',
		
		28 => 'Книга пророка Осии',
		29 => 'Книга пророка Иоиля',
		30 => 'Книга пророка Амоса',
		31 => 'Книга пророка Авдия',
		32 => 'Книга пророка Ионы',
		33 => 'Книга пророка Михея',
		34 => 'Книга пророка Наума',
		35 => 'Книга пророка Аввакума',
		36 => 'Книга пророка Софонии',
		37 => 'Книга пророка Аггея',
		38 => 'Книга пророка Захарии',
		39 => 'Книга пророка Малахии',
		
		40 => 'Евангелие от Матфея',
		41 => 'Евангелие от Марка',
		42 => 'Евангелие от Луки',
		43 => 'Евангелие от Иоанна',
		44 => 'Деяния Святых апостолов',
		
		45 => 'Послание Иакова',
		46 => 'Первое послание апостола Петра',
		47 => 'Второе послание апостола Петра',
		48 => 'Первое послание апостола Иоанна',
		49 => 'Второе послание апостола Иоанна',
		50 => 'Третье послание апостола Иоанна',
		51 => 'Послание Иуды',
		
		52 => 'Послание к Римлянам Святого апостола Павла',
		53 => 'Первое послание к Коринфянам Святого апостола Павла',
		54 => 'Второе послание к Коринфянам Святого апостола Павла',
		55 => 'Послание к Галатам Святого апостола Павла',
		56 => 'Послание к Ефесянам Святого апостола Павла',
		57 => 'Послание к Филиппийцам Святого апостола Павла',
		58 => 'Послание к Колоссянам Святого апостола Павла',
		59 => 'Первое послание к Фессалоникийцам Святого апостола Павла',
		60 => 'Второе послание к Фессалоникийцам Святого апостола Павла',
		61 => 'Первое послание к Тимофею Святого апостола Павла',
		62 => 'Второе послание к Тимофею Святого апостола Павла',
		63 => 'Послание к Титу Святого апостола Павла',
		64 => 'Филимону Святого апостола Павла',
		65 => 'Послание к Евреям Святого апостола Павла',
		
		66 => 'Откровение Святого Иоанна Богослова'
	];
	switch ($voice)
	{
		case 'syn-bondarenko':
		{
			$base_ru_template[2]  = 'Вторая книга Моисея Исход';
			$base_ru_template[10] = '';
			$base_ru_template[28] = 'Малые пророки. Осия';
			$base_ru_template[45] = 'Соборные послания Святого апостола Иакова';
			$base_ru_template[46] = 'Первое соборное послание Святого апостола Петра';
			$base_ru_template[47] = 'Второе соборное послание Святого апостола Петра';
			$base_ru_template[48] = 'Первое соборное послание Святого апостола Иоанна Богослова';
			$base_ru_template[49] = 'Второе соборное послание Святого апостола Иоанна Богослова';
			$base_ru_template[50] = 'Третье соборное послание Святого апостола Иоанна Богослова';
			$base_ru_template[51] = 'Соборное послание Святого апостола Иуды';

			return $base_ru_template[$book_index];
		}
		case 'bti-prozorovsky':
			$base_ru_template[7]  = 'Книга судей Израиля';
			$base_ru_template[8]  = 'Книга Руфи';
			$base_ru_template[17] = 'Книга Есфири';
			$base_ru_template[22] = 'Песнь Песней Соломона';
			$base_ru_template[25] = 'Плач Иеремии';
			$base_ru_template[40] = 'Евангелие по Матфею';
			$base_ru_template[41] = 'Евангелие по Марку';
			$base_ru_template[42] = 'Евангелие по Луке';
			$base_ru_template[43] = 'Евангелие по Иоанну';
			$base_ru_template[44] = 'Деяния апостолов';

			$base_ru_template[52] = 'Послание апостола Павла христианам в Риме';
			$base_ru_template[53] = 'Первое послание апостола Павла христианам в Коринфе';
			$base_ru_template[54] = 'Второе послание апостола Павла христианам в Коринфе';
			$base_ru_template[55] = 'Послание апостола Павла христианам в Галатии';
			$base_ru_template[56] = 'Послание апостола Павла христианам в Ефесе';
			$base_ru_template[57] = 'Послание апостола Павла христианам в Филиппах';
			$base_ru_template[58] = 'Послание апостола Павла христианам в Колоссах';
			$base_ru_template[59] = 'Первое послание апостола Павла христианам в Фессалониках';
			$base_ru_template[60] = 'Второе послание апостола Павла христианам в Фессалониках';
			$base_ru_template[61] = 'Первое послание апостола Павла Тимофею';
			$base_ru_template[62] = 'Второе послание апостола Павла Тимофею';
			$base_ru_template[63] = 'Послание апостола Павла Титу';
			$base_ru_template[64] = 'Послание апостола Павла Филимону';
			$base_ru_template[65] = 'Послание к Евреям';
			$base_ru_template[66] = 'Откровение Иоанна';

			return $base_ru_template[$book_index];
		default:
			die("Book prenames are not found for voice $voice \n");
	}
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
	
	return false;
}

// получение входящих параметров

function determine_text_translation($position=1)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("\nERROR: Set translation var! \nExample usage: \n$ php timecodes.php syn bondarenko\n\n");
	
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
		die("\nERROR: Set voice var! \nExample usage: \n$ php timecodes.php syn bondarenko\n\n");
	
	$voice = $argv[$position];
	
	$url = get_chapter_audio_url($translation, $voice, 1, 1);
	
	if ( !file_get_contents($url) )
		die("Voice not found (example url: $url)\n\n");
	
	return $voice;
}

function determine_mode($position=3)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("Mode is not set (wait one of: MODE_REPLACE, MODE_CHANGE, MODE_FINISH)\n\n");
	
	$mode = $argv[$position];
	
	if ( !in_array($mode, ['MODE_REPLACE', 'MODE_CHANGE', 'MODE_FINISH']) )
		die("Unknown mode: $mode (wait one of: MODE_REPLACE, MODE_CHANGE, MODE_FINISH)\n\n");
	
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

function sprintfn ($format, array $args = array()) {
    // map of argument names to their corresponding sprintf numeric argument value
    $arg_nums = array_slice(array_flip(array_keys(array(0 => 0) + $args)), 1);

    // find the next named argument. each search starts at the end of the previous replacement.
    for ($pos = 0; preg_match('/(?<=%)([a-zA-Z_]\w*)(?=\$)/', $format, $match, PREG_OFFSET_CAPTURE, $pos);) {
        $arg_pos = $match[0][1];
        $arg_len = strlen($match[0][0]);
        $arg_key = $match[1][0];

        // programmer did not supply a value for the named argument found in the format string
        if (! array_key_exists($arg_key, $arg_nums)) {
            user_error("sprintfn(): Missing argument '$arg_key'", E_USER_WARNING);
            return false;
        }

        // replace the named argument with the corresponding numeric one
        $format = substr_replace($format, $replace = $arg_nums[$arg_key], $arg_pos, $arg_len);
        $pos = $arg_pos + strlen($replace); // skip to end of replacement for next iteration
    }

    return vsprintf($format, array_values($args));
}

function get_chapter_audio_url($translation, $voice, $book, $chapter)
{
	$voice_info = get_voice_info($voice);
	$book_info = get_book_info($book);
	
	$link = sprintfn($voice_info['link'], [
		'voice'       => $voice,
		'book'        => $book,
		'chapter'     => $chapter,
		'book0'       => str_pad($book, 2, '0', STR_PAD_LEFT),
		'chapter0'    => str_pad($chapter, 2, '0', STR_PAD_LEFT),
		'bookCode'    => $book_info['code'],
		'translation' => $translation
	]);
	return $link;
	//return 'https://4bbl.ru/data/' . $voice . '/' . str_pad($book, 2, '0', STR_PAD_LEFT) . '/' . str_pad($chapter, 2, '0', STR_PAD_LEFT) . '.mp3';
}

function download_chapter_audio($translation, $voice, $book, $chapter, $mode)
{
	$book0    = str_pad($book, 2, '0', STR_PAD_LEFT);
	$chapter0 = str_pad($chapter, 2, '0', STR_PAD_LEFT);
	
	$filename = "audio/$translation/$voice/mp3/$book0/$chapter0.mp3";
	
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

function create_dir777_if_not_exists($dirname, $clear=False) 
{
	if ( $clear && file_exists($dirname) )
		rmdir_recursive($dirname);

	if ( !file_exists($dirname) )
		mkdir($dirname, 0777, true);
	
	chmod($dirname, 0777);
}

function convert_mp3_to_vaw($translation, $voice, $book, $chapter, $mode)
{
	$filename_source = "audio/$translation/$voice/mp3/$book/$chapter.mp3";
	$filename_destination = "audio/$translation/$voice/mfa_input/$book/$chapter.wav";
	
	$file_exists = file_exists($filename_destination);
	
	if ( $file_exists and $mode == 'MODE_REPLACE' )
	{
		unlink($filename_destination);
		// print "File $filename_destination deleted\n";
	}
	
	if ( !$file_exists or $mode == 'MODE_REPLACE' ) 
	{
		create_dir777_if_not_exists(dirname($filename_destination));
		
		$cmd_ffmpeg = "docker run --name ffmpeg --rm --volume " . __DIR__ . "/audio:/audio linuxserver/ffmpeg -hide_banner -loglevel error -i /$filename_source /$filename_destination";
		// die($cmd_ffmpeg . "\n");
		
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

function containsLetter($str) {
    // Регулярное выражение для поиска хотя бы одной буквы (латиница и кириллица)
    return preg_match('/[a-zA-Zа-яА-Я]/u', $str) === 1;
}

function removeBrackets($str) {
    // Удаляем только скобки, но оставляем содержимое внутри
    $str = str_replace(['[', ']', '(', ')', '<', '>'], '', $str);
    return $str;
}

function deleteTxtFiles($directory) {
    // Проверяем, существует ли директория
    if (!is_dir($directory)) {
        echo "Указанная директория не существует.";
        return;
    }

    // Добавляем разделитель директорий, если его нет
    if (substr($directory, -1) !== DIRECTORY_SEPARATOR) {
        $directory .= DIRECTORY_SEPARATOR;
    }

    // Получаем все файлы с расширением .txt в директории
    $files = glob($directory . '*.txt');

    // Проходим по каждому файлу и удаляем его
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
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
