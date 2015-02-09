<?php 
class Ads {
	var $w;
	var $h;
	var $p;
	var $c;
	var $gads_pubid = 'ca-pub-7509299074890695';
	
	public function __construct() {
		
	}

	public function adSenseSlot($as) {
		$this->adc = 0;
		if ($as == '468x60') {
			$slot = '/* Normal 468x60 */
google_ad_slot = "5401826105";
google_ad_width = 468;
google_ad_height = 60;';
		}
		if ($as == '728x90') {
			$slot = '/* 728x90, creado 28/04/09 */
google_ad_slot = "5559086452";
google_ad_width = 728;
google_ad_height = 90;';
		}
		if ($as == '728x15') {
			$slot = '/* Links 728x15, created 9/13/10 */
google_ad_slot = "2755071579";
google_ad_width = 728;
google_ad_height = 15;';
		}
		if ($as == '160x600') {
			$slot = '/* 160x600, created 9/27/10 */
google_ad_slot = "1975420955";
google_ad_width = 160;
google_ad_height = 600;';
		}
		if ($as == '300x250') {
			$slot = '/* bloque */
google_ad_slot = "9685480422";
google_ad_width = 300;
google_ad_height = 250;';
		}
		$this->adc++;
		
		$ret = '<script type="text/javascript"><!--
google_ad_client = "'.$this->gads_pubid.'";
'.$slot.'
//-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>';
		
		return $ret;
	}
}
?>