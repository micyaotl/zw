<?php

class Forms {
	var $method;
	
	public function __construct($action='', $method='post') {
		
	}
	
	public function input($name='user', $type='text', $value='', $title= 'title', $size='30', $maxl="200") {
		$return = '<input type="'.$type.'" name="'.$name.'"';
		if ($type !== 'submit') {
			$return .= ' size="'.$size.'" maxlength="'.$maxl.'"';
		}
		$return .= ' title="'.$title.'" value="'.$value.'" />';
		return $return;
	}
	
	private function setjsjq($val) {
		
	}
}
//$cls_frm = new Forms();