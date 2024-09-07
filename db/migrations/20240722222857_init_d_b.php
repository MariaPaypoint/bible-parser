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

		$this->execute("CREATE TABLE IF NOT EXISTS `translations` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `alias` varchar(50) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `description` varchar(255) DEFAULT NULL,
		  `language` varchar(10) NOT NULL,
		  PRIMARY KEY (`code`),
		  KEY `language_idx` (`language`),
		  CONSTRAINT `translations_language` FOREIGN KEY (`language`) REFERENCES `languages` (`alias`)
		) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;
		");
		$this->execute("CREATE TABLE IF NOT EXISTS `translation_books` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `book_number` smallint NOT NULL COMMENT 'from table dict_universal_books',
		  `translation` int NOT NULL,
		  `name` varchar(255) NOT NULL,
		  PRIMARY KEY (`code`),
		  KEY `translation_idx` (`translation`),
		  KEY `book_number_idx` (`book_number`),
		  CONSTRAINT `translation_books_translation` FOREIGN KEY (`translation`) REFERENCES `translations` (`code`)
		) ENGINE=InnoDB AUTO_INCREMENT=263 DEFAULT CHARSET=utf8mb3;
		");
		$this->execute("CREATE TABLE IF NOT EXISTS `translation_verses` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `verse_number` smallint NOT NULL,
		  `chapter_number` smallint NOT NULL,
		  `translation_book` int NOT NULL,
		  `text` varchar(10000) NOT NULL,
		  `start_paragraph` tinyint(1) NOT NULL,
		  PRIMARY KEY (`code`),
		  KEY `translation_book_idx` (`translation_book`),
		  KEY `chapter_number_idx` (`chapter_number`),
		  CONSTRAINT `translation_verses_translation_book` FOREIGN KEY (`translation_book`) REFERENCES `translation_books` (`code`)
		) ENGINE=InnoDB AUTO_INCREMENT=109329 DEFAULT CHARSET=utf8mb3;
		");
		$this->execute("CREATE TABLE IF NOT EXISTS `translation_notes` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `translation_verse` int NOT NULL,
		  `position` smallint NOT NULL,
		  `note_number` int NOT NULL,
		  `text` varchar(10000) NOT NULL,
		  PRIMARY KEY (`code`),
		  KEY `translation_verse_idx` (`translation_verse`),
		  CONSTRAINT `translation_notes_translation_verse` FOREIGN KEY (`translation_verse`) REFERENCES `translation_verses` (`code`)
		) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;
		");
		$this->execute("CREATE TABLE IF NOT EXISTS `translation_titles` (
			`code` int NOT NULL AUTO_INCREMENT,
			`before_translation_verse` int NOT NULL,
			`text` varchar(1000) NOT NULL,
			PRIMARY KEY (`code`)
		) ENGINE=InnoDB AUTO_INCREMENT=446 DEFAULT CHARSET=utf8mb3;
		");

		$this->execute("CREATE TABLE IF NOT EXISTS `voices` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `alias` varchar(50) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `description` varchar(1000) DEFAULT NULL,
		  `translation` int NOT NULL,
		  `is_music` tinyint(1) NOT NULL,
		  `link_template` VARCHAR(1000) NULL,
		  PRIMARY KEY (`code`),
		  KEY `translation_idx` (`translation`),
		  CONSTRAINT `voices_translation` FOREIGN KEY (`translation`) REFERENCES `translations` (`code`)
		) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
		");

        $this->execute("CREATE TABLE IF NOT EXISTS `voice_alignments` (
		  `code` int NOT NULL AUTO_INCREMENT,
		  `voice` int NOT NULL,
		  `translation_verse` int NOT NULL,
		  `begin` float NOT NULL,
		  `end` float NOT NULL,
		  `is_correct` tinyint(1) DEFAULT NULL,
		  PRIMARY KEY (`code`),
		  KEY `voice_alignments_voice_idx` (`voice`),
		  CONSTRAINT `voice_alignments_voice` FOREIGN KEY (`voice`) REFERENCES `voices` (`code`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
		");

        $this->execute("CREATE TABLE `keywords` (
  		  `inc` INT NOT NULL AUTO_INCREMENT,
  		  `group_alias` VARCHAR(50) NOT NULL,
      		  `lang` VARCHAR(10) NULL,
 		  `alias` VARCHAR(50) NOT NULL,
 		  `name` VARCHAR(1000) NOT NULL,
 		  `description` VARCHAR(1000) NULL,
 		  PRIMARY KEY (`inc`)
    	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
		");

		$this->execute("CREATE TABLE `bible_stat` (
			`inc` int NOT NULL AUTO_INCREMENT,
			`book_number` smallint NOT NULL,
			`chapter_number` smallint NOT NULL,
			`verses_count` smallint NOT NULL,
			`tolerance_count` smallint NOT NULL,
			PRIMARY KEY (`inc`)
		  ) ENGINE=InnoDB AUTO_INCREMENT=1180 DEFAULT CHARSET=utf8mb3;		  
		");
	   
    }
}
