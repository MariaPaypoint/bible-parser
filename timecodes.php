<?php

require 'include.php';

function create_chapter_timecodes_aenaes($book, $chapter)
{
	$cmd_aenaes = 'docker run --name aenaes --rm --volume "' . __DIR__ . '/audio:/data" aenaes ' .
		   'python -m aeneas.tools.execute_task ' .
		   "/data/$book/$chapter.mp3 " .
		   "/data/text.txt " .
		   '"task_language=rus|os_task_file_format=json|is_text_type=plain" ' .
		   '/data/timecodes.json ' . 
		   '--presets-word --rate '
		   ;
	// echo $cmd_aenaes . "\n";
	
	if ( exec($cmd_aenaes , $output) ) { //, $retval
		
		foreach ($output as $line) {
			if ( strpos($line, '[ERRO]') !== false ) {
				print_r($output);
				
				print('-----------------------------------------------------'."\n");
				print('Advice to test: docker run --name aenaes --rm --volume "/root/cep/bible-by-grabber/audio:/data" -d aenaes sleep 10000; docker exec -it aenaes bash'."\n");
				print('Advice to debug: add -v to aenaes parameters'."\n");
				print('Advice to fix:  try to remove metadata from audio'."\n");
				print('-----------------------------------------------------'."\n");
				
				die();
			}
		}
		// print("Timecodes audio/timecodes.json generated\n");
	}
	else
		print_r($output);
}

function get_formatted_chapter_timecodes_aenaes()
{
	$filenameT = 'audio/timecodes.json';
	
	$timecodesArray = json_decode(file_get_contents($filenameT), true);
	
	$formatted = [];
	
	foreach ($timecodesArray["fragments"] as $key => $value)
	{
		if ( $key == 0 )
			continue; // name of chapter
		
		$verse = [];
		$verse["id"] = $key;
		$verse["begin"] = $value["begin"];
		$verse["end"] = $value["end"];
		
		array_push($formatted, $verse);
	}
	
	// print("Formatting done\n");
	
	return $formatted;
}

function delete_temporary_files_aenaes()
{
	$filename = 'audio/timecodes.json';
	if ( file_exists($filename) )
		unlink($filename);
	
	array_map('unlink', glob('audio/*.txt'));
	
	// print("Temporary files deleted\n");
}

function create_all_formatted_timecodes($mode, $translation, $voice)
{
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
		//if ( $bookCode > 2 ) continue;
		
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
	
		foreach($book['chapters'] as $chapter)
		{
			$chapterCode = $chapter['id'];
			
			//if ( $chapterCode > 2 ) continue;
			
			$chapter0 = str_pad($chapterCode, 2, '0', STR_PAD_LEFT);
			
			download_chapter_audio($translation, $voice, $book0, $chapter0);
			if ( $compute_chapters ) {
				create_chapter_plain($chapter['verses'], $bookCode, $chapterCode, $bible['lang'], 'audio/text.txt');
				create_chapter_timecodes_aenaes($book0, $chapter0);
				array_push($bookArray['chapters'], ['id'=>$chapterCode, 'verses'=>get_formatted_chapter_timecodes_aenaes()]);
			}
			print("$chapterCode ");
		}
	
		array_push($bible['books'], $bookArray);
		
		$filenameB = "audio/$book0/timecodes.json";
		
		if ( !file_exists(dirname($filenameB)) )
			mkdir(dirname($filenameB), 0777, true);

		file_put_contents($filenameB, json_encode($bookArray, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
			
		print("Done!\n");
	}
	
	file_put_contents($filename, json_encode($bible, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
	
	print("\nResult saved to $filename\n\n");
}

$translation = determine_text_translation();
$voice = determine_voice_4bbl($translation);
$mode = determine_mode();

create_all_formatted_timecodes($mode, $translation, $voice);

delete_temporary_files_aenaes();

print("Success!\n\n");