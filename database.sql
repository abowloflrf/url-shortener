CREATE TABLE `url` (
	`key` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`url_short` VARCHAR(6) NOT NULL DEFAULT '0' COLLATE 'utf8_bin',
	`url_full` TEXT NULL COLLATE 'utf8_bin',
	`click` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`key`),
	UNIQUE INDEX `url_short` (`url_short`)
)
COLLATE='utf8_bin'
ENGINE=InnoDB
;