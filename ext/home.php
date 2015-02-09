<?php

class extHome {
	
	public function __construct() {
		$lnk = array(
				'es' => array('uri'=>'directorio',
						'str'=>'Directorio'
				),
				'en' => array('uri'=>'directory',
						'str'=>'Directory'
				),
		);
	}
	
	public function &exthdx() {
		global $zihweb, $cls_cfg, $cls_cnt;
		
	}
	
	public function &extcnt() {
		global $zihweb, $cls_cfg, $cls_cnt;
		
		$lnk = $cls_cfg->uril;
		$ret = <<<EOD
		<h1>Add New</h1>
		<a href="{ZW_URL}">BUSINESS</a>
				<a href="{ZW_URL}">CLASSIFIED AD</a> 
EOD;
		return $ret;
	}
}