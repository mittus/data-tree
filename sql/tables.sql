CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `objects` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent` int(20) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `objects` ADD CONSTRAINT `objects_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `objects` (`id`) ON DELETE CASCADE;