<?php
/**
 * Clase para creación y ajustes de imagenes
 * 
 * @package ZihWeb
 * @subpackage img
 * @version 0.9
 * @author Marco Garcia <micyaotl@gmail.com>
 * @copyright 2011 FeelRiviera.com
 */
 
class Images {
	/*var $w;
	var $h;
	var $r;*/
	public function __construct() {
		ini_set('memory_limit', '128M');
		//$this->r = $_GET['r'];
	}
	
	public function gdBanners($banners) {
		global $cfg, $gcfg,$cls_cfg;
		$banners = str_replace('banners,', '', $banners);
		$src = explode (",",$banners);
		$tmp = $cfg['dir'].'tmp'.Ds.$banners.'.jpg'; 
		$imgLenght = 55 * count($src);
		$iOut = imagecreatetruecolor ("200", $imgLenght) ;
		$bg_color = imagecolorallocate ($iOut, 249, 247, 242);
		imagefilledrectangle($iOut, 0, 0, "200",$imgLenght, $bg_color);
		if ($gcfg['tmp'] == 1 && file_exists($tmp)) {
			//header('Content-Type: image/jpg');
			header("Cache-Control: public");
			$cls_cfg->sendHeaders('jpg','304');
			readfile($tmp);
			exit();
		}
		$img_pos = 0;
		foreach ($src as $link){
			$iTmp = imagecreatefromjpeg(ZW_DIRC.'img'.Ds.'ads'.Ds.$link.'.jpg');
			imagecopy ($iOut,$iTmp,0,$img_pos,0,0,imagesx($iTmp),imagesy($iTmp));
			imagedestroy ($iTmp);
			$img_pos = $img_pos + 55;
		} 
		imagejpeg($iOut,$tmp,100);
		$cls_cfg->sendHeaders('jpg','200');
		readfile($tmp);
		imagedestroy($iOut);
		exit();
	}
	
	public function flkrGal($usr, $apik, $can) {
		require_once(ZW_DIR.'sis'.Ds.'api'.Ds.'flkr'.Ds.'phpFlickr.php');
		$phpFlickrObj = new phpFlickr($apik);
		 
		$user = $phpFlickrObj->people_findByUsername($usr);
		$user_url = $phpFlickrObj->urls_getUserPhotos($user['id']);
		$photos = $phpFlickrObj->people_getPublicPhotos($user['id'], NULL, NULL, $can);
		 
		foreach ($photos['photos']['photo'] as $photo)
		{
		  echo '<a href="'.$user_url.$photo['id'].'" title="'.$photo['title'].' (on Flickr)" target="_blank">';
		  echo '<img alt="'.$photo['title'].'" src="'.$phpFlickrObj->buildPhotoURL($photo, "square").'" />';
		  echo '</a>';
		}
	}
	
	public function &genImg($diri = 'img', $t = '100x100', $img = 'logo', $ext = 'png') {
		global $cfg, $gcfg,$cls_cfg;
		// ini rotacion
		$r = '';
		if (strstr($ext, '-')) {
			$exr = str_replace('-', '', $ext);
			$r = '-'.$exr;
			$ext = str_replace('-'.$exr, '', $ext);
		}
		if (strstr($ext, '+')) {
			$exr = str_replace('+', '', $ext);
			$r = '+'.$exr;
			$ext = str_replace('+'.$exr, '', $ext);
		}
		
		// directorio y archivo origen
		$im = str_replace('_', ''.Ds.'', $img);
		$src = $cfg['dir'].$diri.''.Ds.''.$im.'.'.$ext;
		$tmp = $cfg['dir'].'tmp'.Ds.$t.'.'.$img.'.'.$ext;
		if (!file_exists($src)) {
			$src = $cfg['dir'].$diri.''.Ds.'logo.jpg';
			$ext = 'jpg';
		}
		//$ext = strtolower($ext);
		list($w, $h) = getimagesize($src);
		$sz = $t;
		if ($t =='t') {
			$t = '100x100';
		} elseif ($t =='o') {
			$t = $w.'x'.$h;
		} elseif ($t =='n') {
			$t = '800x600';
		} else {
			$t = $t;
		}
		$t = explode('x', $t);
		if ($gcfg['tmp'] == 1 && file_exists($tmp)) {
			$cls_cfg->sendHeaders(strtolower($ext),'200');
			readfile($tmp);
			exit();
		} else {
			$cls_cfg->sendHeaders(strtolower($ext),'200');
		}
		$itmp = $src;
		if ($sz == 'n'){
			$t[0] = $w;
			$t[1] = $h;
			$maxx = 800;
			$maxy = 800;
		}
		if ($sz == 'o'){
			$t[0] = $w;
			$t[1] = $h;
			$maxx = 1600;
			$maxy = 1600;
		}
		if (isset($maxx) && isset($maxy)) {
			if ( $t[0] >= 200 || $t[1] >= 200) {
				$ratio = $w * 1.0 / $h;
				$x_oversized = ($w > $maxx);
				$y_oversized = ($h > $maxy);
				if ($x_oversized) {
					$t[0] = min($maxx, $ratio * $maxy);
					$t[1] = min($maxy, $maxx / $ratio);
				}
			}
		}
		if ($ext == 'png') {
			$itmp = imagecreatefrompng($src);
			$imgt = imagecreatetruecolor($t[0],$t[1]);
			imagealphablending($imgt, true);
			$g=imagecolorallocatealpha($imgt,255,255,255,127);
			imagefilledrectangle($imgt,0,0,$t[0],$t[1],$g);
			imagealphablending($imgt,true);
		} elseif ($ext == 'gif') {
			$itmp = imagecreatefromgif($src);
			$imgt = imagecreate($t[0],$t[1]);
		} else {
			if ($r !== '') {
				$itmp = imagecreatefromjpeg($src);
				$itmp = imagerotate($itmp, $r, 0);
				$imgt = imagecreatetruecolor($t[1],$t[0]);
			} else {
				$itmp = imagecreatefromjpeg($src);
				$imgt = imagecreatetruecolor($t[0],$t[1]);
			}
		}
		if($w > $h) {
			$r = (double)($h / $t[1]);
			$cw = round($t[0] * $r);
			if ($cw > $w) {
				$r = (double)($w / $t[0]);
				$cw = $w;
				$ch = round($t[1] * $r);
				$xo = 0;
				$yo = round(($h - $ch) / 2);
			} else {
				$ch = $h;
				$xo = round(($w - $cw) / 2);
				$yo = 0;
			}
		} else {
			$r = (double)($w / $t[0]);
			$ch = round($t[1] * $r);
			if ($ch > $h) {
				$ratio = (double)($h / $t[1]);
				$ch = $h;
				$cw = round($t[0] * $r);
				$xo = round(($w - $cw) / 2);
				$yo = 0;
			} else {
				$cw = $w;
				$xo = 0;
				$yo = round(($h - $ch) / 2);
			}
		}
		if ($ext == 'png') {
			imagealphablending($imgt,false);
			imagesavealpha($imgt,true);
			imagecopyresized($imgt, $itmp,0,0,$xo,$yo,$t[0],$t[1], $cw, $ch);
			imagepng($imgt, $tmp);
		} elseif ($ext == 'gif') {
			imagecopyresized($imgt, $itmp,0,0,$xo,$yo,$t[0],$t[1], $cw, $ch);
			imagegif($imgt);
		} else {
			if (!isset($r) || $r != '') {
				imagecopyresampled($imgt, $itmp,0,0,$xo,$yo,$t[0],$t[1], $cw, $ch);
			} else {
				imagecopyresampled($imgt, $itmp,0,0,$xo,$yo,$t[0],$t[1], $cw, $ch);
			}
			imagejpeg($imgt, $tmp, 80);
		}
		imagedestroy($itmp);
		imagedestroy($imgt);
		readfile($tmp);
	}
	
	public function &wk2img($url) {
		global $zihweb, $cls_cfg, $cfg;
			$wk2img = ZW_DIR.'sis'.Ds.'api'.Ds.'wkhtmltoimage-'.$cpu;
			//$fil = md5($url);
			//$html = $zihweb->curly($url);
			$urli = $zihweb->fixUri($url);
			$tmp = $cfg['dir'].'tmp'.Ds.$urli.'.jpg';
			//
			$cl = $wk2img.' --width 1280 --height 700 --format jpg http://'.$url.' '. $tmp;
			//
			//*
			ob_start();
			passthru($cl);
			file_put_contents($tmp, ob_get_clean());
			//*/
			//exec($cl);
			//
			if (!file_exists($tmp)) {
				$cls_cfg->sendHeaders('jpg','200');
				readfile($tmp);
			}
			exit;
	}
}
//$cls_img = new Images();