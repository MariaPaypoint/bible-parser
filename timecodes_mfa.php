<?php

$books_limit    = 999;
$chapters_limit = 999;

require 'include.php';

function format_all($translation, $voice, $mode)
{
	global $books_limit, $chapters_limit;
	
	print("\nFORMATTING:\n");
	
	$filename = "audio/$translation/$voice/timecodes.json";
	
	$translationArray = get_translation_array($translation);
	
	if ( $mode == 'MODE_CHANGE' & file_exists($filename) )
		$old_bible = json_decode(file_get_contents($filename), true); // чтоб все не переделывать
	
	$bible = [];
	$bible['books'] = [];
	$bible['code'] = $voice;
	$bible['translation'] = $translation;
	$translation_info = get_translation_info($translation);
	$bible['lang'] = $translation_info['lang'];
	
	foreach($translationArray['books'] as $book)
	{
		$book_number = $book['id'];
		
		//if ( $book_number < 40 or $book_number > 43 ) continue; // Только Евангелия
		if ( $book_number > $books_limit ) continue;
		
		$compute_chapters = true;
		
		$book_info = get_book_info($book_number);
		
		// не нужно, т.к. переформатирование недолгое, а выравнивание старое берем
		// if ( $mode == 'MODE_CHANGE' ) {
			// попытка найти
			// $old_book = ['chapters' => []];
			// foreach ( $old_bible['books'] as $b ) {
				// if ( $b['id'] == $book_number ) {
					// $old_book = $b;
					// $compute_chapters = false;
					// break;
				// }
			// }
			// $bookArray = $old_book;
		// }
		// else {
			$bookArray = [];
			$bookArray['chapters'] = [];
		// }
		$bookArray['id']        = $book_number;
		$bookArray['code']      = $book_info['code'];
		$bookArray['shortName'] = $book_info['shortName'][$bible['lang']];
		$bookArray['fullName']  = $book_info['fullName'][$bible['lang']];
		
		$book0 = str_pad($book_number, 2, '0', STR_PAD_LEFT);
		
		print("Book $book0 ... ");
	
		foreach ( $book['chapters'] as $chapter ) {
			$chapter_number = $chapter['id'];
			
			if ( $chapter_number > $chapters_limit ) continue;
			
			$chapter0 = str_pad($chapter_number, 2, '0', STR_PAD_LEFT);
			
			array_push($bookArray['chapters'], [
				'id'     => $chapter_number, 
				'verses' => get_formatted_chapter_timecodes_mfa($book0, $chapter0, $translation, $voice)
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

function get_formatted_chapter_timecodes_mfa($book, $chapter, $translation, $voice) 
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
	$cc = 0;
	
	foreach ( $lines as $line ) {
		
		$cc += 1;
		$line = mb_strtolower($line, 'UTF-8');
		
		$interval = get_interval($line, 0, -1, 0);
		//print_r($interval);
		
		if ( $cc == 1 ) continue; // chapter
		if ( $cc == 2 and $chapter == '01' ) continue; // book name
		
		$verse = [];
		$verse["id"] = count($formatted) + 1;
		$verse["begin"] = $interval[1];
		$verse["end"] = $interval[2];
		
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
	global $books_limit;
	
	print "\nALIGNING:\n" ;
	
	$mfa_output_dir = "audio/$translation/$voice/mfa_output";
	$mfa_input_dir  = "audio/$translation/$voice/mfa_input";
	
	// очистим/пересоздадим папку вывода
	if ( $mode == 'MODE_REPLACE' ) 
	{
		create_dir777_if_not_exists($mfa_output_dir, True);
		print "All output files cleaned\n";
	}
	
	// подготовка контейнера
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
	}
	
	// выравнивание
	for ( $book=1; $book<=min(66, $books_limit); $book++ ) {
		$book0 = str_pad($book, 2, '0', STR_PAD_LEFT);
		
		if ( is_dir("$mfa_input_dir/$book0") )
		{
			// так может уже и не надо ничего делать?
			if ( is_output_full("$mfa_input_dir/$book0", "$mfa_output_dir/$book0") )
			{
				print "$mfa_output_dir/$book0 already is full, skipped\n";
				continue;
			}
			create_dir777_if_not_exists("$mfa_output_dir/$book0");
			exec_and_print("docker exec -it mfa bash -c 'mfa align --clean --overwrite --output_format json /$mfa_input_dir/$book0 russian_mfa russian_mfa /$mfa_output_dir/$book0 --beam 20 --retry_beam 80'");
		}
	}

	print("All files aligned. \n");
}

function is_output_full($in_dir, $out_dir)
{
	if ( !is_dir($out_dir) )
		return False;
	
	$in_files = scandir($in_dir);
	$filtered_in_files = array_filter($in_files, function($value) {
		return strripos($value, 'wav');
	});
	// print_r($filtered_in_files);
	// die();
	$out_files = scandir($out_dir);
	foreach ( $filtered_in_files as $in_file )
	{
		$out_file = str_replace('.wav', '.json', $in_file);
		if ( !in_array($out_file, $out_files) )
			return False;
	}
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
	{
		if ( $book_number == 19 )
			$str .= 'Псалом ' . get_ps_name($chapter_number) . ".\n";
		else
			$str .= 'Глава ' . get_chapter_name($chapter_number) . ".\n";
	}
	
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
		
		$str .= $verse['unformatedText'] . "\n";
	}
	
	file_put_contents($filename, $str);
	
	// print("Plain $filename created\n");
}

function prepare_files($translation, $voice, $mode)
{
	global $books_limit, $chapters_limit;

	print "\nPREPARING:\n" ;
	
	$translationArray = get_translation_array($translation);
	
	$translation_info = get_translation_info($translation);
	$voice_info = get_voice_info($voice);
	$lang = $translation_info['lang'];
	
	foreach($translationArray['books'] as $book)
	{
		$bookCode = $book['id'];
		
		if ( $bookCode > $books_limit ) continue;
		
		$book0 = str_pad($bookCode, 2, '0', STR_PAD_LEFT);
		
		print("Book $book0 ... ");
	
		foreach ( $book['chapters'] as $chapter )
		{
			$chapterCode = $chapter['id'];
			$chapter0 = str_pad($chapterCode, 2, '0', STR_PAD_LEFT);
			
			if ( $chapterCode > $chapters_limit ) continue;
			
			// скачивание mp3
			download_chapter_audio($translation, $voice, $bookCode, $chapterCode, $mode);
			// конверт в wav 
			convert_mp3_to_vaw($translation, $voice, $book0, $chapter0, $mode);
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
	global $books_limit;
	print "Checking alignment results, try $try...\n";
	
	$errors_count = 0;
	$fix_count = 0;
	$translationArray = get_translation_array($translation);
	
	// очистим и пересоздадим временные директории
	$mfa_input_dir  = "audio/_fix/mfa_input";
	$mfa_output_dir = "audio/_fix/mfa_output";
	create_dir777_if_not_exists($mfa_input_dir, True);
	create_dir777_if_not_exists($mfa_output_dir, True);
	
	// косяки выравнивания выявляем и копируем файлы
	foreach ( $translationArray['books'] as $book )
	{
		if ( $book['id'] > $books_limit ) break;
		$book0 = str_pad($book['id'], 2, '0', STR_PAD_LEFT);
		foreach ( $book['chapters'] as $chapter ) 
		{
			$chapter0 = str_pad($chapter['id'], 2, '0', STR_PAD_LEFT);
			
			if ( !file_exists("audio/$translation/$voice/mfa_output/$book0/$chapter0.json") ) 
			{
				print "Error: book $book[id] / chapter $chapter[id] is empty!\n";
				
				// копируем файлы
				copy("audio/$translation/$voice/mfa_input/$book0/$chapter0.wav", "$mfa_input_dir/{$book0}_$chapter0.wav");
				copy("audio/$translation/$voice/mfa_input/$book0/$chapter0.txt", "$mfa_input_dir/{$book0}_$chapter0.txt");
				
				$errors_count += 1;
			}
		}
	}
	
	if ( $errors_count > 0 )
	{
		print "\n";
		$beam = $try*1000;
		$retry_beam = $try*1000+500;
		exec_and_print("docker exec -it mfa bash -c 'mfa align --clean --overwrite --output_format json /$mfa_input_dir russian_mfa russian_mfa /$mfa_output_dir --beam $beam --retry_beam $retry_beam'");
		foreach ( scandir($mfa_output_dir) as $f ) 
			if ( $f != '.' and $f != '..' )
			{
				$fix_count += 1;
				list($book0, $chapter0) = explode('_', explode('.', $f)[0]);
				
				copy("$mfa_output_dir/{$book0}_$chapter0.json", "audio/$translation/$voice/mfa_output/$book0/$chapter0.json");
				print "Fixed: book {$book0} / chapter $chapter0 fixed\n";
			}
	}
	
	print "Checking done. $errors_count errors found, $fix_count fixed.\n";
	
	return $errors_count-$fix_count == 0;
}

function do_all($translation, $voice, $mode)
{
	prepare_environment($translation, $voice);
	
	// скачивание
	prepare_files($translation, $voice, $mode);
	
	// выравнивание
	mfa_align_all($translation, $voice, $mode);
	
	// проверка результатов
	for ( $try=1; $try<=5; $try++ )
		if ( check_all($translation, $voice, $try) ) break;
	
	// преобразование результатов 
	format_all($translation, $voice, $mode);
}

$translation = determine_text_translation();
$voice = determine_voice_4bbl($translation);
$mode = determine_mode();
// $step = determine_step();

do_all($translation, $voice, $mode);
