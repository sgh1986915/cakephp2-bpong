﻿DROP VIEW `venues_view`;
CREATE VIEW venues_view AS 
select `venues`.`id` AS `id`,`venues`.`venuetype_id` AS `venuetype_id`,`venues`.`name` AS `name`,`venues`.`description` AS `description`,`venues`.`web_address` AS `web_address`,`venues`.`phone` AS `phone`,`venues`.`isApproved` AS `isApproved`,`venues`.`created` AS `created`,`venues`.`modified` AS `modified`,`venues`.`deleted` AS `deleted`,`venues`.`verified` AS `verified`,`venues`.`slug` AS `slug`,`venues`.`is_deleted` AS `is_deleted`,`venues`.`main_image` AS `main_image`,`venues`.`nbpltype` AS `nbpltype`,`venues`.`nbplday` AS `nbplday`,`addresses`.`address` AS `address`,`addresses`.`address2` AS `address2`,`addresses`.`address3` AS `address3`,`addresses`.`city` AS `city`,`addresses`.`provincestate_id` AS `provincestate_id`,`addresses`.`latitude` AS `latitude`,`addresses`.`longitude` AS `longitude`,`addresses`.`postalcode` AS `postalcode`, `addresses`.`country_id` AS `country_id`,`countries`.`name` AS `country_name`,`countries`.`iso2` AS `country_shortname`,`provincestates`.`name` AS `state_name`,`provincestates`.`shortname` AS `shortname` from (((`venues` left join `addresses` on(((`addresses`.`model_id` = `venues`.`id`) and (`addresses`.`model` = 'Venue')))) left join `provincestates` on((`provincestates`.`id` = `addresses`.`provincestate_id`))) left join `countries` on((`countries`.`id` = `addresses`.`country_id`)))
