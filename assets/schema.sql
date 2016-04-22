
CREATE TABLE `fastest_connection` (
  `departureTime` int(10) unsigned NOT NULL DEFAULT '0',
  `arrivalTime` int(10) unsigned NOT NULL DEFAULT '0',
  `origin` char(4) NOT NULL DEFAULT '',
  `destination` char(4) NOT NULL DEFAULT '',
  `service` char(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`departureTime`,`arrivalTime`,`origin`,`destination`,`service`),
  KEY `departureTime` (`departureTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `timestamp_connection` (
  `departureTime` int(10) unsigned NOT NULL DEFAULT '0',
  `arrivalTime` int(10) unsigned NOT NULL DEFAULT '0',
  `origin` char(4) NOT NULL DEFAULT '',
  `destination` char(4) NOT NULL DEFAULT '',
  `service` char(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`departureTime`,`arrivalTime`,`origin`,`destination`,`service`),
  KEY `departureTime` (`departureTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `shortest_path` (
  `origin` char(4) NOT NULL DEFAULT '',
  `destination` char(4) NOT NULL DEFAULT '',
  `duration` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`origin`,`destination`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `non_timetable_connection` (
  `origin` char(4) NOT NULL DEFAULT '',
  `destination` char(4) NOT NULL DEFAULT '',
  `duration` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`origin`,`destination`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `interchange` (
  `station` char(4) NOT NULL DEFAULT '',
  `duration` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`station`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
