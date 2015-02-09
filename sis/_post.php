<?php
/**
 * $_POST listener
 * @version 0.1
 */

// INI MAILERS
/*if (isset($_POST['sendAndminMail'])) {
	require_once ZW_DIR.'sis'.Ds.'eml.php'; // eml
	sendAdminMail();
}
// Reservations
elseif (isset($_POST['myroom'])) {
	require_once ZW_DIR.'sis'.Ds.'eml.php'; // eml
	reqReserve();
}
elseif (isset($_POST['comm']) && isset($_POST['subj'])) {
	require_once ZW_DIR.'sis'.Ds.'eml.php'; // eml
	usrMail();
}*/
// FIN MAILERS



/**
 * INI LOGIN & LOGOUT
 */
if( isset($_POST['login']) && isset($_POST['eml']) && isset($_POST['pas']) ) {
	if (isset($_POST['lvl'])) { $lvl = $_POST['lvl']; } else { $lvl = ''; }
	#login($_POST['eml'], $_POST['pas'], $lvl);
	if (strstr($_POST['eml'], '@')){
		$whr = "email = '".$_POST['eml']."'";
	} else {
		$whr = "nombre = '".$_POST['eml']."'";
	}
	$sql = $cls_cfg->query("SELECT uid, nombre, email, pass, ktk, comm FROM ".TBLPRE."cnt_usr WHERE $whr LIMIT 1" );
	list ($uid, $nombre, $email, $pass, $ktk, $comm) = $cls_cfg->fetch($sql);
	if ($pass == md5($_POST['pas'])) {
		$pas = md5($pass.$ktk);
		$u = $uid.'.'.$pas;
		$t = time();
		$_SESSION['f'] = $t;
		$_SESSION['u'] = $u;
		setcookie ('u', $u, $t+46800*30*2, $uri, ZWDOM);
		if($lvl != '') {
			$_SESSION['ul'] = $lvl;
			setcookie( 'ul', $lvl.'.'.md5($ktk), $t+46800*30*2, '/', ZWDOM);
		}
		if(isset($_SERVER['HTTP_REFERER'])) {
			header('Location: '.$_SERVER['HTTP_REFERER'].'#logedin');
		} else {
			header('Location: '.ZW_URL.ZW_ADM);
		}
	} else {
		header('Location: '.$_SERVER['HTTP_REFERER'].'#retry');
	}
}

if (isset($_POST['logout'] )) {
	setcookie( 'u', '', 0, '/');
	setcookie( 'ul', '', 0, '/');
	setcookie( 'i', '', 0, '/');
	//session_destroy();
	header('Location: '.ZW_URL);
}
//  FIN LOGIN & LOGOUT
//*/