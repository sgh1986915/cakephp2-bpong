ALTER TABLE  `venues` ADD  `nbplstarttime` TIME NOT NULL DEFAULT  '0' AFTER  `nbplday` ;
ALTER TABLE  `venues` ADD  `timezone_id` INT( 11 ) NOT NULL DEFAULT  '-5' AFTER  `nbplstarttime` ;
ALTER TABLE  `packages` CHANGE  `people_in_room`  `people_in_room` TINYINT( 4 ) UNSIGNED NULL DEFAULT  '0';