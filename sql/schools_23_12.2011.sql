ALTER TABLE  `schools` ADD INDEX (  `userscount` );
ALTER TABLE  `schools` ADD INDEX (  `latitude` );
ALTER TABLE  `schools` ADD INDEX (  `longitude` );
ALTER TABLE  `schools` ADD FULLTEXT (
`name`
);
