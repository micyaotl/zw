<?php
/**
 * Funciones principales
 *
 * @package ZihWeb
 * @subpackage fnc
 * @version 1.8.2
 * @author Marco Garcia <micyaotl@gmail.com>
 * @copyright 2014 feelRiviera.com
 */
define('ZW_V', 'ZihWeb CMS rev. 0.5.75-271014');
if(!defined('TBLPRE')) define('TBLPRE', '');
if (!defined('ZW_ADM')) define('ZW_ADM', $cfg['adm']);
if(!defined('Ds')) { define('Ds', DIRECTORY_SEPARATOR); }
define('N', "\n");
define('T', "\t");

class ZihWebCMS {
	var $vesrion = ZW_V;
	var $cpu;
//	var $chars = '0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
	var $chars = 'wvYxLy26E8GPHQjkRUT5V3SWz0Z1Xdef4ghim9ADnF7otpJNqrBCaMbcsKu';
	function __construct() {
		
		//ini_set('error_reporting', 'E_ALL & ~E_NOTICE');
		
		//if(`grep -i amd /proc/cpuinfo`!='') $this->cpu='amd64';
		//elseif(`grep -i intel /proc/cpuinfo`!='') $this->cpu='i386';
		
		
	}
	
	public function &fixUri($uri){
		$uri = strtolower($uri);
		$g = '-';
		$rt = array('_',',',' ','.',':',';','/','+',
		'á','é','í','ó','ú', 'û', 'ü', 'ñ','à','è', 'ê','ì','ò','ù',
		'&',"'","\"",
		'----','---','--',
		);
		$rw = array($g, $g, $g, $g, $g, $g, $g, $g,
		'a','e','i','o','u','u','u','n','a','e','e','i','o','u',
		'and', '', '',
		$g,$g,$g,
		);
		$uri = str_replace($rt, $rw, $uri);
		//$uri = htmlspecialchars($uri, ENT_COMPAT, 'UTF-8');
		$uri = urlencode($uri);
		return $uri;
	}
	
	public function &navbar($tabla, $where, $link, $porpag = 10) {
		global $pag, $idi, $cls_cfg;
		if (isset($_GET['pag'])) { $pag = $_GET['pag']; } else { $pag = 1; } 
		$sql = "SELECT * FROM ".$tabla;
		if (isset($where)) {
			$sql .= " WHERE ".$where;
		}
		$sql = $cls_cfg->query($sql);
		$num_rows = (int) $cls_cfg->numrows($sql);
		//$porpag = 10;
		$offset = 3;
		$ret = '';
		if ($num_rows <= $porpag) {
			return $ret;
		}
		if (isset($_GET['a']) || isset($a) && $a != '') {
			$link .= '&amp;a=' . $_GET ['a'];
		}
		$total_pages = ceil($num_rows / $porpag);
		if ($total_pages > 1) {
			$prev = $pag - 1;
			if ($prev > 1) {
				$ret .= " <a href=\"$link&amp;pag=$prev\"><strong>&nbsp;&laquo;&nbsp;</strong></a> ";
			} else {
				$ret .= " <a href=\"$link\"><strong>&nbsp;&laquo;&nbsp;</strong></a> ";
			}
			$cont = 1;
			$current_page = $pag;
			while ( $cont <= $total_pages ) {
				if ($cont == $current_page) {
					$ret .= ' <a><strong>&nbsp;'.$cont.'&nbsp;</strong></a> ';
				} elseif (($cont > $current_page - $offset && $cont < $current_page + $offset) || $cont == 1 || $cont == $total_pages) {
					if ($cont == $total_pages && $current_page < $total_pages - $offset) {
						$ret .= ' <a><strong>&nbsp;...&nbsp;</strong></a> ';
					}
					//$ret .= '<a href="'.$link.($cont - 1).'">'.$cont.'</a> ';
					if (strstr($link, "?" )) {
						if ($cont == 1) {
							$ret .= " <a href=\"$link\">&nbsp;$cont&nbsp;</a> ";
						} else {
							$ret .= " <a href=\"$link?pag=$cont\">&nbsp;$cont&nbsp;</a> ";
						}
					} else {
						$ret .= " <a href=\"$link?pag=$cont\">&nbsp;$cont&nbsp;</a> ";
					}
					if ($cont == 1 && $current_page > 1 + $offset) {
						$ret .= ' <a><strong>&nbsp;...&nbsp;</strong></a> ';
					}
				}
				$cont ++;
			}
			$next = $pag + 1;
			if ($next <= $total_pages) {
				$ret .= " <a href=\"$link?pag=$next\"><strong>&nbsp;&raquo;&nbsp;</strong></a> ";
			}
			$ret .= ' <a><strong>&nbsp;'.$idi['str']['pag'].' '.$pag.' '.$idi['str']['de'].' '.$total_pages.'&nbsp;</strong></a>';
		}
		$ret = '<div id="pnav">'.$ret.'</div>';
		return $ret;
	}
	
	public function getRobots() {
		global $cls_cfg, $cls_cnt;
		if (isset($cls_cfg->gcfg['robots'])) {
			$rbts = $cls_cnt->remTxt($cls_cfg->gcfg['robots']);
		} else {
		$rbts = <<< EOPAGE
User-agent: *
Allow: /
Disallow: /lib/
Disallow: /tmp/
EOPAGE;
		}
		$cls_cfg->sendHeaders('txt', '200');
		echo $rbts;
		return $rbts;
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
	
	public function viseditor($class, $tiped='default', $jq = '$') {
		global $cfg, $gcfg, $lib, $master;
		$hdx = '
	<script src="'.$cfg['lib'].'tmce/jquery.tinymce.js"></script>
	<script>
	//<![CDATA[
		'.$jq.'(document).ready(function() {
			'.$jq.'(\''.$class.'\').tinymce({
				script_url : \''.$cfg['lib'].'tmce/tinymce.js\',
				theme : "advanced",
				skin : "default",
				skin_variant : "silver",
				plugins : "safari,pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,noneditable,xhtmlxtras,template,wordcount",
				relative_urls : true,
				document_base_url : "'.$cfg['url'].'",
				strict_loading_mode : true,
				extend_valid_elements : "fb[*],fb,nav",
				language : "'.$cfg['idi'].'",
				';
				if($tiped == 'default' ) {
				$hdx .= 'theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,charmap",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,media,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,advhr,removeformat,visualaid,styleprops,|,sub,sup,cite,abbr,acronym,del,ins,attribs,iespell",
				';
				}/* elseif($tiped == 'min') {
					$hdx .= 'theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,|,link,unlink,bullist,numlist,|,undo,redo,|,forecolor,backcolor,attribs,fullscreen"
				';
				}*/ else {
				$hdx .= 'theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,|,link,unlink,bullist,numlist,|,undo,redo,|,forecolor,backcolor,attribs,fullscreen",
				theme_advanced_buttons2 : "",
				theme_advanced_buttons3 : "",
				';
				}
				$hdx .= 'theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "center",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,
				//theme_advanced_toolbar_location : "external",
				content_css : "'.ZW_URL.'lib/css.zw",
				//template_external_list_url : "dyn/template_list.js",
				//external_link_list_url : "'.ZW_URL.'dyn/lnk_lst.js",
				//external_image_list_url : "'.ZW_URL.'dyn/img_lst.js",
				//media_external_list_url : "'.ZW_URL.'dyn/mda_lst.js",
				// Replace values for the template plugin
				template_replace_values : {
					siteurl : "'.$cfg['url'].'",
					staffid : "991234"
				}
			});
		});
	//]]>
	</script>
	';
		return $hdx;
	}
	
	public function &curly($url) {
		global $cls_cfg, $cfg;
		$jsc = curl_init();
		curl_setopt($jsc, CURLOPT_URL,$url);
		curl_setopt($jsc, CURLOPT_HEADER, false);
		curl_setopt($jsc, CURLOPT_USERAGENT, $cls_cfg->agent);
		curl_setopt($jsc, CURLOPT_HTTPHEADER, array('Accept-Language: '.$cfg['idi']));
		curl_setopt($jsc, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($jsc, CURLOPT_VERBOSE,false);
		curl_setopt($jsc, CURLOPT_TIMEOUT, 5);
		curl_setopt($jsc, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($jsc, CURLOPT_SSLVERSION,3);
		curl_setopt($jsc, CURLOPT_SSL_VERIFYHOST, FALSE);
		$jsc = curl_exec($jsc);
		return $jsc;
		@curl_close($jsc);
		unset($jsc);
	}
	
	public function &feedproxy($req_uri) {
		global $cls_cfg;
		$xml = $req_uri;
		$feed = explode('feedproxy/', $_SERVER['REQUEST_URI']);
		/*$xml = curl_init();
		$agent = $cls_cfg->agent;
		curl_setopt($xml, CURLOPT_URL,$feed[1] );
		curl_setopt($xml, CURLOPT_HEADER, false);
		curl_setopt($xml, CURLOPT_USERAGENT, $agent);
		curl_setopt($xml, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($xml, CURLOPT_VERBOSE,false);
		curl_setopt($xml, CURLOPT_TIMEOUT, 5);
		curl_setopt($xml, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($xml, CURLOPT_SSLVERSION,3);
		curl_setopt($xml, CURLOPT_SSL_VERIFYHOST, FALSE);
		$xml = curl_exec($xml);
		echo $xml;
		@curl_close($xml);
		unset($xml);*/
		header("Content-Type: text/xml");
		echo $this->curly($feed[1]);
		exit;
	}

	public function &base_encode($num, $alphabet) {
		$base_count = strlen($alphabet);
		$encoded = '';
	
		while ($num >= $base_count) {
	
			$div = $num/$base_count;
			$mod = ($num-($base_count*intval($div)));
			$encoded = $alphabet[$mod] . $encoded;
			$num = intval($div);
		}
	
		if ($num) $encoded = $alphabet[$num] . $encoded;
		return $encoded;
	}
	
	public function &base_decode($num, $alphabet) {
		$decoded = 0;
		$multi = 1;
		while (strlen($num) > 0) {
			$digit = $num[strlen($num)-1];
			$decoded += $multi * strpos($alphabet, $digit);
			$multi = $multi * strlen($alphabet);
			$num = substr($num, 0, -1);
		}
	
		return $decoded;
	}
	
	public function &basedomain() {
		$bd = str_replace('https', '', ZW_URL);
		$bd = str_replace('http', '', $bd);
		$bd = str_replace('://', '', $bd);
		$bd = str_replace('www.', '', $bd);
		$bd = str_replace('/', '', $bd);
		//define('ZWDOM', $bd);
		//unset($basedomain);
		return $bd;
	}
	
	public function &manifestCache() {
		global $cls_cfg, $cfg, $lib, $visd;
		error_reporting(0);
		sendHeaders('cache', '200');
		$cache_manifest = array();
		$cache_manifest['default'] = ZW_URL.'index.html
'.$lib.'*
'.$lib.'jquery.js
';
		if (file_exists($cfg['dir'].$visd.'style.css')) {
			$cache_manifest['default'] .= ZW_URL.$visd.'style.css';
		} else {
			$cache_manifest['default'] .= ZW_URL.'style.css';
		}
	
		$cache_manifest['cache'] = '';
		$cache_manifest['network'] = '';
		$cache_manifest['fallback'] = '';
	
		if($cls_cfg->admin = true) {
			$cache_manifest['default'] .= '';
			$cache_manifest['cache'] .= $cfg['lib'].'adm.css
'.$cfg['lib'].'tmce/tiny_mce.js';
			$cache_manifest['network'] .= '';
			$cache_manifest['fallback'] .= ZW_URL.'chat.php /error.html';
		}
	
		$manifestcache = 'CACHE MANIFEST
'.$cache_manifest['default'].'
'.$cache_manifest['cache'].'
	
NETWORK:
'.$cache_manifest['network'].'
	
FALLBACK:
'.$cache_manifest['fallback'].'';
		echo $manifestcache;
		return $manifestcache;
	}
}
$zihweb = new ZihWebCMS();

define('ZWDOM', $zihweb->basedomain());

require ZW_DIR.'sis'.Ds.'cfg.php';
require ZW_DIR.'sis'.Ds.'usr.php'; // usuarios

/* master WASA * v/
ORIGINAL SET
/*fin master WASA */
$pag_vista = array();
$req_uri = $cls_cfg->req_uri;
$fldr = $cls_cfg->fldr;
$ruri = $cls_cfg->ruri;
$cfg['idi'] = $cls_cfg->idis;
if (isset($_GET['tmp']) && $_GET['tmp'] == 0) { $cls_cfg->gcfg['tmp'] = 0; }

require_once ZW_DIR.'sis'.Ds.'cnt.php'; // contenito texto/html/css

$lastmod = $cls_cfg->lastmod();

$pag_id = $cls_cnt->pag_id;

// Aliaz redir
if (!strstr($ruri,ZW_ADM) || !isset($pag_id)) {
	$noadmin404 = true;
	$sql = $cls_cfg->query('SELECT nom, val FROM '.TBLPRE.'cnt_cfg WHERE idi LIKE "xR" AND nom LIKE "'.$ruri.'"');
	while (list($nom, $val) = $cls_cfg->fetch($sql)) {
		if ( $noadmin404 == true && $nom == $ruri ):
			//header( "HTTP/1.1 301 Moved Permanently" );
			header( "HTTP/1.1 302 Moved temporarily" );
			//if (strstr($val, 'http://')):
			//	header( "Location: ".$val );
			//else:
				header( "Location: ".$val );
			//endif;
			exit();
		endif;
	}
}

// Config redir
if ($noadmin404 == true && isset($cls_cfg->gcfg['redir']) && $cls_cfg->gcfg['redir'] != '' && $cls_cfg->req_uri == '') {
	//header( "HTTP/1.1 301 Moved Permanently" );
	header( "HTTP/1.1 302 Moved temporarily" );
	//header( 'Location: '.$cls_cnt->remTxt($gcfg['redir']) );
	header( 'Location: '.$cls_cfg->gcfg['redir'] );
	exit();
}

function esAdmin() {
	global $cls_cfg;
	return $cls_cfg->esAdmin();
}

function esClient() {
	global $cls_cfg;
	return $cls_cfg->esClient();
}

function sendHeaders($ext = 'html', $h='200') {
	global $cls_cfg;
	return $cls_cfg->sendHeaders($ext, $h);
}

require_once ZW_DIR.'sis'.Ds.'img.php'; // imagenes
require_once ZW_DIR.'sis'.Ds.'dyn.php';
require_once ZW_DIR.'sis'.Ds.'idi.php'; // idioma
require_once ZW_DIR.'sis'.Ds.'frm.php';


// $_POST Request listener
require ZW_DIR.'sis'.Ds.'_post.php';

// Request URI
require ZW_DIR.'sis'.Ds.'_ruri.php';

if (isset($_COOKIE['i'])) {
			$cls_cfg->idis = $_COOKIE['i'];
			$_SESSION['i'] = $cls_cfg->idis;
		}

/**
 * 
 * Abre un XML desde el servidor para el usuario
 */
function feedproxy($req_uri) {
	global $zihweb;
	$ret = $zihweb->feedproxy($req_uri);
	return $ret;
}

function admBtn($ga=true) {
	global $cls_cfg;
	return $cls_cfg->admBtn($ga);
}
function unVar($vars){
	$var = explode(',', $vars);
	while ($var) {
		unset($$var);
	}
}
/**
 * navegacion de paginas
 */
function navbar($tabla, $where, $link, $porpag = 10) {
	global $zihweb, $pag, $idi, $cls_cfg;
	return $zihweb->navbar($tabla, $where, $link, $porpag);
}


// Get browser local
function gBl() {
	require_once ZW_DIR.'sis'.Ds.'api'.Ds.'gbl.php';
	$bcf = ZW_DIR.'sis'.Ds.'api'.Ds.'browscap.ini';
	return get_browser_local(null, false, $bcf);
}
function uuid($prefix = ''){
	global $zihweb;
	return $zihweb->uuid($prefix);
}
/**
 * Escribe el JavaScript para TinyMCE
 * 
 * @param string Class CSS
 * @param string Tipo de editor
 */
function viseditor($class, $tiped, $jq = '$') {
	global $zihweb;
	return $zihweb->viseditor($class, $tiped, $jq);
}


function recount_serialized_bytes($text) {
	mb_internal_encoding("UTF-8");
	mb_regex_encoding("UTF-8");
	mb_ereg_search_init($text, 's:[0-9]+:"');

	$offset = 0;

	while(preg_match('/s:([0-9]+):"/u', $text, $matches, PREG_OFFSET_CAPTURE, $offset) ||
		  preg_match('/s:([0-9]+):"/u', $text, $matches, PREG_OFFSET_CAPTURE, ++$offset)) {
		$number = $matches[1][0];
		$pos = $matches[1][1];

		$digits = strlen("$number");
		$pos_chars = mb_strlen(substr($text, 0, $pos)) + 2 + $digits;

		$str = mb_substr($text, $pos_chars, $number);

		$new_number = strlen($str);
		$new_digits = strlen($new_number);

		if($number != $new_number) {
			// Change stored number
			$text = substr_replace($text, $new_number, $pos, $digits);
			$pos += $new_digits - $digits;
		}

		$offset = $pos + 2 + $new_number;
	}

	return $text;
}

function gCfg($idi = '00') {
	global $cls_cfg;
	return $cls_cfg->cfg($idi);
}
