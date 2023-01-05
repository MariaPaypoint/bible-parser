<?php

function determine_translation()
{
	global $argv;
	
	if ( !isset($argv[1]) )
		die("\nERROR: Set translation var! \nExample usage: \n$ php alignment.php syn syn-bondarenko\n\n");
	
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
		die("\nERROR: Set voice var! \nExample usage: \n$ php alignment.php syn syn-bondarenko\n\n");
	
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
	
	foreach ($translationArrayBookChapter as $key => $value)
	{
		$str .= $value . "\n";
	}
	
	file_put_contents($filename, $str);
	
	//print("Plain $filename created\n");
}

function download_chapter_audio($voice, $book_chapter, $book, $chapter)
{
	$filename = 'audio/' . $book_chapter . '.mp3';
	
	if ( !file_exists($filename) )
	{
		$url = get_chapter_audio_url($voice, $book, $chapter);
		file_put_contents($filename, file_get_contents($url));
		//print("Audio $filename downloaded\n");
	}
	else {
		//print("Audio $filename already exists\n");
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

	//echo $cmd_aenaes . "\n";
	if ( exec($cmd_aenaes , $output) ) { //, $retval
		//print("Timecodes audio/timecodes.json generated\n");
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
		
		$formatted[$key] = [];
		$formatted[$key]["begin"] = $value["begin"];
		$formatted[$key]["end"] = $value["end"];
	}
	
	//print("Formatting done\n");
	
	return $formatted;
}

function delete_temporary_files()
{
	unlink('audio/timecodes.json');
	array_map('unlink', glob('audio/*.txt'));
	
	print("Temporary files deleted\n");
}

function create_all_formatted_timecodes($translation, $voice)
{
	$translationArray = get_translation_array($translation);
	
	$resultArray = [];
	
	foreach($translationArray as $bookCode => $bookArray)
	{
		//if ( $bookCode < 40 or $bookCode > 43 ) continue; // Только Евангелия
		
		$resultArray[$bookCode] = [];
		
		print("Book $bookCode ... ");
		
		foreach($bookArray as $chapterCode => $chapterArray)
		{
			//if ( $chapterCode > 2 ) break;
			
			$book_chapter = str_pad($bookCode, 2, '0', STR_PAD_LEFT) . '_' . str_pad($chapterCode, 2, '0', STR_PAD_LEFT);
			
			create_chapter_plain($chapterArray, $book_chapter, $chapterCode);
			download_chapter_audio($voice, $book_chapter, $bookCode, $chapterCode);
			create_chapter_timecodes($book_chapter);
			
			$resultArray[$bookCode][$chapterCode] = get_formatted_chapter_timecodes();
			
			print("$chapterCode ");
		}
		
		print("Done!\n");
	}
	
	
	$filename = "audio/$translation.json";
	file_put_contents($filename, json_encode($resultArray, JSON_PRETTY_PRINT));
	
	print("\nResult saved to $filename\n\n");
}

$translation = determine_translation();
$voice = determine_voice();

create_all_formatted_timecodes($translation, $voice);

delete_temporary_files();

print("Success!\n\n");