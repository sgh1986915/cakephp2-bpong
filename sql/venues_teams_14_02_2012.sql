CREATE TABLE IF NOT EXISTS `venues_teams` (
  `id` int(11) NOT NULL auto_increment,
  `team_id` int(11) NOT NULL default '0',
  `venue_id` int(11) NOT NULL default '0',
  `wins` int(11) NOT NULL default '0',
  `losses` int(11) NOT NULL default '0',
  `cupdif` int(11) NOT NULL default '0',
  `nbplpoints` int(11) NOT NULL default '0',
  `wins_ytd` int(11) NOT NULL default '0',
  `losses_ytd` int(11) NOT NULL default '0',
  `cupdif_ytd` int(11) NOT NULL default '0',
  `nbplpoints_ytd` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
