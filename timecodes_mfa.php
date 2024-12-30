<?php

$only_book = 21; // false
$only_chapter = 1;

require 'include.php';

// форматирование выровненных файлов в итоговый файл timecodes.json
function format_all($translation, $voice)
{
	global $only_book, $only_chapter;
	
	print("\nFORMATTING:\n");
	
	$filename = "audio/$translation/$voice/timecodes.json";
	
	$translationArray = get_translation_array($translation);
	$voice_info = get_voice_info($voice);
	
	$bible = [];
	$bible['books'] = [];
	$bible['code'] = $voice;
	$bible['translation'] = $translation;
	$translation_info = get_translation_info($translation);
	$bible['lang'] = $translation_info['lang'];
	
	foreach($translationArray['books'] as $book)
	{
		$book_number = $book['id'];
		
		if ( $only_book!==false && $book_number!=$only_book ) continue;
		
		$compute_chapters = true;
		
		$book_info = get_book_info($book_number);
		
		$bookArray = [];
		$bookArray['chapters']  = [];
		$bookArray['id']        = $book_number;
		$bookArray['code']      = $book_info['code'];
		$bookArray['shortName'] = $book_info['shortName'][$bible['lang']];
		$bookArray['fullName']  = $book_info['fullName'][$bible['lang']];
		
		$book0 = str_pad($book_number, 2, '0', STR_PAD_LEFT);
		
		print("Book $book0 ... ");
	
		foreach ( $book['chapters'] as $chapter ) {
			$chapter_number = $chapter['id'];
			
			if ( $only_chapter!==false && $chapter_number!=$only_chapter ) continue;
			
			$chapter0 = str_pad($chapter_number, 2, '0', STR_PAD_LEFT);
			
			array_push($bookArray['chapters'], [
				'id'     => $chapter_number, 
				'verses' => get_formatted_chapter_timecodes_mfa($book0, $chapter0, $translation, $voice, $chapter, $voice_info)
			]);
		}
		
		array_push($bible['books'], $bookArray);
		
		// $filenameB = "audio/$translation/$voice/mp3/$book0/timecodes.json";
		
		// create_dir777_if_not_exists($filenameB);

		// file_put_contents($filenameB, json_encode($bookArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			
		print("Done!\n");
	}
	
	file_put_contents($filename, json_encode($bible, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	
	print("\nResult saved to $filename\n\n");
}

function get_formatted_chapter_timecodes_mfa($book, $chapter, $translation, $voice, $chapterArray, $voice_info) 
{
	global $entries;

	$filename_json = "audio/$translation/$voice/mfa_output/$book/$chapter.json";
	if ( !file_exists($filename_json) ) {
		print("$filename_json does not exists!\n\n");
		return;
	}
	$mfa_json = json_decode(file_get_contents($filename_json), true);
	$entries = $mfa_json['tiers']['words']['entries'];
	
	$filename_text = "audio/$translation/$voice/mfa_input/$book/$chapter.txt";
	if ( !file_exists($filename_text) ) 
		die("$filename_text does not exists!!!\n\n");
	$lines = file($filename_text);
	
	$formatted = [];

	$skip_begin = 0;
	if ( $voice_info['readBookNames'] && ($chapter == '01' || $voice_info['readBookNamesAllChapters']) )
		$skip_begin += 1;
	if ( $voice_info['readChapterNumbers'] )
		$skip_begin += 1;


	// print("skip_begin: $skip_begin\n");

	$is_title = false;
	foreach ( $lines as $line ) {
		
		$line = mb_strtolower($line, 'UTF-8');
		$interval = get_interval($line, 0, -1, 0);
		
		if ( $skip_begin>0 ) {
			$skip_begin -= 1;
			continue;
		}

		$verse_id = count($formatted) + 1;

		// print("verse_id: $verse_id, line: $line, interval: " . print_r($interval, 1) . "\n");
		
		// пропуск, если перед следующим стихом должен быть заголовок, но только один раз
		if ( $is_title ) 
			$is_title = false;
		else {
			if ( $voice_info['readTitles'] ) {
				foreach ( $chapterArray['titles'] as $title ) {
					if ( $title['before_verse_number'] == $verse_id ) {
						$is_title = true;
						//print("Title found before $verse_id\n");
						break;
					}
				}
			}
		}
		if ( $is_title ) continue;

		$verse = [];
		$verse['id'] = $verse_id;
		$verse['begin'] = $interval[1];
		$verse['end'] = $interval[2];

		// print_r($verse); print("\n");

		if ( $verse['end'] < $verse['begin'] ) {
			if ( containsLetter($line) ) {
				print("Error: end [$verse[end]] < begin [$verse[begin]] IN book:$book, chapter:$chapter, verse:$verse[id], line:$line\n");
				die();
			}
			else 
				$verse['end'] = $verse['begin']; // example: bti book:01, chapter:42, verse:3, line:—
		}

		array_push($formatted, $verse);
	}
	
	return $formatted;
}

function get_interval($line, $offset, $begin, $old_end) 
{
	global $entries;
	
	$entry = array_shift($entries);
	
	if ( $entry == null ) {
		return [trim($line), $begin, $old_end];
	}
	
	$end = $entry[1];
	$entryword = $entry[2];
	
	$b = ( $begin == -1 ) ? $entry[0] : $begin;
	
	if ( $entryword == '' ) {
		return get_interval($line, $offset, $b, $end);
	}
	
	if ( mb_strpos($line, $entryword, $offset) !== false ) {
		$offset += mb_strlen($entryword) + 1;
		return get_interval($line, $offset, $b, $end);
	}
	
	// отпилим половину от паузы
	$next_begin = $entry[0];
	$pause = $next_begin - $old_end;
	
	array_unshift($entries, $entry);
	
	return [ trim($line), $b, $old_end + $pause/2 ];
}

function mfa_align_all($translation, $voice, $mode)
{
	global $only_book;
	
	print "\nALIGNING:\n" ;
	
	$mfa_output_dir = "audio/$translation/$voice/mfa_output";
	$mfa_input_dir  = "audio/$translation/$voice/mfa_input";

	// определение моделей
	$translation_info = get_translation_info($translation);
	
	switch($translation_info['lang']) {
		case 'en':
			$models = 'english_mfa english_mfa';
			break;
		case 'ru':
			$models = 'russian_mfa russian_mfa';
			break;
		default:
			die('Undetermined models for this language');
	}

	// очистим/пересоздадим папку вывода
	if ( $mode == 'MODE_REPLACE' ) 
	{
		create_dir777_if_not_exists($mfa_output_dir, True);
		print "All output files cleaned\n";
	}
	
	// подготовка контейнера
	prepare_container();
	
	// выравнивание
	for ( $book=1; $book<=66; $book++ ) {

		if ( $only_book!==false && $book!=$only_book ) continue;
		
		$book0 = str_pad($book, 2, '0', STR_PAD_LEFT);
		
		if ( is_dir("$mfa_input_dir/$book0") )
		{
			// так может уже и не надо ничего делать?
			if ( is_output_almost_full("$mfa_input_dir/$book0", "$mfa_output_dir/$book0") )
			{
				print "$mfa_output_dir/$book0 already is full, skipped\n";
				continue;
			}
			create_dir777_if_not_exists("$mfa_output_dir/$book0");
			exec_and_print("docker exec -it mfa bash -c 'mfa align --clean --overwrite --output_format json /$mfa_input_dir/$book0 $models /$mfa_output_dir/$book0 --beam 20 --retry_beam 80'");
			//exec_and_print("docker exec -it mfa bash -c 'mfa align --clean --overwrite --output_format json /$mfa_input_dir/$book0 russian_mfa russian_mfa /$mfa_output_dir/$book0 --beam 20 --retry_beam 80'");
		}
	}

	print("All files aligned. \n");
}

function prepare_container() {
	$container_exist = exec('docker exec -it mfa echo 1');
	if ( $container_exist ) 
	{
		print "Container mfa already exists.\n";
		print "If you want, you can drop it manually [docker rm -f mfa] and repeat operation.\n\n";
	}
	else
	{
		exec_and_print('docker run -it -d --name mfa --volume "' . __DIR__ . '/audio:/audio" mmcauliffe/montreal-forced-aligner:v2.2.17');
		exec_and_print('docker exec -it mfa bash -c "mfa models download dictionary russian_mfa --version v2.0.0a"');
		exec_and_print('docker exec -it mfa bash -c "mfa models download acoustic russian_mfa --version v2.0.0a"');
		exec_and_print('docker exec -it mfa bash -c "mfa models download dictionary english_mfa --version v2.0.0a"');
		exec_and_print('docker exec -it mfa bash -c "mfa models download acoustic english_mfa --version v2.0.0a"');
	}
}

function is_output_almost_full($in_dir, $out_dir)
{
	if ( !is_dir($out_dir) )
		return False;
	
	$in_files = scandir($in_dir);
	$filtered_in_files = array_filter($in_files, function($value) {
		return strripos($value, 'wav');
	});
	if ( count($filtered_in_files) == 0 ) 
		return True;
	// print_r($filtered_in_files);
	// die();

	$cc_not_exists = 0;
	$out_files = scandir($out_dir);
	foreach ( $filtered_in_files as $in_file )
	{
		$out_file = str_replace('.wav', '.json', $in_file);
		if ( !in_array($out_file, $out_files) ) {
			$cc_not_exists += 1;
			//return False;
		}
	}

	// до 30% нехватки пропустим потом через фикс
	if ( $cc_not_exists / count($filtered_in_files) < 0.3 ) 
		return True;
}


function create_chapter_plain($voice, $voice_info, $chapter, $book_number, $chapter_number, $lang, $filename)
{
	$str = '';
	
	if ( $voice_info['readBookNames'] and ($chapter_number == 1 or $voice_info['readBookNamesAllChapters'] == 1) ) 
	{
		$book_info = get_book_info($book_number);
		// вообще для каждого перевода своя система походу, как чтец называет книги
		// if ( $book_info['ru_audio'] )
			// $str .= $book_info['ru_audio'] . ".\n";
		// else
			// print_r($book_info);
		$prename = get_book_prename($voice, $book_number);
		$str .= ($prename ? $prename : $book_info['fullName'][$lang]) . ".\n";
	}
	if ( $voice_info['readChapterNumbers'] )
		$str .= get_chapter_name($lang, $book_number, $chapter_number) . ".\n";
	
	foreach ( $chapter['verses'] as $verse )
	{
		// не добавлять пустые
		if ( !$verse['unformatedText'] )
			continue;
		// добавление заголовка
		if ( $voice_info['readTitles'] )
			foreach ( $chapter['titles'] as $title )
				if ( $title['before_verse_number'] == $verse['id'] )
				{
					$str .= $title['text'] . ".\n";
					break;
				}
		
		$str .= removeBrackets($verse['unformatedText']) . "\n";
	}
	
	file_put_contents($filename, $str);
	
	// print("Plain $filename created\n");
}

function prepare_files($translation, $voice, $mode)
{
	global $only_book, $only_chapter;

	print "\nPREPARING:\n" ;
	
	$translationArray = get_translation_array($translation);
	
	$translation_info = get_translation_info($translation);
	$voice_info = get_voice_info($voice);
	$lang = $translation_info['lang'];
	
	foreach($translationArray['books'] as $book)
	{
		$bookCode = $book['id'];
		
		if ( $only_book!==false && $bookCode!=$only_book ) continue;
		
		$book0 = str_pad($bookCode, 2, '0', STR_PAD_LEFT);
		
		print("Book $book0 ... ");
		deleteTxtFiles("audio/$translation/$voice/mfa_input/$book0/");
	
		foreach ( $book['chapters'] as $chapter )
		{
			$chapterCode = $chapter['id'];
			$chapter0 = str_pad($chapterCode, 2, '0', STR_PAD_LEFT);
			
			if ( $only_chapter!==false && $chapterCode!=$only_chapter ) continue;
			
			// скачивание mp3
			download_chapter_audio($translation, $voice, $bookCode, $chapterCode);
			// конверт в wav 
			convert_mp3_to_vaw($translation, $voice, $book0, $chapter0);
			// подготовка текста
			create_chapter_plain($voice, $voice_info, $chapter, $bookCode, $chapterCode, $lang, "audio/$translation/$voice/mfa_input/$book0/$chapter0.txt");
			
			print("$chapterCode ");
		}
		
		print("Done!\n");
	}
	
	print("All files prepared. \n");
}

function prepare_environment($translation, $voice) 
{
	create_dir777_if_not_exists("audio/$translation/$voice/mfa_input");
	create_dir777_if_not_exists("audio/$translation/$voice/mfa_output");
}

function check_all($translation, $voice, $try) 
{
	global $only_book, $only_chapter;
	print "Checking alignment results, try $try...\n";
	
	$errors_count = 0;
	
	$translationArray = get_translation_array($translation);
	
	// очистим и пересоздадим временные директории
	$mfa_input_dir  = "audio/_fix/mfa_input";
	$mfa_output_dir = "audio/_fix/mfa_output";
	create_dir777_if_not_exists($mfa_input_dir, True);
	create_dir777_if_not_exists($mfa_output_dir, True);
	
	// косяки выравнивания выявляем и копируем файлы
	foreach ( $translationArray['books'] as $book )
	{
		if ( $only_book!==false && $book['id']!=$only_book ) continue;
		
		$book0 = str_pad($book['id'], 2, '0', STR_PAD_LEFT);
		foreach ( $book['chapters'] as $chapter ) 
		{
			if ( $only_chapter!==false && $chapter['id']!=$only_chapter ) continue;
			
			$chapter0 = str_pad($chapter['id'], 2, '0', STR_PAD_LEFT);
			
			if ( !file_exists("audio/$translation/$voice/mfa_output/$book0/$chapter0.json") ) 
			{
				print "Need fix: book $book[id] / chapter $chapter[id] is empty!\n";
				
				// копируем файлы
                $source_wav = "audio/$translation/$voice/mfa_input/$book0/$chapter0.wav";
                $source_txt = "audio/$translation/$voice/mfa_input/$book0/$chapter0.txt";
                
                if ( !file_exists($source_wav) ) die("$source_wav does not exist!\n");
				if ( !file_exists($source_txt) ) die("$source_txt does not exist!\n");

                copy($source_wav, "$mfa_input_dir/{$book0}_$chapter0.wav");
                copy($source_txt, "$mfa_input_dir/{$book0}_$chapter0.txt");
                
				$errors_count += 1;
				if ( $errors_count >= 3 ) break;
			}

			if ( $errors_count >= 3 ) break;
		}
	}
	
	if ( $errors_count > 0 )
	{
		print "\n";
		$beam = $try*1000 - 500;
		$retry_beam = $try*1000;
		prepare_container();
		exec_and_print("docker exec -it mfa bash -c 'mfa align --clean --overwrite --output_format json /$mfa_input_dir russian_mfa russian_mfa /$mfa_output_dir --beam $beam --retry_beam $retry_beam'");
		$fix_count = save_fixes($translation, $voice);
	}
	else
		$fix_count = 0;
	
	print "Checking done. $errors_count errors found, $fix_count fixed.\n";
	
	return $errors_count-$fix_count == 0;
}

function save_fixes($translation, $voice)
{
	print "Saving fixes...\n";
	global $only_book, $only_chapter;
	$mfa_output_dir = "audio/_fix/mfa_output";
	$fix_count = 0;
	foreach ( scandir($mfa_output_dir) as $f ) 
	{
		if ( $f != '.' and $f != '..' )
		{
			$fix_count += 1;
			list($book0, $chapter0) = explode('_', explode('.', $f)[0]);
			
			if ( $only_book!==false && (int)$book0!=$only_book ) continue;
			if ( $only_chapter!==false && (int)$chapter0!=$only_chapter ) continue;

			copy("$mfa_output_dir/{$book0}_$chapter0.json", "audio/$translation/$voice/mfa_output/$book0/$chapter0.json");
			print "Fixed: book {$book0} / chapter $chapter0 fixed\n";
		}
	}
	if ( $fix_count == 0 ) 
		print "Nothing fixed.\n";
	else 
		print "$fix_count fixed.\n";

	return $fix_count;
}

function do_all($translation, $voice, $mode)
{
	if ( $mode == 'MODE_FINISH' ) 
		save_fixes($translation, $voice);
	else {
		prepare_environment($translation, $voice);
		
		// скачивание
		prepare_files($translation, $voice, $mode);
		
		// выравнивание
		mfa_align_all($translation, $voice, $mode);
	}

	// проверка результатов
	for ( $try=1; $try<=5; $try++ )
		if ( check_all($translation, $voice, $try) ) break;
	
	// преобразование результатов 
	format_all($translation, $voice);
}

$translation = determine_text_translation();
$voice = determine_voice_4bbl($translation);
$mode = determine_mode();

do_all($translation, $voice, $mode);
