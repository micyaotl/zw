<?php
/**
 * 
 * Config class
 * @author Marco
 *
 */
class Config {
	var $db;
	var $gcfg;
	var $admin;
	
	var $ruri;
	var $fldr;
	var $phpinfo;
	
	// URI Listener para directorios predeterminados
	var $uril = array('cont', 'fbc', 'mobi');
	
	var $smtime; // microtime inicio
	var $ttime;  // tiempo total
	var $ftime;  // tiempo final
	var $tq = 0;
	var $lq;
	var $ttq;
	var $idis;
	
	var $memuse;
	var $memmax;
	var $cont;
	
	var $tmp;
	var $agent = "Opera/9.80 (Windows NT 6.1; U; en-US) Presto/2.10.229 Version/11.60";

	public function __construct($db = 'mysql') {
		global $_COOKIE, $_POST, $_GET, $_SESSION, $cls_usr, $cls_cnt;
		$this->smtime = $this->stime();
		if (defined('MYSQLi')) {
			$db = (ZWBDD_PERSIT) ? 'mysqli_pconnect' : 'mysqli_connect';
			if(!function_exists($db)) die('MySQL no esta habilitado en tu configuraci&oacute;n PHP!');
			$this->db = new mysqli(ZWBDD_HST , ZWBDD_USR, ZWBDD_PSWD, ZWBDD_BDD) or die('No se pudo conectar a la base de datos en "'.ZWBDD_HST.'" con el usuario "'.ZWBDD_USR.'"<br />
				('.mysqli_errno().') '.mysqli_error().'<br />
				Por favor revisa la configuraci&oacute;n principal!<br />');
			//mysqli_select_db(ZWBDD_BDD, $db) or die ('Por favor crea la base de datos "'.ZWBDD_BDD.'" y asegurate que el usuario tenga los privilegios necesarios!');
			//$this->db = $db;
		} else {
			$db = (ZWBDD_PERSIT) ? 'mysql_pconnect' : 'mysql_connect';
			if(!function_exists($db)) die('MySQL no esta habilitado en tu configuraci&oacute;n PHP!');
			$this->db = $db(ZWBDD_HST , ZWBDD_USR, ZWBDD_PSWD) or die( 'No se pudo conectar a la base de datos en "'.ZWBDD_HST.'" con el usuario "'.ZWBDD_USR.'"<br />
				('.mysql_errno().') '.mysql_error().'<br />
				Por favor revisa la configuraci&oacute;n principal!<br />');
			mysql_select_db(ZWBDD_BDD) or die ('Por favor crea la base de datos "'.ZWBDD_BDD.'" y asegurate que el usuario tenga los privilegios necesarios!');
		}
		$this->query("SET NAMES 'utf8'");
		mb_internal_encoding("UTF-8");
		mb_regex_encoding("UTF-8");
		date_default_timezone_set('America/Mexico_City');
		extract($_GET);
		extract($_POST, EXTR_OVERWRITE);
		if(isset($_COOKIE['u'])) { $this->admin = $this->esAdmin(); }
		if (function_exists('memory_get_usage')) $this->memuse .= 'MEM/MB: '.number_format((memory_get_usage()/1024/1024),3);
		if (function_exists('memory_get_peak_usage')) $this->memmax .= 'MAX MEM/MB: '.number_format((memory_get_peak_usage()/1024/1024),3);

		// SSD ... cpanel
		if (get_magic_quotes_gpc()) {
			
			//$ssd = stripslashes_deep;
			$ssd = $this->db->real_escape_string;
		    if (isset($_POST)) $_POST = array_map($ssd, $_POST);
		    if (isset($_GET)) $_GET = array_map($ssd, $_GET);
		    if (isset($_COOKIE)) $_COOKIE = array_map($ssd, $_COOKIE);
		    if (isset($_REQUEST)) $_REQUEST = array_map($ssd, $_REQUEST);
		    
			function stripslashes_deep($value) {
		        $value = is_array($value) ?
		                    array_map('stripslashes_deep', $value) :
		                    stripslashes($value);
		                    return $value;
		    }
		    $_POST = array_map('stripslashes_deep', $_POST);
		    $_GET = array_map('stripslashes_deep', $_GET);
		    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
		    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
		}
		// SSD

		// URI init
		if($GLOBALS['uri'] == '/') {
			$this->req_uri = substr($_SERVER['REQUEST_URI'], 1);
		} else {
			$this->req_uri = preg_replace('^'.$GLOBALS['uri'].'^', '', $_SERVER['REQUEST_URI']);
		}
		$this->fldr = explode('/', $this->req_uri);
		$this->ruri = explode('?', $this->req_uri);
		$this->ruri = $this->ruri[0];
		
		// idi global
		if (isset($GLOBALS['_COOKIE']['i'])) {
			$this->idis = $_COOKIE['i'];
		}
		//idi global
		
		if (strlen($this->fldr[0])== 2) {
			$idi = $this->fldr[0];
			$this->ruri = str_replace($idi.'/', '', $this->ruri);
			$this->idis = $idi;
			//  set i cookie
			setcookie('i', $idi, time()+46800*30*2, '/', ZWDOM);
			//
		} elseif (isset($GLOBALS['_COOKIE']['i'])) {
			$this->idis = $_COOKIE['i'];
			$_SESSION['i'] = $this->idis;
		} else  {
			$this->idis = $GLOBALS['cfg']['idi'];
		}
		
		if (in_array($this->fldr[0], $this->uril)) {
			$this->ruri = str_replace($this->fldr[0].'/', '', $this->ruri);
			$this->ruri = explode('?', $this->ruri);
			$this->ruri = $this->ruri[0];
		}
		if($this->ruri == '') {
			$this->ruri = 'index.html';
		}
		if ($this->ruri == 'phpinfo') {
			ob_start();
			phpinfo();
			$this->phpinfo = ob_get_contents();
			ob_end_clean();
		}
		$this->gcfg = $this->cfg('00');
		
		// esAdmin
		/*if(isset($_COOKIE['u'])) {
			$u = explode('.', $_COOKIE['u']);
			
		}*/
		if(isset($_COOKIE['u'])) {
			$u = explode('.', $_COOKIE['u']);
			$sql = $this->query("SELECT pass, ktk FROM ".TBLPRE."cnt_usr WHERE uid = ".$u[0]." AND comm LIKE 'root' OR comm LIKE 'admin' LIMIT 1" );
			list($p,$k) = $this->fetch($sql, 'row');

			if ( md5($p.$k) == $u[1] ) {
				define('ADMIN', true);
			}
		} else {
			define('ADMIN', false);
		}
	}
	
	/**
	 * Start time function
	 * Set the init start time in microseconds
	 */
	public function stime() {
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->smtime = $mtime;
		define('ST', $mtime);
		return $mtime;
	}
	public function ftime() {
		$ftime = microtime();
		$ftime = explode(' ', $ftime);
		$ftime = $ftime[1] + $ftime[0];
		$ttime = ($ftime - ST); //$this->smtime);
		$this->ttime = $ttime;
		return $ftime;
	}

	public function sendHeaders($ext = 'html', $h='404') {
		global $cfg, $pag_vista, $lastmod, $cls_cnt;
		$req_uri = $this->ruri;
		$pag_id = $GLOBALS['pag_id'];
		$gmdate = gmdate("D, d M Y H:i:s");
		if ($ext == 'html') {
			$mime = 'text/html';
		} elseif ($ext == 'xml') {
			$mime = 'application/xml';
		}  elseif ($ext == 'atom+xml') {
			$mime = 'application/atom+xml';
		} elseif ($ext == 'js') {
			$mime = 'application/javascript';
		} elseif ($ext == 'css') {
			$mime = 'text/css';
		} elseif ($ext == 'txt') {
			$mime = 'text/plain';
		} elseif ($ext == 'cache') {
			$mime = 'text/cache-manifest';
		} elseif (in_array($ext, array('jpg','gif','png'))) {
			$mime = 'image/'.$ext;
		}
		
		header("Content-Type:".$mime."; charset=utf-8");
		if ($h == '304') {
			header("HTTP/1.0 304 Not Modified");
			header("Pragma: public");
			header("Last-Modified: ".$lastmod);
		} elseif (esAdmin() || strstr($req_uri,'dyn.zw') || strstr($req_uri,ZW_ADM)) {
			header("HTTP/1.0 200 Ok");
			header("Last-Modified: ".$gmdate);
			header("Expires: ".$gmdate);
		} elseif ( $h == '200' || isset($cls_cnt->pag_id)) {
			header("HTTP/1.0 200 Ok");
			header("Pragma: public");
			header("Cache-Control: max-age=86400, public");
			header("Last-Modified: ".$lastmod." GMT"); //gmdate("D, d M Y H:i:s")
			header("Expires: ".$gmdate);
		} elseif (file_exists($cfg['dir'].'error')) {
			header("HTTP/1.0 500 Server Error");
		} else {
			header("HTTP/1.0 404 Not Found");
		}
	}
	
	public function stripslashes_deep($value) {
		//$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
		return $value;
	}

	/**
	 *  Send and register MySQL query
	 * @param string $sql
	 * @return boolean|string
	 */
	public function query($sql = "") {
		$this->tq++;
		$this->lq .= '<li>'.$sql.'</li>';
		if (defined('MYSQLi')) {
			//$sql = $this->db->real_escape_string($sql);
			return mysqli_query($this->db, $sql) or $ret = mysqli_errno().'  '.mysqli_error();
		} else {
			//$sql = mysql_real_escape_string($sql);
			$ret = mysql_query($sql,$this->db) or $ret = mysql_errno().'  '.mysql_error();
			if (!$ret) {
				$ret = 'Could not run query: ' . mysql_error();
				//exit;
			}
			return $ret;
		}
	}
	/**
	 * MySQL fetch array | row | assoc
	 * @param var text $sql
	 * @param var kind $tip
	 * @return fetch row result
	 */
	public function fetch ($sql, $tip='row') {
		if (defined('MYSQLi')) {
			$ret = 'mysqli_fetch_'.$tip;
		} else {
			$ret = 'mysql_fetch_'.$tip;
		}
		return $ret($sql);// or die(mysql_errno().'  '.mysql_error());;
	}
	/**
	 * MySQL fetch row
	 * @param var text $sql
	 */
	public function fetchr($sql) {
		if (defined('MYSQLi')) {
			return $this->fetch($sql, 'row');
		} else {
			return mysql_fetch_row($sql);
		}
	}
	
	public function numrows($sql) {
		if (defined('MYSQLi')) {
			return mysqli_num_rows($sql);
		} else {
			return mysql_num_rows($sql);
		}
	}
	/**
	 * Select $fields, $table, $where
	 * @param var fields in row $cam
	 * @param var table name $tbl
	 * @param var where params $whr
	 */
	public function select($cam, $tbl, $whr) {
		$cam = str_replace(', ', '`', $cam);
		$q = "SELECT `".$cam."` FROM ".$tbl." WHERE ".$whr;
		$q = $this->query($q) or $q = mysql_errno().'  '.mysql_error();
		return $q;
	}
	
	public function realescape($sql) {
		if (defined('MYSQLi')) {
			return mysqli_real_escape_string($sql, $this->db);
		} else {
			return mysql_real_escape_string($sql, $this->db);
		}
	}
	
	public function &cfg($idi = '00') {
		//$q = $this->select('nom, val', TBLPRE.'cnt_cfg', 'idi LIKE ="'.$idi.'"');
		//$q = $this->fetch($q, 'row');
		//while (list($nom, $val) = $this->fetch($query, 'row')) {
		$q = $this->query("SELECT nom, val FROM ".TBLPRE."cnt_cfg WHERE idi LIKE '".$idi."'");
		$ret = '';
		//while (list($nom, $val) = mysql_fetch_row($q)) {
		while (list($nom, $val) = $this->fetch($q, 'row')) {
			$ret[$nom] = $val;
		}
		return $ret;
	}
	/*
	 * Verifica que el usuario sea Administrador
	 */
	public function esAdmin() {
		global $_COOKIE;
		if(isset($_COOKIE['u'])) {
			$u = explode('.', $_COOKIE['u']);
			$sql = $this->query("SELECT pass, ktk FROM ".TBLPRE."cnt_usr WHERE uid = ".$u[0]." AND comm LIKE 'root' OR comm LIKE 'admin' LIMIT 1" );
			list($p,$k) = $this->fetch($sql, 'row');
			if ( md5($p.$k) == $u[1] ) {
				return true;
			}
		} else {
			return false;
		}
		//return ADMIN;
	}
	
	/*
	 * Verifica que el usuario sea cliente
	 */
	function esClient() {
		if( isset($_COOKIE['u']) ) {
			$u = explode('.', $_COOKIE['u']);
			$sql = $this->query("SELECT ktk FROM ".TBLPRE."cnt_usr WHERE uid = ".$u[0]." AND comm LIKE 'client' LIMIT 1" );
			list($k) = $this->fetch($sql);
			if ( isset($_COOKIE['ul']) && $_COOKIE['ul'] == 'cli.'.md5($k)) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function idiLst() {
		//global $gcfg, $cfg;
		$idiomas = $GLOBALS['cfg']['idi'];
		if(isset($this->gcfg['idiomas'])) { $idiomas = $GLOBALS['gcfg']['idiomas']; }
		if(strstr($idiomas, ',')) { 
			return explode(',', $idiomas);
		} else {
			return array($idiomas);
		}
	}
	
	public function admBtn($ga=true) {
		global $gcfg, $cfg, $salusr, $fblilo, $cls_cnt;
		$pid = $cls_cnt->cont['id'];
		$req_uri = $this->req_uri;
		$ruri = $this->ruri;
		if ($ga != true) $salusr = '';
		/*
		 * <script>
	//<![CDATA[
	'.$cls_cnt->jsfull.N.'
	//]]>
	</script>
	*/
		$ret = '';
		if ($cls_cnt->footerjs != '') {
			$ret .= $cls_cnt->footerjs;
		}
		$ret .= '
		<div id="lab">';
		if ( esClient() && $this->admin = true ) {
		$ret .= '
			<div id="btn-admin">
			'.$salusr.'
			</div>';
		}
		if ( $this->esAdmin() ) {
			$ret .= '<div id="btn-admin">'.$salusr.' ';
			if ($pid != '') {
				$ret .= '<a href="'.ZW_URL.ZW_ADM.'?edita='.$pid.'">e</a> ';
			}
			$jsftr = '';
			/*$jsftr = '$(\'a\').click(function(e){
				var lref = $(this).attr("href");
				var cX = e.pageX;
				var cY = e.pageY;
				var nvgtr = navigator.userAgent;
				var brws = nvgtr+"-"+cX+"-"+cY;
					//$.post("log.php", { req: lref, time: "2pm" }, function(data){alert(brws+lref+data);});
				return true;
			});'*/
			if (isset($gcfg['verdebug']) && $gcfg['verdebug'] == 1) {
				$jsftr = '
				jQ("#adm").append("<span class=\'vdbg\'> <a class=\'vgcfg\' href=\'\' title=\'Ver debug\'>+</a><a class=\'vgcfg\' href=\'\' title=\'Ocultar debug\' style=\'display:none\'>-</a></span>");
				jQ(".vdbg a").click(function(event) {
					var cssobj = { "color":"#FF000F","font-size":"0.8em","background-color":"#ffe3b9","border-top":"solid 1px #FF000F","border-bottom":"solid 1px #FF000F","margin":"10px","padding":"10px"}
					jQ("span.vdbg a").toggle();
					jQ("#dbg").css(cssobj);
					jQ("#dbg").toggle();
					event.preventDefault();
				});
				';
			}
			$ret .= '<a href="'.ZW_URL.ZW_ADM.'" id="adm">[a]</a></div>'.N.T;
			if ($jsftr != '') {
				$ret .= T.'<script>
			//<![CDATA[
			'.$jsftr.'
			//]]>
	</script>'.N.T;
			}
		} else {
			$ret .= $fblilo;
			
		}
		$ret .= '</div>';
		$ret.= $this->debuginfo();
		//$stime = $this->stime;
/* elseif (file_exists(ZW_DIRC.'local')) {
			$ret .= '<!--
			'.$ttime.'
			-->';
		}*/
			//self::$ttq = $this->tq;
		return $ret;
	}
	
	private function &debuginfo() {
		global $gcfg, $cfg, $salusr, $fblilo, $cls_cnt;
		$pid = $cls_cnt->cont['id'];
		$ttime = '
<strong>Querys:</strong> '.$this->tq.'<br />
<strong>S. Time: </strong>'.$this->smtime.'<br />
<strong>F. Time: </strong>'.$this->ftime().'<br />
<strong>T. Time: </strong>'.$this->ttime.'';
		
		// debug info
		if($this->esAdmin() && isset($gcfg['verdebug']) && $gcfg['verdebug'] == 1) {
			if (isset($this->fldr[1])) {
				$fldr1 = ' · '.$this->fldr[1];
			} else {
				$fldr1 = '';
			}
			$ret = '
<!-- dbg info -->
<div id="dbg" style="display:none;position:absolute;overflow:auto;width:500px;top:15px;right:5px;border:solid 5px #aaa;z-index:1000;">
<h3>'.ZW_V.'</h3>
<strong>req_uri:</strong>'.$this->req_uri.' <strong>ruri:</strong>'.$this->ruri.'<br />
<strong>fldr:</strong>'.$this->fldr[0].$fldr1.'<br />
'.$ttime.'
<br /><strong>idioma:</strong>'.$GLOBALS['cfg']['idi'].'&nbsp;&nbsp;&nbsp;&nbsp<strong>pag_id:</strong>'.$pid.'';
			$ret .= $this->memuse;
			$ret .= ' - ';
			$ret .= $this->memmax;
		
			ob_start();
			print_r($cls_cnt->jo);
			$pr_cfg = ob_get_contents();
			ob_end_clean();
		
			$ret .= '<hr />'.$pr_cfg.'
<ol>'.$this->lq.'</ol><br>
';
		
			ob_start();
			print_r($GLOBALS['cfg']);
			$pr_cfg = ob_get_contents();
			ob_end_clean();
			ob_start();
			print_r($GLOBALS['gcfg']);
			$pr_gcfg = ob_get_contents();
			ob_end_clean();
			ob_start();
			print_r($_SESSION);
			$pr_sess = ob_get_contents();
			ob_end_clean();
			ob_start();
			print_r($_COOKIE);
			$pr_cook = ob_get_contents();
			ob_end_clean();
		
			$ret .= N.T.'<hr />
<strong>cfg-></strong>'.nl2br($pr_cfg).'
<hr />
<strong>gcfg-></strong>'.nl2br(htmlentities($pr_gcfg)).'
<hr />
<strong>sesion-></strong>'.nl2br($pr_sess).'
<hr />
<strong>cookie-></strong>'.nl2br($pr_cook).'
</div>';
		}
		return $ret;
	}
	
	public function &lastmod() {
		global $cfg;
		if (isset($cfg['lastmod'])){
			$lastmod = $cfg['lastmod'];
		} elseif (isset($this->gcfg['lastmod'])) {
			$lastmod = $this->gcfg['lastmod'];
		} else {
			$lastmod = 'Thu, 10 Mar 2011 00:38:13 GMT';
		}
		return $lastmod;
	}
	
	public function uuid($prefix = ''){
		$chars = md5(uniqid(mt_rand(), true));
		$uuid  = substr($chars,0,8) . '-';
		$uuid .= substr($chars,8,4) . '-';
		$uuid .= substr($chars,12,4) . '-';
		$uuid .= substr($chars,16,4) . '-';
		$uuid .= substr($chars,20,12);
		return $prefix . $uuid;
	}
	
}
$cls_cfg = new Config();

// Global configs
$gcfg = $cls_cfg->gcfg;
$cfg = $GLOBALS['cfg'];

// VISUAL
$visual = $visd = '';
if (isset($gcfg['visual']) && $gcfg['visual'] != ''  && file_exists($cfg['dir'].'visual'.Ds.$gcfg['visual'].Ds.$gcfg['visual'].'.php') && !isset($_GET['visual']) ) {
	$visual = $gcfg['visual'];
	$visd = 'visual/'.$gcfg['visual'].'/';
} elseif (isset($_GET['visual']) && $_GET['visual'] != ''  && file_exists($cfg['dir'].'visual'.Ds.$_GET['visual'].Ds.$_GET['visual'].'.php') ) {
	$visual = $_GET['visual'];
	$visd = 'visual/'.$_GET['visual'].'/';
} else {
	$visual = 'gelos';
	$visd = 'visual'.Ds.'gelos'.Ds;
}

if(!isset($_SESSION)) {
	session_name('s');
	session_cache_expire(3600);
	session_set_cookie_params(3600, $cfg['uri'], ZWDOM);
	session_start();
}
//$_SESSION['uip'] = $_SERVER['REMOTE_ADDR'];
//$_SESSION['unh'] = $_SERVER['HTTP_HOST'];
//$_SESSION['uwb'] = $_SERVER['HTTP_USER_AGENT'];
$_SESSION['f'] = intval(time());

if ($_SESSION['f'] <= time()-3600 && !isset($_SESSION['u'])) {
	session_destroy();
}
function fixSql($sql) {
	$sql = "'".str_replace('\\"', '"', addslashes($sql))."'";
	return $sql;
}

function admLogin() {
	echo '<form action="'.ZW_URL.ZW_ADM.'" method="post" enctype="multipart/form-data">
	<input name="eml" type="text" size="30" maxlength="64" />
	<input name="pas" type="password" size="30" maxlength="64" />
	<input name="login" type="hidden" />
	<input name="sub" type="submit" value="login" />
	</form>';
}