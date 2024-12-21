<?php

function get_translation_info($translation)
{
	switch ($translation)
	{
		case 'syn' : return ['lang'=>'ru' , 'shortName'=>'SYNO' , 'fullName'=>'Синодальный перевод',         'bibleComDigitCode'=>'167'];
		case 'nrt' : return ['lang'=>'ru' , 'shortName'=>'НРП'  , 'fullName'=>'Новый русский перевод',       'bibleComDigitCode'=>'143'];
		case 'bti' : return ['lang'=>'ru' , 'shortName'=>'BTI'  , 'fullName'=>'Библия в переводе Кулаковых', 'bibleComDigitCode'=>'313'];
		case 'kjv' : return ['lang'=>'ru' , 'shortName'=>'KJV'  , 'fullName'=>'King James Bible',            'bibleComDigitCode'=>'1'];
		case 'cars': return ['lang'=>'ru' , 'shortName'=>'CARS' , 'fullName'=>'Восточный перевод',           'bibleComDigitCode'=>'385'];
	}
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
				'link'                     => $link_4bbl,
				'link_template'            => 'https://4bbl.ru/data/syn-bondarenko/{book_zerofill}/{chapter_zerofill}.mp3'
			];
		case 'new-russian' : 
			return [
				'name'                     => 'Новый русский перевод', 
				'isMusic'                  => 1, 
				'description'              => '',
				'readBookNames'            => 0,
				'readBookNamesAllChapters' => 0,
				'readChapterNumbers'       => 0,
				'readTitles'               => 1,
				'link'                     => $link_4bbl,
				'link_template'            => 'https://4bbl.ru/data/new-russian/{book_zerofill}/{chapter_zerofill}.mp3'
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
				'link'                     => $link_4bbl,
				'link_template'            => 'https://4bbl.ru/data/bti-prozorovsky/{book_zerofill}/{chapter_zerofill}.mp3'
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
				'link'                     => 'https://mp3.only.bible/rst/%voice$s/%bookCode$s-%chapter$s.mp3',
				'link_template'            => 'https://mp3.only.bible/rst/prudovsky/{book_alias}-{chapter}.mp3'
			];
		case 'cars':
			return [
				'name'                     => 'Чтец неизвестен', 
				'isMusic'                  => 0, 
				'description'              => 'К сожалению, найти информацию о том, кто так замечательно читает этот текст, в публичных источниках не удалось.',
				'readBookNames'            => 1,
				'readBookNamesAllChapters' => 1,
				'readChapterNumbers'       => 1,
				'readTitles'               => 0,
				'link'                     => 'https://res.cloudinary.com/telosmedia-platform/video/upload/v1698132873/telosmedia-platform/tenant-files/cars/audio/books/%bookCodeUpper$s/chapters/%chapter$s/%bookCodeUpper$s-%chapter$s.mp3',
				'link_template'            => 'https://res.cloudinary.com/telosmedia-platform/video/upload/v1698132873/telosmedia-platform/tenant-files/cars/audio/books/{book_alias_upper}/chapters/{chapter}/{book_alias_upper}-{chapter}.mp3'
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
		case 'cars':
			// надо отслушать и настроить
			$base_ru_template[1] = 'Начало';
			$base_ru_template[2 ] = 'Исход';
			$base_ru_template[3 ] = 'Левит';
			$base_ru_template[4 ] = 'Числа';
			$base_ru_template[5 ] = 'Второзаконие';
			$base_ru_template[6 ] = 'Иешуа';
			$base_ru_template[7 ] = 'Судьи';
			$base_ru_template[8 ] = 'Руфь';
			$base_ru_template[9 ] = '1 Царств';
			$base_ru_template[10] = '2 Царств';
			$base_ru_template[11] = '3 Царств';
			$base_ru_template[12] = '4 Царств';
			$base_ru_template[13] = '1 Летопись';
			$base_ru_template[14] = '2 Летопись';
			$base_ru_template[15] = 'Узайр';
			$base_ru_template[16] = 'Неемия';
			$base_ru_template[17] = 'Есфирь';
			$base_ru_template[18] = 'Аюб';
			$base_ru_template[19] = 'Забур';
			$base_ru_template[20] = 'Мудрые изречения';
			$base_ru_template[21] = 'Размышления';
			$base_ru_template[22] = 'Песнь Сулеймана';
			$base_ru_template[23] = 'Исаия';
			$base_ru_template[24] = 'Иеремия';
			$base_ru_template[25] = 'Плач';
			$base_ru_template[26] = 'Езекиил';
			$base_ru_template[27] = 'Даниял';
			$base_ru_template[28] = 'Осия';
			$base_ru_template[29] = 'Иоиль';
			$base_ru_template[30] = 'Амос';
			$base_ru_template[31] = 'Авдий';
			$base_ru_template[32] = 'Юнус';
			$base_ru_template[33] = 'Михей';
			$base_ru_template[34] = 'Наум';
			$base_ru_template[35] = 'Аввакум';
			$base_ru_template[36] = 'Софония';
			$base_ru_template[37] = 'Аггей';
			$base_ru_template[38] = 'Закария';
			$base_ru_template[39] = 'Малахия';
			$base_ru_template[40] = 'Матай';
			$base_ru_template[41] = 'Марк';
			$base_ru_template[42] = 'Лука';
            $base_ru_template[43] = 'Иохан';
            $base_ru_template[44] = 'Деяния';
            $base_ru_template[45] = 'Якуб';
            $base_ru_template[46] = '1 Петира';
            $base_ru_template[47] = '2 Петира';
            $base_ru_template[48] = '1 Иохана';
            $base_ru_template[49] = '2 Иохана';
            $base_ru_template[50] = '3 Иохана';
            $base_ru_template[51] = 'Иуда';
            $base_ru_template[52] = 'Римлянам';
            $base_ru_template[53] = '1 Коринфянам';
            $base_ru_template[54] = '2 Коринфянам';
            $base_ru_template[55] = 'Галатам';
            $base_ru_template[56] = 'Эфесянам';
            $base_ru_template[57] = 'Филиппийцам';
            $base_ru_template[58] = 'Колоссянам';
            $base_ru_template[59] = '1 Фессалоникийцам';
            $base_ru_template[60] = '2 Фессалоникийцам';
            $base_ru_template[61] = '1 Тиметею';
            $base_ru_template[62] = '2 Тиметею';
            $base_ru_template[63] = 'Титу';
            $base_ru_template[64] = 'Филимону';
            $base_ru_template[65] = 'Евреям';
            $base_ru_template[66] = 'Откровение';
			
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
