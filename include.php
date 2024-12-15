<?php

require "config.php";
require "const.php";

function get_db_cursor()
{
	$mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_SCHEMA, MYSQL_PORT);
	return $mysqli;
}

// получение входящих параметров

function determine_text_translation($position=1)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("\nERROR: Set translation var! \nExample usage: \n$ php timecodes.php syn bondarenko\n\n");
	
	$translation = $argv[$position];
	$filename = "text/$translation.json";
	
	if ( !file_exists($filename) )
		die("Translation not found (expected: $filename)\n\n");
	
	return $translation;
}

function determine_voice_4bbl($translation, $position=2)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("\nERROR: Set voice var! \nExample usage: \n$ php timecodes.php syn bondarenko\n\n");
	
	$voice = $argv[$position];
	
	$url = get_chapter_audio_url($translation, $voice, 1, 1);
	
	if ( !file_get_contents($url) )
		die("Voice not found (example url: $url)\n\n");
	
	return $voice;
}

function determine_mode($position=3)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("Mode is not set (wait one of: MODE_REPLACE, MODE_CHANGE, MODE_FINISH)\n\n");
	
	$mode = $argv[$position];
	
	if ( !in_array($mode, ['MODE_REPLACE', 'MODE_CHANGE', 'MODE_FINISH']) )
		die("Unknown mode: $mode (wait one of: MODE_REPLACE, MODE_CHANGE, MODE_FINISH)\n\n");
	
	return $mode;
}

function determine_step($position=4)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("Step is not set (wait one of: 1, 2)\n\n");
	
	$step = $argv[$position];
	
	if ( !in_array($step, ['1', '2']) )
		die("Unknown step: $step (wait one of: 1, 2)\n\n");
	
	return $step;
}

function determine_export_type($position=2)
{
	global $argv;
	
	if ( !isset($argv[$position]) )
		die("Export type is not set (wait one of: TEXT, TIMECODES)\n\n");
	
	$step = $argv[$position];
	
	if ( !in_array($step, ['TEXT', 'TIMECODES']) )
		die("Unknown export type: $step (wait one of: TEXT, TIMECODES)\n\n");
	
	return $step;
}

// для аудио


function get_translation_array($translation)
{
	$filename = "text/$translation.json";
	$translationArray = json_decode(file_get_contents($filename), true);
	
	return $translationArray;
}

function get_voice_array($translation, $voice)
{
	$filename = "audio/$translation/$voice/timecodes.json";
	$voiceArray = json_decode(file_get_contents($filename), true);
	
	return $voiceArray;
}

function get_chapter_name_1($digit) 
{
	switch ($digit)
	{
		case 0 : return '';
		case 1 : return 'первая';
		case 2 : return 'вторая';
		case 3 : return 'третья';
		case 4 : return 'четвертая';
		case 5 : return 'пятая';
		case 6 : return 'шестая';
		case 7 : return 'седьмая';
		case 8 : return 'восьмая';
		case 9 : return 'девятая';
	}
}
function get_chapter_name_2($digit) 
{
	switch ($digit)
	{
		case 10 : return 'десятая';
		case 11 : return 'одиннадцатая';
		case 12 : return 'двенадцатая';
		case 13 : return 'тринадцатая';
		case 14 : return 'четырнадцатая';
		case 15 : return 'пятнадцатая';
		case 16 : return 'шестнадцатая';
		case 17 : return 'семнадцатая';
		case 18 : return 'восемнадцатая';
		case 19 : return 'девятнадцатая';
	}
}
function get_chapter_name_3($digit, $zero) 
{
	switch ($digit)
	{
		case 2 : return $zero ? 'двадцатая'     : 'двадцать';
		case 3 : return $zero ? 'тридцатая'     : 'тридцать';
		case 4 : return $zero ? 'сороковая'     : 'сорок';
		case 5 : return $zero ? 'пятидесятая'   : 'пятьдесят';
		case 6 : return $zero ? 'шестидесятая'  : 'шестьдесят';
		case 7 : return $zero ? 'семидесятая'   : 'семьдесят';
		case 8 : return $zero ? 'восьмидесятая' : 'восемьдесят';
		case 9 : return $zero ? 'девяностая'    : 'девяносто';
	}
}

function get_chapter_name($chapter)
{
	if ( $chapter <= 9 )
		return get_chapter_name_1($chapter);
	
	elseif ( $chapter <= 19 )
		return get_chapter_name_2($chapter);
	
	elseif ( $chapter == 100 )
		return 'сотая';
	
	else
		return ($chapter > 100 ? 'сто ' : '') . get_chapter_name_3( round($chapter / 10), $chapter%10==0 ) . ' ' . get_chapter_name_1($chapter % 10);
}

function get_ps_name_1($digit) 
{
	switch ($digit)
	{
		case 1 : return 'первый';
		case 2 : return 'второй';
		case 3 : return 'третий';
		case 4 : return 'четвертый';
		case 5 : return 'пятый';
		case 6 : return 'шестой';
		case 7 : return 'седьмой';
		case 8 : return 'восьмой';
		case 9 : return 'девятый';
	}
}
function get_ps_name_2($digit) 
{
	switch ($digit)
	{
		case 10 : return 'десятый';
		case 11 : return 'одиннадцатый';
		case 12 : return 'двенадцатый';
		case 13 : return 'тринадцатый';
		case 14 : return 'четырнадцатый';
		case 15 : return 'пятнадцатый';
		case 16 : return 'шестнадцатый';
		case 17 : return 'семнадцатый';
		case 18 : return 'восемнадцатый';
		case 19 : return 'девятнадцатый';
	}
}

function get_ps_name_3($digit, $zero) 
{
	switch ($digit)
	{
		case 2 : return $zero ? 'двадцатый'     : 'двадцать';
		case 3 : return $zero ? 'тридцатый'     : 'тридцать';
		case 4 : return $zero ? 'сороковой'     : 'сорок';
		case 5 : return $zero ? 'пятидесятый'   : 'пятьдесят';
		case 6 : return $zero ? 'шестидесятый'  : 'шестьдесят';
		case 7 : return $zero ? 'семидесятый'   : 'семьдесят';
		case 8 : return $zero ? 'восьмидесятый' : 'восемьдесят';
		case 9 : return $zero ? 'девяностый'    : 'девяносто';
	}
}
function get_ps_name($chapter)
{
	if ( $chapter <= 9 )
		return get_ps_name_1($chapter);
	
	elseif ( $chapter <= 19 )
		return get_ps_name_2($chapter);
	
	elseif ( $chapter == 100 )
		return 'сотый';
	
	else
		return ($chapter > 100 ? 'сто ' : '') . get_ps_name_3( round($chapter / 10), $chapter%10==0 ) . ' ' . get_ps_name_1($chapter % 10);
}

function sprintfn ($format, array $args = array()) {
    // map of argument names to their corresponding sprintf numeric argument value
    $arg_nums = array_slice(array_flip(array_keys(array(0 => 0) + $args)), 1);

    // find the next named argument. each search starts at the end of the previous replacement.
    for ($pos = 0; preg_match('/(?<=%)([a-zA-Z_]\w*)(?=\$)/', $format, $match, PREG_OFFSET_CAPTURE, $pos);) {
        $arg_pos = $match[0][1];
        $arg_len = strlen($match[0][0]);
        $arg_key = $match[1][0];

        // programmer did not supply a value for the named argument found in the format string
        if (! array_key_exists($arg_key, $arg_nums)) {
            user_error("sprintfn(): Missing argument '$arg_key'", E_USER_WARNING);
            return false;
        }

        // replace the named argument with the corresponding numeric one
        $format = substr_replace($format, $replace = $arg_nums[$arg_key], $arg_pos, $arg_len);
        $pos = $arg_pos + strlen($replace); // skip to end of replacement for next iteration
    }

    return vsprintf($format, array_values($args));
}

function get_chapter_audio_url($translation, $voice, $book, $chapter)
{
	$voice_info = get_voice_info($voice);
	$book_info = get_book_info($book);
	
	$link = sprintfn($voice_info['link'], [
		'voice'       => $voice,
		'book'        => $book,
		'chapter'     => $chapter,
		'book0'       => str_pad($book, 2, '0', STR_PAD_LEFT),
		'chapter0'    => str_pad($chapter, 2, '0', STR_PAD_LEFT),
		'bookCode'    => $book_info['code'],
		'translation' => $translation
	]);
	return $link;
	//return 'https://4bbl.ru/data/' . $voice . '/' . str_pad($book, 2, '0', STR_PAD_LEFT) . '/' . str_pad($chapter, 2, '0', STR_PAD_LEFT) . '.mp3';
}

function download_chapter_audio($translation, $voice, $book, $chapter)
{
	$book0    = str_pad($book, 2, '0', STR_PAD_LEFT);
	$chapter0 = str_pad($chapter, 2, '0', STR_PAD_LEFT);
	
	$filename = "audio/$translation/$voice/mp3/$book0/$chapter0.mp3";
	
	if ( !file_exists($filename) )
	{
		$url = get_chapter_audio_url($translation, $voice, $book, $chapter);
		
		if ( !file_exists(dirname($filename)) )
			mkdir(dirname($filename), 0755, true);

		file_put_contents($filename, file_get_contents($url));
		// print("Audio $filename downloaded\n");
	}
	else {
		// print("Audio $filename already exists\n");
	}
}

function create_dir777_if_not_exists($dirname, $clear=False) 
{
	if ( $clear && file_exists($dirname) )
		rmdir_recursive($dirname);

	if ( !file_exists($dirname) )
		mkdir($dirname, 0777, true);
	
	chmod($dirname, 0777);
}

function convert_mp3_to_vaw($translation, $voice, $book, $chapter)
{
	$filename_source = "audio/$translation/$voice/mp3/$book/$chapter.mp3";
	$filename_destination = "audio/$translation/$voice/mfa_input/$book/$chapter.wav";
	
	$file_exists = file_exists($filename_destination);
	
	if ( !$file_exists ) 
	{
		create_dir777_if_not_exists(dirname($filename_destination));
		
		$cmd_ffmpeg = "docker run --name ffmpeg --rm --volume " . __DIR__ . "/audio:/audio linuxserver/ffmpeg -hide_banner -loglevel error -i /$filename_source /$filename_destination";
		// die($cmd_ffmpeg . "\n");
		
		$exec_result = exec($cmd_ffmpeg, $output, $retval);
		if ( $exec_result or $retval==0 ) { //
			// print("File $filename_destination created\n"); 
		}
		else {
			if ( $output )
				print_r($output);
		}
	}
	else {
		// print("File $filename_destination already exists\n");
	}
}

function containsLetter($str) {
    // Регулярное выражение для поиска хотя бы одной буквы (латиница и кириллица)
    return preg_match('/[a-zA-Zа-яА-Я]/u', $str) === 1;
}

function removeBrackets($str) {
    // Удаляем только скобки, но оставляем содержимое внутри
    $str = str_replace(['[', ']', '(', ')', '<', '>'], '', $str);
    return $str;
}

function deleteTxtFiles($directory) {
    // Проверяем, существует ли директория
    if (!is_dir($directory)) {
        echo "Указанная директория не существует.";
        return;
    }

    // Добавляем разделитель директорий, если его нет
    if (substr($directory, -1) !== DIRECTORY_SEPARATOR) {
        $directory .= DIRECTORY_SEPARATOR;
    }

    // Получаем все файлы с расширением .txt в директории
    $files = glob($directory . '*.txt');

    // Проходим по каждому файлу и удаляем его
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}

function rmdir_recursive($path) {
	if (is_file($path)) return unlink($path);
	if (is_dir($path)) {
		foreach(scandir($path) as $p) if (($p!='.') && ($p!='..'))
			rmdir_recursive($path.DIRECTORY_SEPARATOR.$p);
		return rmdir($path); 
    }
	return false;
}

function exec_and_print($cmd, $return_error=False) 
{
	print "CMD: $cmd...";
	
	$result = exec($cmd, $output);
	
	print ($result ? "OK" : "ERROR") . "\n";
	foreach($output as $str) {
		print "    " . $str . "\n\n";
	}
	
	if ( $result )
		return True;
	else
	{
		if ( $return_error )
			return False;
		else
			die();
	}

}
