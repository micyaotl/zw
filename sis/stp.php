<?php
require_once ZW_DIR.'sis'.Ds.'cnt.php';

function instalarZihWeb() {
	echo 'bienvenido al install';
}

// Banners
$cls_cfg->query ( "CREATE TABLE IF NOT EXISTS `".TBLPRE."cnt_ads` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `cli` varchar(300) NOT NULL,
  `w` int(11) NOT NULL,
  `h` int(11) NOT NULL,
  `txt` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `v` int(11) NOT NULL,
  `fi` int(11) NOT NULL,
  PRIMARY KEY (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ");


$sql = "CREATE TABLE IF NOT EXISTS `_fb_users` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `fbuid` int(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `f_name` varchar(50) NOT NULL,
  `l_name` varchar(50) NOT NULL,
  `link` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `birthday` varchar(10) NOT NULL,
  `hometown` int(20) NOT NULL,
  `location` int(20) NOT NULL,
  `bio` text NOT NULL,
  `gender` varchar(10) NOT NULL,
  `website` varchar(250) NOT NULL,
  `timezone` varchar(5) NOT NULL,
  `locale` varchar(8) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fbuid` (`fbuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$sql1 .= "CREATE TABLE IF NOT EXISTS `_fb_town` (
  `fbid` int(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  UNIQUE KEY `fbid` (`fbid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$cls_cfg->query ($sql);
$cls_cfg->query ($sql1);

