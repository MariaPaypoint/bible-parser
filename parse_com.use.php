<?php

require 'parse_com.class.php';
require 'include.php'; 

$translation = $argv[1];

$only_book = 40;   // Укажите номер книги, если нужно обработать только определённую книгу
$only_chapter = 1; // Укажите номер главы, если нужно обработать только определённую главу

$parser = new BibleParser($translation, $only_book, $only_chapter);
$parser->parse();
