CREATE TABLE `nbpldays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venue_id` int(11) NOT NULL,
  `nbplday` enum('Wednesday','Tuesday','Saturday','Friday','Thursday','Monday','Sunday') COLLATE utf8_bin NOT NULL DEFAULT 'Sunday',
  `nbplstarttime` time NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;