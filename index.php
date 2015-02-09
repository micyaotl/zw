<?php
/**
 * @package ZihWeb CMS
 * @version 0.2.8
 * @author Marco Garcia <marco@feelriviera.com>
 * @copyright 2013 feelRiviera.com
 */

if(!defined('Ds')) { define('Ds', DIRECTORY_SEPARATOR); }
if(file_exists(dirname(__FILE__).Ds.'cfg.php')) {
	include_once(dirname(__FILE__).Ds.'cfg.php');
} else {
	echo "We need to run the setup";
}

if (strstr($cls_cfg->req_uri, ZW_ADM)) {
	//sendHeaders();
	include(ZW_DIR.'admon.php');
} else {
	//sendHeaders();
	include(ZW_DIR.'indexcnt.php');
}