ALTER TABLE  `events` CHANGE  `type`  `type` ENUM(  'default',  'tournament',  'nbplweekly',  'wsobp',  'nbplsatellite' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT  'tournament';
