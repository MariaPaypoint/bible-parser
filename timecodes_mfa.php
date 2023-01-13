<?php

$books_limit = 200;
$chapters_limit = 1000;

require 'include.php';

function mfa_align_all($mode, $translation, $voice)
{
	global $books_limit;
	// $cmd_mfa = 'docker exec -it mfa bash -c "conda activate aligner && ' . 
	           // 'mfa models download dictionary russian_mfa && ' .
			   // 'mfa models download acoustic russian_mfa && ' .
			   // 'mfa align --output_format json /audio/mfa_input russian_mfa russian_mfa /audio/mfa_output"';
	// очистим папку вывода
	array_map('unlink', glob('audio/mfa_output/*'));
	
	// запустим/перезапустим контейнер с mfa
	$cmd_mfa = 'docker rm -f mfa && docker run -it -d --name mfa --volume "' . __DIR__ . '/audio:/audio" mfa:latest ';
	
	if ( exec($cmd_mfa , $output) ) { //, $retval
		
		$aligns = '';
		for ( $book=1; $book<=min(66, $books_limit); $book++ ) {
			$book0 = str_pad($book, 2, '0', STR_PAD_LEFT);
			$aligns .= " && mfa align --clean --overwrite --output_format json /audio/mfa_input/$book0 russian_mfa russian_mfa /audio/mfa_output/$book0 --beam 20 --retry_beam 80";
		}
		// автоматизировать сложновато https://pythonspeed.com/articles/activate-conda-dockerfile/
		print("\n");
		print('PLEASE, RUN MANUAL: ' . "\n");
		print('    docker exec -it mfa bash' . "\n");
		print("    conda activate aligner && mfa models download dictionary russian_mfa && mfa models download acoustic russian_mfa $aligns && exit \n");
		print('AFTER THAT, DO THE SECOND STEP:' . "\n");
		print("    php82 timecodes_mfa.php $translation $voice $mode 2 \n\n");
	}
	else {
		print_r($output);
		die();
	}
}

function delete_temporary_files_aenaes()
{
	$filename = 'audio/timecodes.json';
	if ( file_exists($filename) )
		unlink($filename);
	
	array_map('unlink', glob('audio/*.txt'));
	
	// print("Temporary files deleted\n");
}

function step1($mode, $translation, $voice)
{
	global $books_limit, $chapters_limit;

	$translationArray = get_translation_array($translation);
	
	$translation_info = get_translation_info($translation);
	$lang = $translation_info['lang'];
	
	foreach($translationArray['books'] as $book)
	{
		$bookCode = $book['id'];
		
		if ( $bookCode > $books_limit ) continue;
		
		$book0 = str_pad($bookCode, 2, '0', STR_PAD_LEFT);
		
		print("Book $bookCode ... ");
	
		// скачивание mp3 и конверт в wav 
		foreach ( $book['chapters'] as $chapter )
		{
			$chapterCode = $chapter['id'];
			$chapter0 = str_pad($chapterCode, 2, '0', STR_PAD_LEFT);
			
			if ( $chapterCode > $chapters_limit ) continue;
			
			download_chapter_audio($translation, $voice, $book0, $chapter0);
			convert_mp3_to_vaw($translation, $voice, $book0, $chapter0);
			create_chapter_plain($chapter['verses'], $bookCode, $chapterCode, $lang, "audio/mfa_input/${book0}/${chapter0}.txt");
			
			print("$chapterCode ");
		}
		
		print("Done!\n");
	}
	
	print("All files prepared. \n");
	
	// массовое преобразование
	mfa_align_all($mode, $translation, $voice);
}

function step2($mode, $translation, $voice)
{
	global $books_limit, $chapters_limit;
	
	$filename = "audio/$translation-$voice.json";
	
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
		
		if ( $mode == 'MODE_CHANGE' ) {
			//попытка найти
			$old_book = ['chapters' => []];
			foreach ( $old_bible['books'] as $b ) {
				if ( $b['id'] == $bookCode ) {
					$old_book = $b;
					$compute_chapters = false;
					break;
				}
			}
			$bookArray = $old_book;
		}
		else {
			$bookArray = [];
			$bookArray['chapters'] = [];
		}
		$bookArray['id'] = $bookCode;
		$book_info = get_book_info($bookCode);
		$bookArray['code'] = $book_info['code'];
		$bookArray['shortName'] = $book_info['shortName'][$bible['lang']];
		$bookArray['fullName'] = $book_info['fullName'][$bible['lang']];
		
		$book0 = str_pad($bookCode, 2, '0', STR_PAD_LEFT);
		
		print("Book $bookCode ... ");
	
		foreach($book['chapters'] as $chapter) {
			$chapterCode = $chapter['id'];
			
			if ( $chapterCode > $chapters_limit ) continue;
			
			$chapter0 = str_pad($chapterCode, 2, '0', STR_PAD_LEFT);
			
			array_push($bookArray['chapters'], ['id'=>$chapterCode, 'verses'=>get_formatted_chapter_timecodes_mfa($book0, $chapter0)]);
		}
		
		array_push($bible['books'], $bookArray);
		
		$filenameB = "audio/mp3/$book0/timecodes.json";
		
		if ( !file_exists(dirname($filenameB)) )
			mkdir(dirname($filenameB), 0777, true);

		file_put_contents($filenameB, json_encode($bookArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			
		print("Done!\n");
	}
	
	file_put_contents($filename, json_encode($bible, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	
	print("\nResult saved to $filename\n\n");
}

function get_formatted_chapter_timecodes_mfa($book, $chapter) 
{
	global $entries;
	
	$filename_json = "audio/mfa_output/${book}/${chapter}.json";
	if ( !file_exists($filename_json) ) {
		print("$filename_json does not exists!\n\n");
		return;
	}
	$mfa_json = json_decode(file_get_contents($filename_json), true);
	$entries = $mfa_json['tiers']['words']['entries'];
	
	$filename_text = "audio/mfa_input/${book}/${chapter}.txt";
	if ( !file_exists($filename_text) ) 
		die("$filename_text does not exists!!!\n\n");
	$lines = file($filename_text);
	
	// $textline = implode(" ", $lines);
	// $textline = str_replace("\n", "", $textline);
	// $textline = preg_replace('/[^\p{L}\p{N}\s]/u', '', $textline);
	// $textwords = explode(" ", mb_strtolower($textline, 'UTF-8'));
	
	// foreach ( $entries as $entry ) {
		// $begin = $entry[0];
		// $end = $entry[1];
		// $entryword = $entry[2];
		
		// if ( $entryword == '' ) continue;
		
		// $textword = array_shift($textwords);
		
		// if ( $textword != $entryword ) {
			// print("$textword != $entryword");
			// die();
		// }
		
		// print("entryword: $entryword, textword: $textword, begin: $begin, end: $end \n");
		// break;
	// }
	
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

$translation = determine_audio_translation();
$voice = determine_voice_4bbl($translation);
$mode = determine_mode();
$step = determine_step();

if ( $step == 1 )
	step1($mode, $translation, $voice);
else
	step2($mode, $translation, $voice);
	
//delete_temporary_files_aenaes();

// print("Success!\n\n"); 