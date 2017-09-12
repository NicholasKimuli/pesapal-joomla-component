CREATE TABLE IF NOT EXISTS `#__donations` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`fname` varchar(250) NOT NULL,
`lname` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL,
`mobile` varchar(255) NOT NULL,
`amount` INT  NULL,
`description` TEXT  NULL,
`reference` TEXT  NULL,
`tracking_id` TEXT  NULL,
`currency` varchar(25) NOT NULL,
`status` varchar(25) NOT NULL,
`donation_period` VARCHAR(50) NOT NULL DEFAULT 'ONEOFF',
date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;