<?php

function determine_translation()
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

function determine_voice()
{
	global $argv;
	
	if ( !isset($argv[2]) )
		die("\nERROR: Set voice var! \nExample usage: \n$ php timecodes.php syn syn-bondarenko\n\n");
	
	$voice = $argv[2];
	
	$url = get_chapter_audio_url($voice, '01', '01');
	
	if ( !file_get_contents($url) )
		die("Voice not found (example url: $url)");
	
	return $voice;
}

function get_chapter_audio_url($voice, $book, $chapter)
{
	return 'https://4bbl.ru/data/' . $voice . '/' . str_pad($book, 2, '0', STR_PAD_LEFT) . '/' . str_pad($chapter, 2, '0', STR_PAD_LEFT) . '.mp3';
}

function get_translation_array($translation)
{
	$filename = "bible/$translation.json";
	$translationArray = json_decode(file_get_contents($filename), true);
	
	return $translationArray;
}

function create_chapter_plain($translationArrayBookChapter, $book_chapter, $chapter)
{
	$filename = 'audio/' . $book_chapter . '.txt';
	
	$str = "Глава $chapter \n";
	
	foreach ($translationArrayBookChapter as $verse)
	{
		$str .= $verse['text'] . "\n";
	}
	
	file_put_contents($filename, $str);
	
	// print("Plain $filename created\n");
}

function download_chapter_audio($voice, $book_chapter, $book, $chapter)
{
	$filename = 'audio/' . $book_chapter . '.mp3';
	
	if ( !file_exists($filename) )
	{
		$url = get_chapter_audio_url($voice, $book, $chapter);
		file_put_contents($filename, file_get_contents($url));
		// print("Audio $filename downloaded\n");
	}
	else {
		// print("Audio $filename already exists\n");
	}
}

function create_chapter_timecodes($book_chapter)
{
	$filename = 'audio/' . $book_chapter . '.mp3';
	
	
	
	$cmd_aenaes = 'docker run --name aenaes --rm --volume "' . __DIR__ . '/audio:/data" aenaes ' .
		   'python -m aeneas.tools.execute_task ' .
		   '/data/' . $book_chapter . '.mp3 ' .
		   '/data/' . $book_chapter . '.txt ' .
		   '"task_language=rus|os_task_file_format=json|is_text_type=plain" ' .
		   '/data/timecodes.json ' . 
		   '--presets-word --rate '
		   ;

	// echo $cmd_aenaes . "\n";
	if ( exec($cmd_aenaes , $output) ) { //, $retval
		
		foreach ($output as $line) {
			//print(strpos($line, '[ERRO]') . "\n");
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

function get_formatted_chapter_timecodes()
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

function delete_temporary_files()
{
	$filename = 'audio/timecodes.json';
	if ( file_exists($filename) )
		unlink($filename);
	
	array_map('unlink', glob('audio/*.txt'));
	
	// print("Temporary files deleted\n");
}

function create_all_formatted_timecodes($translation, $voice)
{
	$filename = "audio/$translation.json";
	
	$translationArray = get_translation_array($translation);
	
	$bible = [];
	$bible['code'] = $translation;
	$bible['books'] = [];
	// if ( file_exists($filename) )
		// $bible = json_decode(file_get_contents($filename), true); // чтоб все не переделывать
	
	foreach($translationArray['books'] as $book)
	{
		$bookCode = $book['id'];
		
		//if ( $bookCode < 40 or $bookCode > 43 ) continue; // Только Евангелия
		
		$bookArray = [];
		$bookArray['id'] = $bookCode;
		$bookArray['chapters'] = [];
		
		// $bible[$bookCode] = [];
		
		print("Book $bookCode ... ");
		
		foreach($book['chapters'] as $chapter)
		{
			$chapterCode = $chapter['id'];
			
			// if ( $chapterCode > 2 ) break;
			
			$book_chapter = str_pad($bookCode, 2, '0', STR_PAD_LEFT) . '_' . str_pad($chapterCode, 2, '0', STR_PAD_LEFT);
			
			create_chapter_plain($chapter['verses'], $book_chapter, $chapterCode);
			download_chapter_audio($voice, $book_chapter, $bookCode, $chapterCode);
			create_chapter_timecodes($book_chapter);
			
			array_push($bookArray['chapters'], ['id'=>$chapterCode, 'verses'=>get_formatted_chapter_timecodes()]);
			
			print("$chapterCode ");
		}
		
		array_push($bible['books'], $bookArray);
		
		print("Done!\n");
	}
	
	file_put_contents($filename, json_encode($bible, JSON_PRETTY_PRINT));
	
	print("\nResult saved to $filename\n\n");
}

$translation = determine_translation();
$voice = determine_voice();

create_all_formatted_timecodes($translation, $voice);

delete_temporary_files();

print("Success!\n\n");