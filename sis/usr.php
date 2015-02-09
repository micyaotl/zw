<?php
/**
 * Funciones y gestion de usuario
 * 
 * @package ZihWeb CMS
 * @subpackage Users
 * @version 0.4
 * @author Marco Garcia <micyaotl@gmail.com>
 * @copyright 2010 feelRiviera.com
 */
class User {
	var $lvl = array(
		'user', 'friend', 'family', 'co-work', 'owner',
		'null', 'author', 'editor', 'admin', 'root'
	);
	
	var $facebook;
	var $user;
	var $fbme;
	var $fbLoginUrl;
	var $fblilo;
	var $admin;
	var $aui;
	var $admn;
	
	public function __construct() {
		global $gcfg, $cls_cfg, $_POST;
		
		$this->aui = $this->getAdminId();
		/* master WASA */
		if(isset($_COOKIE['u'])) {
			$u = explode('.', $_COOKIE['u']);
			if(in_array($u[0], $this->aui)) {
				define('WASA', 1);
				$master = WASA;
				$this->admn = 1;
			}
		}
		if (!defined('WASA') && !isset($master)) {
			define('WASA', 0);
			$master = WASA;
		}
		/*fin master WASA */
		
		/**
		 * Logout functions
		 * /
		if (isset($_POST['logout'] )) {
			setcookie( 'u', '', 0, '/');
			setcookie( 'ul', '', 0, '/');
			setcookie( 'i', '', 0, '/');
			session_destroy();
			header('Location: '.ZW_URL);
		}
		/**
		 * Login functions
		 * /
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
					setcookie ('u', $u, $t+46800*30*2, '/', ZWDOM);
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
		
		/**
		 * Facebook API login
		 */
		if( isset($gcfg['fb-apid']) && $gcfg['fb-apid'] != '' && isset($gcfg['fb-secr'])) {
			require_once ZW_DIR.'sis'.Ds.'api'.Ds.'facebook.php';
			$this->facebook = new Facebook(array(
			  'appId'  => $cls_cfg->gcfg['fb-apid'],
			  'secret' => $cls_cfg->gcfg['fb-secr'],
			));
			$this->user = $this->facebook->getUser();

			$me = null;
			// Session based API call.
			if ($this->user) {
			  try {
			    $this->fbme = $this->facebook->api('/me');
			    $this->fbInsertUser($this->fbme);
			  } catch (FacebookApiException $e) {
			    error_log($e);
			  }
			}
			$params = array(
				'scope' => 'user_status,user_about_me,user_birthday,publish_stream,user_website,email',
				'redirect_uri' => ZW_URL.'',
			);
			$this->fbLoginUrl = $this->facebook->getLoginUrl($params);
			
			if ($this->user) {
			  $this->fblilo = '<a href="' .  htmlspecialchars($this->facebook->getLogoutUrl()) . '">Logout</a>';
			} else {
			  $this->fblilo = '<a href="' . htmlspecialchars($this->fbLoginUrl) . '">Login</a>';
			}
		}
	}
	
	private function &getAdminId() {
		global $cfg, $cls_cfg;
		$aui = array(1);
		if(isset($cfg['aui'])) {
			if (is_array($cfg['aui'])) {
				$aui = $cfg['aui'];
			} else {
				$aui = explode(',', $cfg['aui']);
			}
		}
		if(isset($cls_cfg->gcfg['aui'])) {
			$aui = explode(',', $cls_cfg->gcfg['aui']);
		}
		return $aui;
	}
	
	private function &fbInsertUser($usrdata) {
		global $cls_cfg;
	}
	
	public function login($usr, $pass) {
		global $cls_cfg;
		if(isset($_COOKIE['u'])) {
			$u = explode('.', $_COOKIE['u']);
			define('_UID', $u[0]);
			$sql = $cls_cfg->query("SELECT nombre, pass, ktk, comm FROM ".TBLPRE."cnt_usr WHERE uid = "._UID." LIMIT 1" );
			list($nom, $p,$k,$lvl) = $cls_cfg->fetch($sql, 'row');
			define('_UNM',  $nom);
			$_SESSION['username'] = _UNM;
			$usrdat = array(
				'nom' => $nom,
				'lvl' => $lvl,
			);
		} else {
			define('_UID', 0);
		}
	}
}
$cls_usr = new User();

$fblilo = $cls_usr->fblilo;

/* master WASA * /
$aui = array(1);
if(isset($cfg['aui'])) {
	if (is_array($cfg['aui'])) {
		$aui = $cfg['aui'];
	} else {
		$aui = explode(',', $cfg['aui']);
	}
}
if(isset($cls_cfg->gcfg['aui'])) {
	$aui = explode(',', $cls_cfg->gcfg['aui']);
}
if(isset($_COOKIE['u'])) {
	$u = explode('.', $_COOKIE['u']);
	if(in_array($u[0], $aui)) {
		define('WASA', 1);
		$master = WASA;
	}
}
if (!defined('WASA') && !isset($master)) {
	define('WASA', 0);
	$master = WASA;
}
/*fin master WASA */

if(isset($_COOKIE['u'])) {
	$u = explode('.', $_COOKIE['u']);
	define('_UID', $u[0]);
	$sql = $cls_cfg->query("SELECT nombre, pass, ktk, comm FROM ".TBLPRE."cnt_usr WHERE uid = "._UID." LIMIT 1" );
	list($nom, $p,$k,$lvl) = $cls_cfg->fetch($sql, 'row');
	define('_UNM',  $nom);
	$_SESSION['username'] = _UNM;
	$usrdat = array(
		'nom' => $nom,
		'lvl' => $lvl,
	);
} else {
	define('_UID', 0);
}
if(!defined('_UNM')) { define('_UNM', ''); }
// <script src="'.$cfg['lib'].'chat.js"></script>
/*<ul>
				<li><a href="javascript:void(0)" onclick="javascript:chatWith(\'seb\')">Seb</a></li>
				<li><a href="javascript:void(0)" onclick="javascript:chatWith(\'marco\')">Marco</a></li>
			</ul>*/
$salusr = '
		<div id="umenu" style="float:left;display:inline;"><strong>Hola '._UNM.'!</strong>&nbsp;'.$fblilo.'&nbsp;
			
		</div>';