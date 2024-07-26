<?php

$books_limit    = 9999;
$chapters_limit = 9999;

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
		$bookCode = $book['id'];
		
		//if ( $bookCode < 40 or $bookCode > 43 ) continue; // Только Евангелия
		if ( $bookCode > $books_limit ) continue;
		
		$compute_chapters = true;
		
		// не нужно, т.к. переформатирование недолгое, а выравнивание старое берем
		// if ( $mode == 'MODE_CHANGE' ) {
			// попытка найти
			// $old_book = ['chapters' => []];
			// foreach ( $old_bible['books'] as $b ) {
				// if ( $b['id'] == $bookCode ) {
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
		$bookArray['id'] = $bookCode;
		$book_info = get_book_info($bookCode);
		$bookArray['code'] = $book_info['code'];
		$bookArray['shortName'] = $book_info['shortName'][$bible['lang']];
		$bookArray['fullName'] = $book_info['fullName'][$bible['lang']];
		
		$book0 = str_pad($bookCode, 2, '0', STR_PAD_LEFT);
		
		print("Book $book0 ... ");
	
		foreach($book['chapters'] as $chapter) {
			$chapterCode = $chapter['id'];
			
			if ( $chapterCode > $chapters_limit ) continue;
			
			$chapter0 = str_pad($chapterCode, 2, '0', STR_PAD_LEFT);
			
			array_push($bookArray['chapters'], ['id'=>$chapterCode, 'verses'=>get_formatted_chapter_timecodes_mfa($book0, $chapter0, $translation, $voice)]);
		}
		
		array_push($bible['books'], $bookArray);
		
		// $filenameB = "audio/$translation/$voice/mp3/$book0/timecodes.json";
		
		// if ( !file_exists(dirname($filenameB)) )
			// mkdir(dirname($filenameB), 0777, true);

		// file_put_contents($filenameB, json_encode($bookArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			
		print("Done!\n");
	}
	
	file_put_contents($filename, json_encode($bible, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	
	print("\nResult saved to $filename\n\n");
}

function get_formatted_chapter_timecodes_mfa($book, $chapter, $translation, $voice) 
{
	global $entries;
	
	$filename_json = "audio/$translation/$voice/mfa_output/${book}/${chapter}.json";
	if ( !file_exists($filename_json) ) {
		print("$filename_json does not exists!\n\n");
		return;
	}
	$mfa_json = json_decode(file_get_contents($filename_json), true);
	$entries = $mfa_json['tiers']['words']['entries'];
	
	$filename_text = "audio/$translation/$voice/mfa_input/${book}/${chapter}.txt";
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
		rmdir_recursive();
		mkdir($mfa_output_dir, 0777, true);
		chmod($mfa_output_dir, 0777);
		
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
			if ( !is_dir("$mfa_output_dir/$book0") )
			{
				mkdir("$mfa_output_dir/$book0", 0777, true);
				chmod("$mfa_output_dir/$book0", 0777);
			}
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

function prepare_files($translation, $voice, $mode)
{
	global $books_limit, $chapters_limit;

	print "\nPREPARING:\n" ;
	
	$translationArray = get_translation_array($translation);
	
	$translation_info = get_translation_info($translation);
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
			download_chapter_audio($translation, $voice, $book0, $chapter0, $mode);
			// конверт в wav 
			convert_mp3_to_vaw($translation, $voice, $book0, $chapter0, $mode);
			// подготовка текста
			create_chapter_plain($voice, $chapter['verses'], $bookCode, $chapterCode, $lang, "audio/$translation/$voice/mfa_input/${book0}/${chapter0}.txt");
			
			print("$chapterCode ");
		}
		
		print("Done!\n");
	}
	
	print("All files prepared. \n");
}

function prepare_environment($translation, $voice) 
{
	if ( !file_exists("audio/$translation/$voice/mfa_input") ) 
	{
		mkdir("audio/$translation/$voice/mfa_input", 0777, true);
		chmod("audio/$translation/$voice/mfa_input", 0777);
	}
	
	if ( !file_exists('audio/mfa_output') ) 
	{
		mkdir("audio/$translation/$voice/mfa_output", 0777, true);
		chmod("audio/$translation/$voice/mfa_output", 0777);
	}
}

function do_all($translation, $voice, $mode)
{
	prepare_environment($translation, $voice);
	
	prepare_files($translation, $voice, $mode);
	
	// массовое выравнивание
	mfa_align_all($translation, $voice, $mode);
	
	// преобразование результатов 
	format_all($translation, $voice, $mode);
}

$translation = determine_text_translation();
$voice = determine_voice_4bbl($translation);
$mode = determine_mode();
// $step = determine_step();

do_all($translation, $voice, $mode);
