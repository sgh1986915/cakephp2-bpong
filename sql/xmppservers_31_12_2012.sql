CREATE TABLE `xmppservers` (
  `id` int(11) NOT NULL auto_increment,
  `hostname` varchar(255) NOT NULL,
  `latitude` float NOT NULL default '0',
  `longitude` float NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `xmppservers`
--

INSERT INTO `xmppservers` VALUES(1, 'mortlach.ord01.nbpl-networks.net', 41.5086, -107.051);
INSERT INTO `xmppservers` VALUES(2, 'speyburn.ord01.nbpl-networks.net', 38.1346, -84.5508);
