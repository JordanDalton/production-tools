/*DDL Information*/
-------------------

CREATE TABLE `gui_wor` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `author` VARCHAR(100) DEFAULT 'NA' COMMENT 'Name of the person who created the record.',
  `bo_value` FLOAT DEFAULT NULL COMMENT 'Back Order Value',
  `created` INT(10) NOT NULL COMMENT 'Timestamp of when records was created.',
  `headings` TEXT COMMENT 'JSON encoded thead info.',
  `record_count` INT(11) DEFAULT NULL COMMENT 'Number of records (rows).',
  `report` VARCHAR(255) DEFAULT 'Not Specified' COMMENT 'The name of the report that the data is for.',
  `rows` LONGTEXT COMMENT 'PHP serialized row data.',
  `updated` INT(10) DEFAULT NULL COMMENT 'Timestamp of the update.',
  `work_center_groups` LONGTEXT COMMENT 'PHP serialized work center groups',
  `work_centers` TEXT COMMENT 'PHP serialized work center(s)',
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8