
DROP TABLE IF EXISTS `fastest_connection`;
CREATE TABLE `fastest_connection` (
  `departureTime` TIME DEFAULT NULL,
  `arrivalTime` TIME DEFAULT NULL,
  `origin` char(3) NOT NULL,
  `destination` char(3) NOT NULL,
  `service` char(8) NOT NULL,
  PRIMARY KEY (`departureTime`,`arrivalTime`,`origin`,`destination`,`service`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `timetable_connection`;
CREATE TABLE `timetable_connection` (
  `departureTime` TIME DEFAULT NULL,
  `arrivalTime` TIME DEFAULT NULL,
  `origin` char(3) NOT NULL,
  `destination` char(3) NOT NULL,
  `service` VARCHAR(8) NOT NULL,
  `monday` TINYINT(1) NOT NULL,
  `tuesday` TINYINT(1) NOT NULL,
  `wednesday` TINYINT(1) NOT NULL,
  `thursday` TINYINT(1) NOT NULL,
  `friday` TINYINT(1) NOT NULL,
  `saturday` TINYINT(1) NOT NULL,
  `sunday` TINYINT(1) NOT NULL,
  `startDate` DATE NOT NULL,
  `endDate` DATE NOT NULL,
  `dummy` char(3) DEFAULT '---',
  `dummy2` TIME DEFAULT NULL,
  PRIMARY KEY (`departureTime`,`arrivalTime`,`origin`,`destination`,`service`, `endDate`),
  KEY `startDate` (`startDate`),
  KEY `endDate` (`endDate`),
  KEY `origin` (`origin`),
  KEY `destination` (`destination`),
  KEY `arrivalTime` (`arrivalTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `shortest_path`;
CREATE TABLE `shortest_path` (
  `origin` char(3) NOT NULL,
  `destination` char(3) NOT NULL,
  `duration` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`origin`,`destination`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `non_timetable_connection`;
CREATE TABLE `non_timetable_connection` (
  `id` INT(8) unsigned AUTO_INCREMENT,
  `origin` char(3) NOT NULL,
  `destination` char(3) NOT NULL,
  `duration` int(11) unsigned NOT NULL,
  `mode` VARCHAR(20) NOT NULL,
  `startTime` TIME NOT NULL,
  `endTime` TIME NOT NULL,
  `startDate` DATE NOT NULL,
  `endDate` DATE NOT NULL,
  `monday` TINYINT(1) NOT NULL,
  `tuesday` TINYINT(1) NOT NULL,
  `wednesday` TINYINT(1) NOT NULL,
  `thursday` TINYINT(1) NOT NULL,
  `friday` TINYINT(1) NOT NULL,
  `saturday` TINYINT(1) NOT NULL,
  `sunday` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `origin` (`origin`),
  KEY `destination` (`destination`),
  KEY `startDate` (`startDate`),
  KEY `endDate` (`endDate`),
  KEY `startTime` (`startTime`),
  KEY `endTime` (`endTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `interchange`;
CREATE TABLE `interchange` (
  `station` char(3) NOT NULL,
  `duration` int(11) unsigned NOT NULL,
  PRIMARY KEY (`station`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
