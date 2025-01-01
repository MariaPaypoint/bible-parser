<?php

function get_translation_info($translation)
{
    $translations = [
        'syn'  => ['lang' => 'ru', 'shortName' => 'SYNO', 'fullName' => 'Синодальный перевод', 'bibleComDigitCode' => '400'],
        'nrt'  => ['lang' => 'ru', 'shortName' => 'НРП',  'fullName' => 'Новый русский перевод', 'bibleComDigitCode' => '143'],
        'bti'  => ['lang' => 'ru', 'shortName' => 'BTI',  'fullName' => 'Библия в переводе Кулаковых', 'bibleComDigitCode' => '313'],
        'kjv'  => ['lang' => 'ru', 'shortName' => 'KJV',  'fullName' => 'King James Bible', 'bibleComDigitCode' => '1'],
        'cars' => ['lang' => 'ru', 'shortName' => 'CARS', 'fullName' => 'Восточный перевод', 'bibleComDigitCode' => '385'],

        'niv'  => ['lang' => 'en', 'shortName' => 'NIV', 'fullName' => 'New International Version', 'bibleComDigitCode' => '111'],
    ];

    if (array_key_exists($translation, $translations)) {
        return $translations[$translation];
    }

    throw new InvalidArgumentException('Incorrect translation.');
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
				'link'                     => 'https://4bbl.ru/data/syn-bondarenko/%book0$s/%chapter0$s.mp3',
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

			case 'dramatized':
				return [
					'name'                     => 'NIV by Dramatized', 
					'isMusic'                  => 1, 
					'description'              => "Scriptures taken from the Holy Bible, New International Version®, NIV®. Copyright © 1973, 1978, 1984, 2011 by Biblica, Inc.® Used by permission of Zondervan. All rights reserved worldwide. www.zondervan.com The “NIV” and “New International Version” are trademarks registered in the United States Patent and Trademark Office by Biblica, Inc.®",
					'readBookNames'            => 1,
					'readBookNamesAllChapters' => 0,
					'readChapterNumbers'       => 1,
					'readTitles'               => 0,
					'link'                     => 'https://stream.biblegateway.com/bibles/32/niv-dramatized/%bookCode2$s.%chapter$s.mp3',
					'link_template'            => 'https://stream.biblegateway.com/bibles/32/niv-dramatized/{book_code2}.{chapter}.mp3',
					'source'				   => 'https://www.biblegateway.com/audio/dramatized/niv/'
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
	$base_en_template = [
		1  => 'Genesis',
		2  => 'Exodus',
		3  => 'Leviticus',
		4  => 'Numbers',
		5  => 'Deuteronomy',
		
		6  => 'Joshua',
		7  => 'Judges',
		8  => 'Ruth',
		9  => '1 Samuel',
		10 => '2 Samuel',
		11 => '1 Kings',
		12 => '2 Kings',
		13 => '1 Chronicles',
		14 => '2 Chronicles',
		15 => 'Ezra',
		16 => 'Nehemiah',
		17 => 'Esther',
		
		18 => 'Job',
		19 => 'Psalms',
		20 => 'Proverbs',
		21 => 'Ecclesiastes',
		22 => 'Song of Solomon',
		
		23 => 'Isaiah',
		24 => 'Jeremiah',
		25 => 'Lamentations',
		26 => 'Ezekiel',
		27 => 'Daniel',
		
		28 => 'Hosea',
		29 => 'Joel',
		30 => 'Amos',
		31 => 'Obadiah',
		32 => 'Jonah',
		33 => 'Micah',
		34 => 'Nahum',
		35 => 'Habakkuk',
		36 => 'Zephaniah',
		37 => 'Haggai',
		38 => 'Zechariah',
		39 => 'Malachi',
		
		40 => 'Matthew',
		41 => 'Mark',
		42 => 'Luke',
		43 => 'John',
		44 => 'Acts',
		
		45 => 'James',
		46 => '1 Peter',
		47 => '2 Peter',
		48 => '1 John',
		49 => '2 John',
		50 => '3 John',
		51 => 'Jude',
		
		52 => 'Romans',
		53 => '1 Corinthians',
		54 => '2 Corinthians',
		55 => 'Galatians',
		56 => 'Ephesians',
		57 => 'Philippians',
		58 => 'Colossians',
		59 => '1 Thessalonians',
		60 => '2 Thessalonians',
		61 => '1 Timothy',
		62 => '2 Timothy',
		63 => 'Titus',
		64 => 'Philemon',
		65 => 'Hebrews',
		
		66 => 'Revelation'
	];
	
	switch ($voice)
	{
		case 'bondarenko':
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
		case 'dramatized':
			return $base_en_template[$book_index];
		default:
			die("Book prenames are not found for voice $voice \n");
	}
}

function get_book_info($book_index)
{
	switch ($book_index)
	{
		case 1 : return ['code'=>'gen', 'shortName'=>['en'=>'Gen'   , 'ru'=>'Быт'  ], 'fullName'=>['en'=>'Genesis'         , 'ru'=>'Бытие'				], 'code2'=>'Gen'	];
		case 2 : return ['code'=>'exo', 'shortName'=>['en'=>'Ex'    , 'ru'=>'Исх'  ], 'fullName'=>['en'=>'Exodus'          , 'ru'=>'Исход'				], 'code2'=>'Exod'	];
		case 3 : return ['code'=>'lev', 'shortName'=>['en'=>'Lev'   , 'ru'=>'Лев'  ], 'fullName'=>['en'=>'Leviticus'       , 'ru'=>'Левит'				], 'code2'=>'Lev'	];
		case 4 : return ['code'=>'num', 'shortName'=>['en'=>'Num'   , 'ru'=>'Чис'  ], 'fullName'=>['en'=>'Numbers'         , 'ru'=>'Числа'				], 'code2'=>'Num'	];
		case 5 : return ['code'=>'deu', 'shortName'=>['en'=>'Deut'  , 'ru'=>'Втор' ], 'fullName'=>['en'=>'Deuteronomy'     , 'ru'=>'Второзаконие'		], 'code2'=>'Deut'	];

		case 6 : return ['code'=>'jos', 'shortName'=>['en'=>'Josh'  , 'ru'=>'Нав'  ], 'fullName'=>['en'=>'Joshua'          , 'ru'=>'Иисус Навин'		], 'code2'=>'Josh'	];
		case 7 : return ['code'=>'jdg', 'shortName'=>['en'=>'Judg'  , 'ru'=>'Суд'  ], 'fullName'=>['en'=>'Judges'          , 'ru'=>'Судьи'				], 'code2'=>'Judg'	];
		case 8 : return ['code'=>'rut', 'shortName'=>['en'=>'Ruth'  , 'ru'=>'Руфь' ], 'fullName'=>['en'=>'Ruth'            , 'ru'=>'Руфь'				], 'code2'=>'Ruth'	];
		case 9 : return ['code'=>'1sa', 'shortName'=>['en'=>'1Sam'  , 'ru'=>'1Цар' ], 'fullName'=>['en'=>'1 Samuel'        , 'ru'=>'1 Царств'			], 'code2'=>'1Sam'	];
		case 10: return ['code'=>'2sa', 'shortName'=>['en'=>'2Sam'  , 'ru'=>'2Цар' ], 'fullName'=>['en'=>'2 Samuel'        , 'ru'=>'2 Царств'			], 'code2'=>'2Sam'	];
		case 11: return ['code'=>'1ki', 'shortName'=>['en'=>'1Kings', 'ru'=>'3Цар' ], 'fullName'=>['en'=>'1 Kings'         , 'ru'=>'3 Царств'			], 'code2'=>'1Kgs'	];
		case 12: return ['code'=>'2ki', 'shortName'=>['en'=>'2Kings', 'ru'=>'4Цар' ], 'fullName'=>['en'=>'2 Kings'         , 'ru'=>'4 Царств'			], 'code2'=>'2Kgs'	];
		case 13: return ['code'=>'1ch', 'shortName'=>['en'=>'1Chron', 'ru'=>'1Пар' ], 'fullName'=>['en'=>'1 Chronicles'    , 'ru'=>'1 Паралипоменон'	], 'code2'=>'1Chr'	];
		case 14: return ['code'=>'2ch', 'shortName'=>['en'=>'2Chron', 'ru'=>'2Пар' ], 'fullName'=>['en'=>'2 Chronicles'    , 'ru'=>'2 Паралипоменон'	], 'code2'=>'2Chr'	];
		case 15: return ['code'=>'ezr', 'shortName'=>['en'=>'Ezra'  , 'ru'=>'Езд'  ], 'fullName'=>['en'=>'Ezra'            , 'ru'=>'Ездра'				], 'code2'=>'Ezra'	];
		case 16: return ['code'=>'neh', 'shortName'=>['en'=>'Neh'   , 'ru'=>'Неем' ], 'fullName'=>['en'=>'Nehemiah'        , 'ru'=>'Неемия'				], 'code2'=>'Neh'	];
		case 17: return ['code'=>'est', 'shortName'=>['en'=>'Esther', 'ru'=>'Есф'  ], 'fullName'=>['en'=>'Esther'          , 'ru'=>'Есфирь'				], 'code2'=>'Esth'	];

		case 18: return ['code'=>'job', 'shortName'=>['en'=>'Job'   , 'ru'=>'Иов'  ], 'fullName'=>['en'=>'Job'             , 'ru'=>'Иов'				], 'code2'=>'Job'	];
		case 19: return ['code'=>'psa', 'shortName'=>['en'=>'Ps'    , 'ru'=>'Пс'   ], 'fullName'=>['en'=>'Psalms'          , 'ru'=>'Псалтирь'			], 'code2'=>'Ps'	];
		case 20: return ['code'=>'pro', 'shortName'=>['en'=>'Prov'  , 'ru'=>'Прит' ], 'fullName'=>['en'=>'Proverbs'        , 'ru'=>'Притчи'				], 'code2'=>'Prov'	];
		case 21: return ['code'=>'ecc', 'shortName'=>['en'=>'Eccles', 'ru'=>'Еккл' ], 'fullName'=>['en'=>'Ecclesiastes'    , 'ru'=>'Екклесиаст'			], 'code2'=>'Eccl'	];
		case 22: return ['code'=>'sng', 'shortName'=>['en'=>'Song'  , 'ru'=>'Песн' ], 'fullName'=>['en'=>'Song of Solomon' , 'ru'=>'Песни Песней'		], 'code2'=>'Song'	];

		case 23: return ['code'=>'isa', 'shortName'=>['en'=>'Is'    , 'ru'=>'Ис'   ], 'fullName'=>['en'=>'Isaiah'          , 'ru'=>'Исаия'				], 'code2'=>'Isa'	];
		case 24: return ['code'=>'jer', 'shortName'=>['en'=>'Jer'   , 'ru'=>'Иер'  ], 'fullName'=>['en'=>'Jeremiah'        , 'ru'=>'Иеремия'			], 'code2'=>'Jer'	];
		case 25: return ['code'=>'lam', 'shortName'=>['en'=>'Lam'   , 'ru'=>'Плач' ], 'fullName'=>['en'=>'Lamentations'    , 'ru'=>'Плач Иеремии'		], 'code2'=>'Lam'	];
		case 26: return ['code'=>'ezk', 'shortName'=>['en'=>'Ezek'  , 'ru'=>'Иез'  ], 'fullName'=>['en'=>'Ezekiel'         , 'ru'=>'Иезекииль'			], 'code2'=>'Ezek'	];
		case 27: return ['code'=>'dan', 'shortName'=>['en'=>'Dan'   , 'ru'=>'Дан'  ], 'fullName'=>['en'=>'Daniel'          , 'ru'=>'Даниил'				], 'code2'=>'Dan'	];

		case 28: return ['code'=>'hos', 'shortName'=>['en'=>'Hos'   , 'ru'=>'Ос'   ], 'fullName'=>['en'=>'Hosea'           , 'ru'=>'Осия'     			], 'code2'=>'Hos'	];
		case 29: return ['code'=>'jol', 'shortName'=>['en'=>'Joel'  , 'ru'=>'Иоиль'], 'fullName'=>['en'=>'Joel'            , 'ru'=>'Иоиль'    			], 'code2'=>'Joel'	];
		case 30: return ['code'=>'amo', 'shortName'=>['en'=>'Amos'  , 'ru'=>'Амос' ], 'fullName'=>['en'=>'Amos'            , 'ru'=>'Амос'     			], 'code2'=>'Amos'	];
		case 31: return ['code'=>'oba', 'shortName'=>['en'=>'Obad'  , 'ru'=>'Авд'  ], 'fullName'=>['en'=>'Obadiah'         , 'ru'=>'Авдий'    			], 'code2'=>'Obad'	];
		case 32: return ['code'=>'jon', 'shortName'=>['en'=>'Jon'   , 'ru'=>'Иона' ], 'fullName'=>['en'=>'Jonah'           , 'ru'=>'Иона'     			], 'code2'=>'Jonah'	];
		case 33: return ['code'=>'mic', 'shortName'=>['en'=>'Mic'   , 'ru'=>'Мих'  ], 'fullName'=>['en'=>'Micah'           , 'ru'=>'Михей'    			], 'code2'=>'Mic'	];
		case 34: return ['code'=>'nam', 'shortName'=>['en'=>'Nahum' , 'ru'=>'Наум' ], 'fullName'=>['en'=>'Nahum'           , 'ru'=>'Наум'				], 'code2'=>'Nah'	];
        case 35: return ['code'=>'hab', 'shortName'=>['en'=>'Hab'   , 'ru'=>'Авв'  ], 'fullName'=>['en'=>'Habakkuk'        , 'ru'=>'Аввакум'			], 'code2'=>'Hab'	];
        case 36: return ['code'=>'zep', 'shortName'=>['en'=>'Zeph'  , 'ru'=>'Соф'  ], 'fullName'=>['en'=>'Zephaniah'       , 'ru'=>'Софония'			], 'code2'=>'Zeph'	];
        case 37: return ['code'=>'hag', 'shortName'=>['en'=>'Hag'   , 'ru'=>'Агг'  ], 'fullName'=>['en'=>'Haggai'          , 'ru'=>'Аггей'				], 'code2'=>'Hag'	];
        case 38: return ['code'=>'zec', 'shortName'=>['en'=>'Zech'  , 'ru'=>'Зах'  ], 'fullName'=>['en'=>'Zechariah'       , 'ru'=>'Захария'			], 'code2'=>'Zech'	];
        case 39: return ['code'=>'mal', 'shortName'=>['en'=>'Mal'   , 'ru'=>'Мал'  ], 'fullName'=>['en'=>'Malachi'         , 'ru'=>'Малахия'			], 'code2'=>'Mal'	];

        case 40: return ['code'=>'mat', 'shortName'=>['en'=>'Mt'    , 'ru'=>'Мф'   ], 'fullName'=>['en'=>'Matthew'         , 'ru'=>'Евангелие от Матфея'], 'code2'=>'Matt'	];
        case 41: return ['code'=>'mrk', 'shortName'=>['en'=>'Mk'    , 'ru'=>'Мк'   ], 'fullName'=>['en'=>'Mark'            , 'ru'=>'Евангелие от Марка'	], 'code2'=>'Mark'	];
        case 42: return ['code'=>'luk', 'shortName'=>['en'=>'Lk'    , 'ru'=>'Лк'   ], 'fullName'=>['en'=>'Luke'            , 'ru'=>'Евангелие от Луки'	], 'code2'=>'Luke'	];
        case 43: return ['code'=>'jhn', 'shortName'=>['en'=>'Jn'    , 'ru'=>'Ин'   ], 'fullName'=>['en'=>'John'            , 'ru'=>'Евангелие от Иоанна'], 'code2'=>'John'	];
        case 44: return ['code'=>'act', 'shortName'=>['en'=>'Acts'  , 'ru'=>'Деян' ], 'fullName'=>['en'=>'Acts'            , 'ru'=>'Деяния апостолов'	], 'code2'=>'Acts'	];

        case 45: return ['code'=>'jas', 'shortName'=>['en'=>'Jas'   , 'ru'=>'Иак'  ], 'fullName'=>['en'=>'James'           , 'ru'=>'Иакова'				], 'code2'=>'Jas'	];
        case 46: return ['code'=>'1pe', 'shortName'=>['en'=>'1Pet'  , 'ru'=>'1Пет' ], 'fullName'=>['en'=>'1 Peter'         , 'ru'=>'1 Петра'			], 'code2'=>'1Pet'	];
        case 47: return ['code'=>'2pe', 'shortName'=>['en'=>'2Pet'  , 'ru'=>'2Пет' ], 'fullName'=>['en'=>'2 Peter'         , 'ru'=>'2 Петра'			], 'code2'=>'2Pet'	];
        case 48: return ['code'=>'1jn', 'shortName'=>['en'=>'1Jn'   , 'ru'=>'1Ин'  ], 'fullName'=>['en'=>'1 John'          , 'ru'=>'1 Иоанна'			], 'code2'=>'1John'	];
        case 49: return ['code'=>'2jn', 'shortName'=>['en'=>'2Jn'   , 'ru'=>'2Ин'  ], 'fullName'=>['en'=>'2 John'          , 'ru'=>'2 Иоанна'			], 'code2'=>'2John'	];
        case 50: return ['code'=>'3jn', 'shortName'=>['en'=>'3Jn'   , 'ru'=>'3Ин'  ], 'fullName'=>['en'=>'3 John'          , 'ru'=>'3 Иоанна'			], 'code2'=>'3John'	];
        case 51: return ['code'=>'jud', 'shortName'=>['en'=>'Jude'  , 'ru'=>'Иуд'  ], 'fullName'=>['en'=>'Jude'            , 'ru'=>'Иуды'				], 'code2'=>'Jude'	];

        case 52: return ['code'=>'rom', 'shortName'=>['en'=>'Rom'   , 'ru'=>'Рим'  ], 'fullName'=>['en'=>'Romans'          , 'ru'=>'Римлянам'			], 'code2'=>'Rom'	];
        case 53: return ['code'=>'1co', 'shortName'=>['en'=>'1Cor'  , 'ru'=>'1Кор' ], 'fullName'=>['en'=>'1 Corinthians'   , 'ru'=>'1 Коринфянам'		], 'code2'=>'1Cor'	];
        case 54: return ['code'=>'2co', 'shortName'=>['en'=>'2Cor'  , 'ru'=>'2Кор' ], 'fullName'=>['en'=>'2 Corinthians'   , 'ru'=>'2 Коринфянам'		], 'code2'=>'2Cor'	];
        case 55: return ['code'=>'gal', 'shortName'=>['en'=>'Gal'   , 'ru'=>'Гал'  ], 'fullName'=>['en'=>'Galatians'       , 'ru'=>'Галатам'			], 'code2'=>'Gal'	];
        case 56: return ['code'=>'eph', 'shortName'=>['en'=>'Eph'   , 'ru'=>'Еф'   ], 'fullName'=>['en'=>'Ephesians'       , 'ru'=>'Ефесянам'			], 'code2'=>'Eph'	];
        case 57: return ['code'=>'php', 'shortName'=>['en'=>'Phil'  , 'ru'=>'Фил'  ], 'fullName'=>['en'=>'Philippians'     , 'ru'=>'Филиппийцам'		], 'code2'=>'Phil'	];
        case 58: return ['code'=>'col', 'shortName'=>['en'=>'Col'   , 'ru'=>'Кол'  ], 'fullName'=>['en'=>'Colossians'      , 'ru'=>'Колоссянам'			], 'code2'=>'Col'	];
        case 59: return ['code'=>'1th', 'shortName'=>['en'=>'1Thess', 'ru'=>'1Фес' ], 'fullName'=>['en'=>'1 Thessalonians' , 'ru'=>'1 Фессалоникийцам'	], 'code2'=>'1Thess'];
        case 60: return ['code'=>'2th', 'shortName'=>['en'=>'2Thess', 'ru'=>'2Фес' ], 'fullName'=>['en'=>'2 Thessalonians' , 'ru'=>'2 Фессалоникийцам'	], 'code2'=>'2Thess'];
        case 61: return ['code'=>'1ti', 'shortName'=>['en'=>'1Tim'  , 'ru'=>'1Тим' ], 'fullName'=>['en'=>'1 Timothy'       , 'ru'=>'1 Тимофею'			], 'code2'=>'1Tim'	];
        case 62: return ['code'=>'2ti', 'shortName'=>['en'=>'2Tim'  , 'ru'=>'2Тим' ], 'fullName'=>['en'=>'2 Timothy'       , 'ru'=>'2 Тимофею'			], 'code2'=>'2Tim'	];
        case 63: return ['code'=>'tit', 'shortName'=>['en'=>'Tit'   , 'ru'=>'Тит'  ], 'fullName'=>['en'=>'Titus'           , 'ru'=>'Титу'				], 'code2'=>'Titus'	];
        case 64: return ['code'=>'phm', 'shortName'=>['en'=>'Philem', 'ru'=>'Флм'  ], 'fullName'=>['en'=>'Philemon'        , 'ru'=>'Филимону'			], 'code2'=>'Phlm'	];
        case 65: return ['code'=>'heb', 'shortName'=>['en'=>'Heb'   , 'ru'=>'Евр'  ], 'fullName'=>['en'=>'Hebrews'         , 'ru'=>'Евреям'				], 'code2'=>'Heb'	];

        case 66: return ['code'=>'rev', 'shortName'=>['en'=>'Rev'   , 'ru'=>'Откр' ], 'fullName'=>['en'=>'Revelation'      , 'ru'=>'Откровение'			], 'code2'=>'Rev'	];
	}
	
	return false;
}

