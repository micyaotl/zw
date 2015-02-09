<?php
// Admin
if(strstr($cls_cfg->ruri, ZW_ADM) || esAdmin()) { require ZW_DIR . 'sis'.Ds.'adm.php'; }

// Language
if (strlen($cls_cfg->fldr[0])== 2 && $cls_cfg->fldr[0] !== 'aw' && $cls_cfg->fldr[0] !== 'zw') {
			$idi = $cls_cfg->fldr[0];
			setcookie('i', $idi, time()+46800*30*2, '/', ZWDOM);
			//$cls_cfg->ruri = str_replace($idi.'/', '', $cls_cfg->ruri);
			$cls_cfg->idis = $idi;
			$_SESSION['i'] = $cls_cfg->idis;
		} else {
			$cls_cfg->idis = $cfg['idi'];
		}
// Sendmails
if (strstr($cls_cfg->req_uri,"sm.zw") || strstr($cls_cfg->req_uri,"sm.php")) {
	require_once(ZW_DIR.'sis'.Ds.'eml.php');
	if (isset($_POST['sendAndminMail'])) {
		sendAdminMail();
	}
	// Reservations
	elseif (isset($_POST['mydate']) && isset($_POST['mydateout'])) {
		$cls_eml->reqReserve();
	}
	// User contact
	elseif (isset($_POST['comm']) && isset($_POST['subj'])) {
		$cls_eml->usrMail();
	}
	exit;
}

/**
 * JavaScript file comp
 */
if (strstr($req_uri,"lib/javascript.zw") || strstr($req_uri,"lib/js.zw")) {
	$cls_dyn->javascript();
}
/**
 * CSS file comp
 */
if (strstr($req_uri,"lib/styles.zw") || strstr($req_uri,"lib/css.zw")) {
	$cls_dyn->styles();
}

if (strstr($req_uri,"chat.php") || strstr($req_uri,"chat.zw")) {
	require_once(ZW_DIR.'sis'.Ds.'api'.Ds.'chat.php');
	exit;	
}

if($fldr[0] == 'd') { // ZW_URL == 'http://koox.mx/' && 

	function redir($lid) {
		global $zihweb, $cls_cfg;
		$lid = $zihweb->base_decode($lid, $zihweb->chars);
		$sql = $cls_cfg->query('SELECT title FROM dir_neg WHERE lid = '.$lid.' LIMIT 1');
		list($title) = @$cls_cfg->fetch($sql, 'row');
		//$title = $cls_cfg->fetch($sql, 'assoc');
		//$title = $title['title'];
		$title = $zihweb->fixUri($title);
		$dir = '';
		if (ZW_URL == 'http://publik.in/') { $dir = 'business-'; }
		$relink = ZW_URL.$dir.'directory/'.$lid.'~'.$title;
		return $relink;
	}
	
	header('Location:'.redir($fldr[1]));
	exit;
}

/**
 * View web
 */
if ($fldr[0] == 'web') {
	$web = explode('web/', $_SERVER['REQUEST_URI']);
	$web = explode('~', $web[1]);
	if ($web[0] == 'directory') {
		$sql = 'SELECT url FROM dir_neg WHERE lid = '.$web[2].' LIMIT 1';
		list($url) = $cls_cfg->fetch($cls_cfg->query($sql), 'row');
	}
	header('Location:'.$url);
	//header("Content-Type: text/html");
	/*$cls_cfg->agent = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
	$web = $zihweb->curly($url);
	$msg = $cls_cnt->remTxt('Estas saliendo de [site]');
	sendHeaders('html','200');
	echo '<!DOCTYPE html>

<html lang="en">

<head>
<meta charset="utf-8" />
			<style>
			#wsxtrn {border: 1px solid #000000; width: 100%; display: block; background: #FFFFFF;}
			</style>
			</head>
			<body>
			
			<div id="wsxtrn" style="">
<p>&nbsp;<span style="font-size: large; font-family: &quot;arial&quot;, &quot;helvetica&quot;, sans-serif;"><strong>'.$msg.'</strong></span></p>
</div>
			';

	echo $web;
	echo '</body>
</html>';*/
	exit;
}

if (strstr($req_uri,"log.zw")) {
	require_once(ZW_DIR.'sis'.Ds.'log.php');
	exit;
}
if ( in_array($fldr[0], $cls_cfg->uril) || isset($GLOBALS['_GET']['cnt'])) {
	// Facebook Connect
	if($fldr[0] == 'fbc' && isset($gcfg['fb-apid']) && isset($gcfg['fb-secr'])) {
		require_once ZW_DIR.'sis'.Ds.'fbc.php';
		exit;
	} elseif ($fldr[0] == 'mobi' || $fldr[0] == 'cont') {
		// mobi  cont
		sendHeaders('html', '200');
		$cls_cnt->getHead($fldr[0]);
		echo '</head>
	
	<body>
	<h1><a href="'.'/'.ZW_URL.$fldr[0].'/'.$gcfg['title'].'</a></h1>
	'.$cls_cnt->getMenu().'
		<div class="cnt">
			'.$cls_cnt->getContent('cnt').'
		</div>
	'.admBtn().'
	<body>
</html>';
		exit;
	}
}

if ($ruri == 'upload.swf' || $ruri == 'uploadify.swf') {
	$swf = ZW_DIR.'lib'.Ds.'upload.swf';
	//header('Content-Description: File Transfer');
    header('Content-Type: application/x-shockwave-flash');
    //header('Content-Disposition: attachment; filename='.basename($ruri));
    header('Content-Transfer-Encoding: binary');
    //header('Expires: 0');
    //header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public cache');
    header('Content-Length: '.filesize($swf));
    ob_clean();
    flush();
    readfile($swf);
    exit;
}

if ($ruri == 'upload.php' || $ruri == 'uploadify.php') {
	$targetPath = $_REQUEST['folder'];
	if (isset($_GET['folder'])) {
		$targetPath = $_GET['folder'];
	}
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = ZW_DIRC.$targetPath;
	$targetDir = str_replace(Ds.Ds, Ds, ZW_DIRC.$_REQUEST['folder']);
	//$targetDir = str_replace(ZW_ADM, '', $targetDir);
	//* temp disabled
	$targetFile = str_replace('_', '-', $_FILES['Filedata']['name']);
	$targetFile = str_replace(',', '-', $targetFile);
	$targetFile = str_replace(' ', '-', $targetFile);
	$targetFile = str_replace('\'', '-', $targetFile);
	//$targetFile = str_replace('----', '-', $targetFile);
	//$targetFile = str_replace('---', '-', $targetFile);
	$targetFile = str_replace('--', '-', $targetFile);
	//*/
	//$targetFile = $zihweb->fixUri($_FILES['Filedata']['name']);
	$targetFile = strtolower($targetFile);
	$targetFile = str_replace(Ds.Ds, Ds, $targetPath).Ds. $targetFile;
	
	//$fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
	//$fileTypes  = str_replace(';','|',$fileTypes);
	//$typesArray = explode('|',$fileTypes);
	$typesArray = array('jpg','png','gif','jpeg','JPG','PNG','GIF','mp3', 'MP3');
	$fileParts  = pathinfo($_FILES['Filedata']['name']);
	if (in_array($fileParts['extension'], $typesArray)) {
		if ($_REQUEST['folder'] != '' && !file_exists($targetDir)) mkdir($targetDir, 0755, true);
		move_uploaded_file($tempFile, $targetFile);//str_replace($search, $replace, $subject)
		sendHeaders('html', '200');
		echo $_FILES['Filedata']['name']; //str_replace(ZW_DIRC, '', $targetFile);
	} else {
		sendHeaders('html', '500');
		echo 'Invalid file type.';
	}
	exit;
}

if (strstr($cls_cfg->req_uri,"check.php") && $cls_cfg->admin = true) {
	sendHeaders('html', '200');
	$fileArray = array();
	foreach ($_POST as $key => $value) {
		if ($key != 'folder') {
			if (file_exists(ZW_DIRC . $_POST['folder'] . Ds . $value)) {
				$fileArray[$key] = $value;
			}
		}
	}
	echo json_encode($fileArray);
	exit;
}

// manifest.cache
if ($ruri == "manifest.cache") {
	$zihweb->manifestCache();
exit;
}
// robots.txt
if ($ruri == 'robots.txt') {
	$zihweb->getRobots();
exit;
}

if($fldr[0] == 'feedproxy') {
	$zihweb->feedproxy($req_uri);
}

if($fldr[0] == 'xml') {
	include_once ZW_DIR.'sis'.Ds.'xml.php';
	exit;
}

if ($ruri == 'dyn.zw') {
	$cls_dyn->getDynamics();
	exit;
}
/*
if (in_array($cls_cfg->fldr[0], array('webimg'))) {
	global $cls_cfg;
	$cls_img = new Images();
}*/

/**
 * 		/webimg/ & /stock/ Image generator
 */
if (in_array($cls_cfg->fldr[0], array('web-img', 'webimg','stock'))) {
	global $zihweb, $cls_cfg, $cfg;
	$cls_img = new Images();
	if($cls_cfg->fldr[0] == 'web-img') {
		$im = str_replace('web-img/', '', $cls_cfg->req_uri);
		$imd = 'images';
	}  elseif($cls_cfg->fldr[0] == 'stock') {
		$cls_img = new Images();
		$im = str_replace('stock/', '', $cls_cfg->req_uri);
		$im = explode('.', $im);
		if( strstr($im[0], 'x') || in_array($im[0], array('o','t','n')) ) {
			$cls_img->genImg('stk', $im[0], $im[1], $im[2]);
		} else {
			$cls_img->genImg('img', 'o', 'logo', 'png');
		}
		unset($cls_img);
		exit;
	} else {
		$im = str_replace('webimg/', '', $cls_cfg->req_uri);
		$imd = 'img';
	}
	
	if ($cls_cfg->fldr[1] == '-90' || $cls_cfg->fldr[1] == '+90') {
		
	} elseif ($cls_cfg->fldr[1] == 'ss') {
		$url = str_replace('webimg/ss/', '', $cls_cfg->req_uri);
		$cls_img->wk2img($url);
		
	} elseif ($cls_cfg->fldr[1] == 'qr') {
		include ZW_DIR.'sis'.Ds.'api'.Ds.'phpqrcode'.Ds.'qrlib.php';
		$cls_cfg->sendHeaders('png','200');
		$id = str_replace('webimg/qr/', '', $cls_cfg->req_uri);
		$txt = ZW_URL.'d/'.$zihweb->base_encode($id, $zihweb->chars);
		//$tmp = $cfg['dir'].'tmp'.Ds.'qr'.$im.'.png';
		//ob_start();
		$im = QRcode::png($text=$txt, $outfile = false, $level=1, $size = 3, $margin = 0, $saveandprint=false);
		//$im = ob_get_contents();
		//ob_end_clean();t.logo.png 
		//$tmp = $cfg['dir'].'tmp'.Ds.'t.feelriviera.png';
		$im = imagecreatefrompng($im);
		//imagefilter($im, IMG_FILTER_NEGATE);
		//imagefilter($im, IMG_FILTER_BRIGHTNESS, 2);
		//imagefilter($im, IMG_FILTER_COLORIZE, 0, 255, 0);
		imagepng($im);//, null, 0, PNG_ALL_FILTERS);
		imagedestroy($im);
	} elseif ($cls_cfg->fldr[1] == 'qrd') {
		/**
		 * TEST QR
		 */
		include ZW_DIR.'sis'.Ds.'api'.Ds.'phpqrcode'.Ds.'qrlib.php';
		$cls_cfg->sendHeaders('png','200');
		$id = str_replace('webimg/qrd/', '', $cls_cfg->req_uri);
		$txt = 'http://koox.mx/d/'.$id;
		//$tmp = $cfg['dir'].'tmp'.Ds.'qr'.$im.'.png';
		//ob_start();
		$im = QRcode::png($text=$txt, $outfile = false, $level=1, $size = 3, $margin = 2, $saveandprint=false);
		//$im = ob_get_contents();
		//ob_end_clean();t.logo.png 
		//$tmp = $cfg['dir'].'tmp'.Ds.'t.feelriviera.png';
		$im = imagecreatefrompng($im);
		//imagefilter($im, IMG_FILTER_NEGATE);
		//imagefilter($im, IMG_FILTER_BRIGHTNESS, 2);
		//imagefilter($im, IMG_FILTER_COLORIZE, 0, 255, 0);
		imagepng($im);//, null, 0, PNG_ALL_FILTERS);
		imagedestroy($im);
	} elseif ($cls_cfg->fldr[1] == 'ads') {
		$im = str_replace('webimg/ads/', '', $cls_cfg->req_uri);
		$im = explode('.', $im);
		$im = $im[0];
		header('Content-Type: image/'.$im[1].'');
		header("Cache-Control: public");
		$cls_cfg->sendHeaders($im[1],'200');
		$cls_img->gdBanners($im);
		exit;
	} else {
		$im = explode('.', $im);
		if( strstr($im[0], 'x') || in_array($im[0], array('o','t','n')) ) {
			$cls_img->genImg($imd, $im[0], $im[1], $im[2]);
		} else {
			$cls_img->genImg($imd, 'o', 'logo', 'png');
		}
	}
	unset($cls_img);
	exit;
}
