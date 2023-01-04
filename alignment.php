<?php

/*
setlocale(LC_ALL, 'ru_RU.utf8');
libxml_use_internal_errors(true);
DEFINE('WRONG_TEXT', 'Если это кодекс, то возможно данный текст просто отсутсвует.');
*/

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

function get_chapter_audio_url($voice, $book, $chapter) {
	return 'https://4bbl.ru/data/' . $voice . '/' . $book . '/' . $chapter . '.mp3';
}

function get_translation_array($translation) {
	$filename = "bible/$translation.json";
	$translationArray = json_decode(file_get_contents($filename), true);
	
	//print_r($translationArray[$book][$chapter]);
	return $translationArray;
}

function create_chapter_plain($translationArray, $book, $chapter) {
	
	$book_chapter = $book . '_' . $chapter;
	$filename = 'tmp/' . $book_chapter . '.txt';
	
	$str = "Глава $chapter \n";
	
	//print_r($translationArray[$book][$chapter]);
	
	foreach ($translationArray[ltrim($book, '0')][ltrim($chapter, '0')] as $key => $value) {
		$str .= $value . "\n";
	}
	
	file_put_contents($filename, $str);
	
	print("Plain $filename created\n");
}

function create_timecodes($voice, $book, $chapter) {
	
	// скачивание главы mp3
	$book_chapter = $book . '_' . $chapter;
	$filename = 'tmp/' . $book_chapter . '.mp3';
	
	if ( !file_exists($filename) ) {
		$url = get_chapter_audio_url($voice, $book, $chapter);
		file_put_contents($filename, file_get_contents($url));
		print("Audio $filename downloaded\n");
	}
	else {
		print("Audio $filename already exists\n");
	}
	
	// timecodes json generation
	$cmd_aenaes = 'docker run --name aenaes --rm --volume "' . __DIR__ . '/tmp:/data" aenaes ' .
		   'python -m aeneas.tools.execute_task ' .
		   '/data/' . $book_chapter . '.mp3 ' .
		   '/data/' . $book_chapter . '.txt ' .
		   '"task_language=rus|os_task_file_format=json|is_text_type=plain" ' .
		   '/data/timecodes.json ' . 
		   '--presets-word --rate '
		   ;

	//echo $cmd_aenaes . "\n";
	exec($cmd_aenaes , $output); //, $retval
	//print_r($output);
	print("Timecodes tmp/timecodes.json generated\n");
}

function format_timecodes($book, $chapter) {
	$filenameT = 'tmp/timecodes.json';
	
	$timecodesArray = json_decode(file_get_contents($filenameT), true);
	
	//print_r($timecodesArray["fragments"][0]);
	
	$formatted = [];
	
	foreach ($timecodesArray["fragments"] as $key => $value) {
		
		if ( $key == 0 )
			continue; // name of chapter
		
		$formatted[$key] = [];
		$formatted[$key]["begin"] = $value["begin"];
		$formatted[$key]["end"] = $value["end"];
	}
	
	//print_r($formatted);
	$book_chapter = $book . '_' . $chapter;
	$filenameF = 'tmp/' . $book_chapter . '.json';
	file_put_contents($filenameF, json_encode($formatted));
	
	print("Format $filenameT done\n");
	//return $formatted;
}

function delete_temporary_files($book, $chapter) {
	unlink('tmp/timecodes.json');
	
	$book_chapter = $book . '_' . $chapter;
	$filenameP = 'tmp/' . $book_chapter . '.txt';
	unlink($filenameP);
	
	print("Temporary files deleted\n");
}

$translation = determine_translation();
$voice = determine_voice();

$book = '02';
$chapter = '02';

$translationArray = get_translation_array($translation);

create_chapter_plain($translationArray, $book, $chapter);

create_timecodes($voice, $book, $chapter);
format_timecodes($book, $chapter);

delete_temporary_files($book, $chapter);

print("Success!\n\n");