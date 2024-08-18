<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitDB extends AbstractMigration
{
    public function up()
    {
		$this->execute("CREATE TABLE IF NOT EXISTS `languages` (
		  `alias` varchar(10) NOT NULL,
		  `name_en` varchar(255) NOT NULL,
		  `name_national` varchar(255) NOT NULL,
		  PRIMARY KEY (`alias`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
		");
		$this->execute("CREATE TABLE IF NOT EXISTS `bible_translations` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `alias` varchar(50) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `description` varchar(255) DEFAULT NULL,
		  `language` varchar(10) NOT NULL,
		  PRIMARY KEY (`code`),
		  KEY `language_idx` (`language`),
		  CONSTRAINT `bible_translations_language` FOREIGN KEY (`language`) REFERENCES `languages` (`alias`)
		) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;
		");
		$this->execute("CREATE TABLE IF NOT EXISTS `bible_books` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `book_number` smallint NOT NULL COMMENT 'from table dict_universal_books',
		  `bible_translation` int NOT NULL,
		  `name` varchar(255) NOT NULL,
		  PRIMARY KEY (`code`),
		  KEY `bible_translation_idx` (`bible_translation`),
		  CONSTRAINT `bible_books_bible_translation` FOREIGN KEY (`bible_translation`) REFERENCES `bible_translations` (`code`)
		) ENGINE=InnoDB AUTO_INCREMENT=263 DEFAULT CHARSET=utf8mb3;
		");
		$this->execute("CREATE TABLE IF NOT EXISTS `bible_verses` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `verse_number` smallint NOT NULL,
		  `chapter_number` smallint NOT NULL,
		  `bible_book` int NOT NULL,
		  `text` varchar(10000) NOT NULL,
		  `start_paragraph` tinyint(1) NOT NULL,
		  PRIMARY KEY (`code`),
		  KEY `bible_book_idx` (`bible_book`),
		  CONSTRAINT `bible_verses_bible_book` FOREIGN KEY (`bible_book`) REFERENCES `bible_books` (`code`)
		) ENGINE=InnoDB AUTO_INCREMENT=109329 DEFAULT CHARSET=utf8mb3;
		");
		$this->execute("CREATE TABLE IF NOT EXISTS `bible_notes` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `bible_verse` int NOT NULL,
		  `position` smallint NOT NULL,
		  `note_number` int NOT NULL,
		  `text` varchar(10000) NOT NULL,
		  PRIMARY KEY (`code`),
		  KEY `bible_verse_idx` (`bible_verse`),
		  CONSTRAINT `bible_notes_bible_verse` FOREIGN KEY (`bible_verse`) REFERENCES `bible_verses` (`code`)
		) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;
		");
		$this->execute("CREATE TABLE IF NOT EXISTS `audio_voices` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `alias` varchar(50) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `description` varchar(1000) DEFAULT NULL,
		  `bible_translation` int NOT NULL,
		  `is_music` tinyint(1) NOT NULL,
		  PRIMARY KEY (`code`),
		  KEY `bible_translation_idx` (`bible_translation`),
		  CONSTRAINT `audio_voices_bible_translation` FOREIGN KEY (`bible_translation`) REFERENCES `bible_translations` (`code`)
		) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
		");
        $this->execute("CREATE TABLE IF NOT EXISTS `audio_alignments` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `audio_voice` int NOT NULL,
		  `bible_verse` int NOT NULL,
		  `begin` float NOT NULL,
		  `end` float NOT NULL,
		  `is_correct` tinyint(1) DEFAULT NULL,
		  PRIMARY KEY (`code`),
		  KEY `audio_alignments_audio_voice_idx` (`audio_voice`),
		  CONSTRAINT `audio_alignments_audio_voice` FOREIGN KEY (`audio_voice`) REFERENCES `audio_voices` (`code`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
		");
        $this->execute("CREATE TABLE `keyword_values` (
  		  `inc` INT NOT NULL,
  		  `group_alias` VARCHAR(50) NOT NULL,
      		  `lang` VARCHAR(10) NULL,
 		  `alias` VARCHAR(50) NOT NULL,
 		  `name` VARCHAR(1000) NOT NULL,
 		  `description` VARCHAR(1000) NULL,
 		  PRIMARY KEY (`inc`)
    		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
		");

	    

    }
}
