<?php
/**
 * Obtener el contenido de las paginas y secciones
 * 
 * @package ZihWeb CMS
 * @subpackage Content
 * @version 0.9.5
 * @author Marco Garcia <micyaotl@gmail.com>
 * @copyright 2011 feelRiviera.com
 */
if(!isset($js)) { $js = '';}
if(!isset($jsjq)) { $jsjq = '';}
if(!isset($css)) { $css = '';}

/**
 * 
 * Retrives content and pharses to show for endusers
 * @author Marco
 *
 */
class Content {
	var $pag_id;
	var $cont;
	var $ruri;
	var $fldr;
	var $req_uri;
	var $jo = array();
	var $js;
	var $jsjq;
	var $jsfull;
	var $css;
	var $admin;
	var $ext_cls;
	var $bodystart = '';
	var $footerjs = '';
	var $httph = 200;

	public function __construct() {
		global $cls_cfg, $cfg, $gcfg;
		$this->ruri = $cls_cfg->ruri;
		$this->fldr = $cls_cfg->fldr;
		$this->req_uri = $cls_cfg->req_uri;
		$this->idis = $cls_cfg->idis;
		$this->uril = $cls_cfg->uril;
		$this->stime = $cls_cfg->smtime;
		$this->admin = $cls_cfg->admin;

		$query = 'SELECT * FROM '.TBLPRE.'cnt WHERE uri = "'.$this->ruri.'"';
		if($cls_cfg->numrows($cls_cfg->query($query)) > 1) $query.= ' AND idi = "'.$this->idis.'"';
		$query.=' LIMIT 1';
		$query = $cls_cfg->query($query);
		$this->cont = $cls_cfg->fetch($query, 'assoc');
		$this->pag_id = $this->cont['id'];
		$this->meta = unserialize($this->cont['meta']);
		
		if (!in_array($this->fldr[0], $cls_cfg->uril) && isset($this->fldr[1])) {
			$query = 'SELECT * FROM '.TBLPRE.'cnt WHERE uri = "'.$this->fldr[0].'/"';
			if($cls_cfg->numrows($cls_cfg->query($query)) > 1) $query.= ' AND idi = "'.$this->idis.'"';
			$query.=' LIMIT 1';
			$query = $cls_cfg->query($query);
			$fscont = $cls_cfg->fetch($query, 'assoc');
			//$this->pag_id = $this->cont['id'];
			$this->meta = unserialize($fscont['meta']);
		}
		// Carga de extensiones
		if (isset($this->meta['ext']) && file_exists(ZW_DIR.'ext'.Ds.$this->meta['ext'].'.php')) {
			$extadm = $extcnt = $exthdx = $admhdx = $addlnk = $tblxst = $upldimg = $nvlnkf = '';
			$urluri = ZW_URL.$cls_cfg->ruri;
			require(ZW_DIR.'ext'.Ds.$this->meta['ext'].'.php');
			if (class_exists('ext'.ucfirst($this->meta['ext']))) {
				$ext_cls = 'ext'.ucfirst($this->meta['ext']);
				$this->ext_cls = new $ext_cls();
			}
		}
	}
	
	public function &textoEnIdioma($texto) {
		global $cls_cfg;
		$patron = array();
		$reemplazo = array();
		
		// español
		$patron[] = "/\[es](.*)\[\/es\]/sU";
		if ($cls_cfg->idis == "es") {
			$reemplazo[] = '\\1';
		} else {
			$reemplazo[] = "";
		}
		// ingles
		$patron[] = "/\[en](.*)\[\/en\]/sU";
		if ($cls_cfg->idis == "en") {
			$reemplazo[] = '\\1';
		} else {
			$reemplazo[] = "";
		}
		// frances
		$patron[] = "/\[fr](.*)\[\/fr\]/sU";
		if ($cls_cfg->idis == "fr") {
			$reemplazo[] = '\\1';
		} else {
			$reemplazo[] = "";
		}
		// italiano
		$patron[] = "/\[it](.*)\[\/it\]/sU";
		if ($cls_cfg->idis == "it") {
			$reemplazo[] = '\\1';
		} else {
			$reemplazo[] = "";
		}
		// aleman
		$patron[] = "/\[de](.*)\[\/de\]/sU";
		if ($cls_cfg->idis == "de") {
			$reemplazo[] = '\\1';
		} else {
			$reemplazo[] = "";
		}
		// chino
		$patron[] = "/\[zh](.*)\[\/zh\]/sU";
		if ($cls_cfg->idis == "zh") {
			$reemplazo[] = '\\1';
		} else {
			$reemplazo[] = "";
		}
		// brazil
		$patron[] = "/\[pr](.*)\[\/pr\]/sU";
		if ($cls_cfg->idis == "pr") {
			$reemplazo[] = '\\1';
		} else {
			$reemplazo[] = "";
		}
		
		//japones
		$patron[] = "/\[ja](.*)\[\/ja\]/sU";
		if ($cls_cfg->idis == "ja") {
			$reemplazo[] = '\\1';
		} else {
			$reemplazo[] = "";
		}
		
		// catalan
		$patron[] = "/\[ca](.*)\[\/ca\]/sU";
		if ($cls_cfg->idis == "ca") {
			$reemplazo[] = '\\1';
		} else {
			$reemplazo[] = "";
		}
		$ret = preg_replace($patron, $reemplazo, $texto);
		return $ret;
	}
	
	public function &remTxt($txt) {
		global $cls_cfg, $cls_usr, $cls_dyn, $cfg, $gcfg;
		$txt = $this->textoEnIdioma($txt);
		$pat = array(
			"/\[lib\]/sU",
			"/\[site\]/sU",
			"/\[title\]/sU",
			"/\[yyyy\]/sU",
			"/\[feelriviera\]/sU",
			"/\[webdev\]/sU",
			"/\[madein\]/sU",
			"/\[imgurl\]/sU",
			"/\[siteurl\]/sU",
			"/\[contacto\]/sU",
			"/\[webimg\]/sU",
	//		"/\[maphdx\]/sU",
	//		"/\[mapcnt\]/sU",
			"/\[fblogin\]/sU",
			"/\[plusone\]/sU",
			"/\[fblike\]/sU",
			"/\[tweet\]/sU",
			"/\[shareit\]/sU",

		//"/\[mapkey\]/sU",
		"/\[vcard\]/sU",
		"/\[idi\]/sU",
		);
		//if(isset($gcfg['mapkey'])) { $mapkey = $gcfg['mapkey']; } else { $mapkey = ''; }
		if(isset($gcfg['vcard'])) { $vcard = $gcfg['vcard']; } else { $vcard = ''; }
		
		$zihweblink = '<a href="http://www.zihweb.com/" class="devfr" title="ZihWeb Development IT Consulting">ZihWeb</a>';
		
		$rem = array(
			$GLOBALS['lib'],
			'<a href="'.ZW_URL.'">'.$gcfg['title'].'</a>',
			$gcfg['title'],
			date('Y'),
			'<a href="http://feelriviera.com/" class="devfr" title="feelRiviera">feelRiviera</a>',
			// [webdev]
			$zihweblink,
			// [madein]
			$zihweblink,
			ZW_URL.'img/',
			ZW_URL,
			'[en]contact[/en][es]contacto[/es][fr]contact[/fr][de]kontakt[/de][it]contatto[/it][ja]コンタック[/ja][zh]联络[/zh]',
			ZW_URL.'webimg/',
		//	$maphdx,
		//	$mapcnt,
			$cls_usr->fblilo,
			$this->plusOne(),
			$this->fbLike(),
			$this->tweet(),
			$this->shareit('html'),

		//	$mapkey,
			$vcard,
			$cfg['idi'],
		);
		
		
		
		$txt = preg_replace($pat, $rem, $txt);
		//$ret = preg_replace_callback('|\[gal\](.+?)\[/gal\]|i', array(&$this, 'gal'), $ret);
		
		//$ret = preg_replace_callback('|\[ext\](.+?)\[/ext\]|i', array(&$this, 'ext'), $ret);
		//$ret = preg_replace_callback('|\[xt\](.+?)\[/xt\]|i', array(&$this, 'ext'), $ret);
		return $txt;
	}
	
	public function remTxtCnt($txt) {
		$pat = array(
			"/\[contactform\]/sU",
			"/\[sitemap\]/sU",
			"/\[resjsjq\]/sU",
			"/\[resform\]/sU",
			"/\[shareit\]/sU",
			'/\[video\](.+?)\[\/video\]/i'
		);
		$rem = array(
			$this->getContactForm(),
			$this->getMenu('sitemap', 1, 'mnsb'),
			$this->getReserveForm('hdx'),
			$this->getReserveForm('cnt'),
			$this->shareit('html'),
			$this->video("\\1"),
		);
		/*
		if (strstr($txt, '[resjsjq]')){
			$pat[] = "/\[resjsjq\]/sU";
			$rem[] = $this->getReserveForm('hdx');
		}
		if (strstr($txt, '[resform]')){
			$pat[] = "/\[resform\]/sU";
			$rem[] = $this->getReserveForm('cnt');
		}*/
		//thumbnails
		$txt = preg_replace_callback('/\[gal\](.+?)\[\/gal\]/i', create_function('$coi',
		'global $cls_dyn;
		$txt = preg_replace("/\[gal\]/sU","", $coi[0]);
		$txt = preg_replace("/\[\/gal\]/sU","",$txt);
		$txt = explode("|", $txt);
		if (!isset($txt[2])) $txt[2] = "100x100";
		return $cls_dyn->thumbs($txt[0],$txt[1],$txt[2]);'), $txt);
		
		// slideshow
		$txt = preg_replace_callback('/\[slide\](.+?)\[\/slide\]/i', create_function('$coi',
		'global $cls_dyn;
		$txt = preg_replace("/\[slide\]/sU","", $coi[0]);
		$txt = preg_replace("/\[\/slide\]/sU","",$txt);
		$txt = explode("|", $txt);
		if (!isset($txt[1])) $txt[1] = "n";
		return $cls_dyn->slide($txt[0], $txt[1]);'), $txt);
		
		$txt = preg_replace($pat, $rem, $txt);
		
		return $txt;
	}

	private function &fdhext($a = '') {
		if (strstr($a[1],'|')) {
			$a = explode('|', $a[1]);
			$a = $a[0];
		} else {
			$a = $a[1];
		}
		if (file_exists(ZW_DIR.Ds.'ext'.Ds.'ext'.Ds.$a.'.php')) {
			require ZW_DIR.Ds.'ext'.Ds.'ext'.Ds.$a.'.php';
		}
		$cont['cnx'] = '';
		$cont['cnt'] = '';
		return $cont;
	}
	
	/**
	 * Retrives content from database and pharses it on html
	 * 
	 * @uses $cls_cfg & $ext_cls
	 * @param string $tip kind of content
	 * @param integer $id content id
	 * @return mixed $content html content
	 */
	public function &getContent($tip, $id=null) {
		global $fldr, $cls_cfg, $gcfg;
		//$gcfg = $cls_cfg->gcfg;
		$req_uri = $this->req_uri;
		$ruri = $this->ruri;
		$ret = $this->remTxt($this->cont);
		$meta = $this->meta;
	
		if (!isset($ret['meta'])) { $ret['meta'] = ''; }
		if (!isset($ret['keywords'])) { $ret['keywords'] = ''; }
		if (!isset($ret['id'])) { $ret['id'] = ''; }
		if (!isset($ret['hdx'])) { $ret['hdx'] = ''; }
		if (!isset($ret['cnx'])) { $ret['cnx'] = ''; }
		if (!isset($ret['cnt'])) { $ret['cnt'] = ''; }
		
		if ($tip == 'hdx') {
			if ($ret['hdx'] != '') {
				$ret['hdx'] = $this->remTxtCnt($ret['hdx']);
			}  elseif (isset($gcfg['g-hdx'])) {
				$ret['hdx'] = $gcfg['g-hdx'];
			}
		}

		if ($tip == 'cnx') {
			if ($ret['cnx'] != '') {
				$ret['cnx'] = $ret['cnx'];
			}  elseif (isset($gcfg['g-cnx'])) {
				$ret['cnx'] = $gcfg['g-cnx'];
			}
		}
	
		// Carga de extensiones
		if (isset($meta['ext']) && file_exists(ZW_DIR.'ext'.Ds.$meta['ext'].'.php')) {
				if (class_exists('ext'.ucfirst($meta['ext']))) {
					$extcnt = $exthdx = '';
					//$ext_cls = 'ext'.ucfirst($this->meta['ext']);
					//$ext_cls = new $ext_cls();
					$ret = $this->ext_cls->cont;
					$ret['title'] = $this->cont['tit'] = $ret['tit'] = $this->ext_cls->tit;
					$ret['hdx'] = $this->ext_cls->exthdx();
					$pag = '';
					if (isset($_GET['pag'])) {
						$pag = $_GET['pag'];
					}
					$ret['cnt'] = $this->ext_cls->extcnt($pag);
				} else {
					$extcnt = $exthdx = '';
					include(ZW_DIR.'ext'.Ds.$meta['ext'].'.php');
					$ret['hdx'] = $exthdx;
					$ret['cnt'] = $extcnt;
				}
				if (!isset($ret['id'])) $ret['id'] = '';
				if (!isset($ret['cnx'])) $ret['cnx'] = '';
		}
		
		if(!isset($this->cont['pag_id']) && !isset($this->ext_cls)) { //!isset($this->ext_cls->cont) //!isset($this->ext_cls)
			if(!isset($this->cont['cnt'])) $this->cont['tit'] = 'ERROR 404';
			if ($tip == 'cnt' && !isset($this->cont['cnt'])) {
			$this->cont['cnt'] = '<h2>ERROR 404!</h2>'.N.T.T.'<p>';
			$this->cont['cnt'] .= '[es]La p&aacute;gina solicitada no existe, verifique la dirección o seleccione otra de las opciones del sitio[/es][en]The page does not exist, verify the address or select another option on the site[/en][de]Die Seite ist nicht vorhanden, überprüfen Sie die Adresse oder wählen Sie eine andere Site-Optionen[/de][it]La pagina non esiste, verificare l\'indirizzo o selezionare le opzioni di un altro sito[/it][fr]La page n\'existe pas, vérifiez l\'adresse ou sélectionnez un autre d\'options[/fr][zh]该网页不存在，请检查地址或其他网站的选项中选择[/zh][ja]このページは、存在しないアドレスを確認するか、別のサイトのオプションを選択する[/ja]';
			$this->cont['cnt'] .= '.</p>
			<p><em>'.ZW_URL.$req_uri.'</em> ? o_O</p>'.N;
			$this->cont['cnt'] .= $this->getMenu('sitemap', 1, 'smsb');
			}
		}
		if($ruri == 'phpinfo') {
			$this->cont['cnt'] = $cls_cfg->phpinfo;
		}
		
		// $this->remTxt();
		$ret = $ret[$tip];
		if ($this->admin = true && isset($gcfg['verdebug']) && $gcfg['verdebug'] == 1 && $tip == 'cnt') {
			if(preg_match( '/\.(xml)$/i', $ruri)) {
				$ret.= '<!--//<![CDATA[';
			}
			if (preg_match( '/\.(js)$/i', $ruri) || preg_match( '/\.(css)$/i', $ruri)){
				$ret.= '/*';
			}
		
			if(preg_match( '/\.(xml)$/i', $ruri)) {
				$ret.= '-->';
			}
			if (preg_match( '/\.(js)$/i', $ruri) || preg_match( '/\.(css)$/i', $ruri)){
				$ret.= '*/';
			}
		}
		if ($tip == 'cnt' || $tip == 'cnx') {
			$ret = $this->remTxtCnt($ret);
		}
		// $this->remTxt()
		return $ret;
	}
	
	public function &getHead($tip='pag', $conf = array()) {
		global $url, $lib, $visd, $visual, $pag_id, $cls_cfg, $js,$jsjq,$css, $cfg;
		$gcfg = $cls_cfg->gcfg;
		$meta = $this->meta;
		$this->js = $js;
		$this->jsjq = $jsjq;
		$this->css = $css;
		//$wb = gBl();
		$visd = str_replace('\\', '/', $visd);
		$head = '';
		if (in_array($tip, $cls_cfg->uril)) {
			$head .= '<!DOCTYPE html>
	<html lang="'.$this->idis.'" class="'.$tip.'">
	<head>';
		}
		if (isset($this->ext_cls->cont['title'])) {
			$tit = $this->ext_cls->cont['title'];
		} else {
			$tit = $this->getContent('tit');
		}
		$title = $this->textoEnIdioma( $tit.' | '.$gcfg['title']);

		$head .= '<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>'.$title.'</title>';
	$oginfo = N.T.'<meta property="og:title" content="'.$title.'" />';
		// <meta name="DC.title" id="DC.title" content="'.$title.'" />
		if (isset($meta['keywrds']) || isset($gcfg['keywords'])) {
			if(isset($meta['keywrds'])) {
				$metakw = $meta['keywrds'];
			} else {
				$metakw = $gcfg['keywords'];
			}
			$head .= N.T.'<meta name="keywords" content="'.$metakw.'" />';
		}
		if(isset($meta['desc'])) {
			$metade = $meta['desc'];
		} elseif (isset($gcfg['description'])) {
			$metade = $gcfg['description'];
		} else { $metade = ''; }
		$head .= N.T.'<meta name="description" content="'.$metade.'" />';
		$oginfo .= N.T.'<meta property="og:description" content="'.$metade.'" />';
		$head .= N.T.'<meta name="author" content="ZihWeb CMS - http://www.zihweb.com/"/>';
		$oginfo .= N.T.'<meta property="og:url" content="'.ZW_URL.$cls_cfg->ruri.'" />
	<meta property="og:type" content="Page"/>
	<meta property="og:site_name" content="'.$title.'"/>';
		$head .= $oginfo;
		if (isset($gcfg['w2-twitter'])) {
			$twitterc = '
	<meta name="twitter:card" content="summary">
	<meta name="twitter:site" content="@'.$gcfg['w2-twitter'].'">
	<meta name="twitter:creator" content="@feelRiviera">
	<meta name="twitter:title" content="'.$tit.'">
	<meta name="twitter:description" content="'.$metade.'">
	<meta name="twitter:image" content="'.$gcfg['image-twitter'].'">
				';
		} else {
			$twitterc = '';
		}
		if (isset($gcfg['w2-plus'])) {
			$head .= N.T.'<link href="'.$gcfg['w2-plus'].'" rel="publisher" />';
		}
		/*if (isset($gcfg['g+publisher'])) {
			$head .= N.T.'<link href="https://plus.google.com/'.$gcfg['g+publisher'].'/" rel="publisher" />';
		}*/
		
		$head .= $twitterc.'
	<meta name="generator" content="'.ZW_V.'"/>
	<link href="'.$url.$visd.'favicon.ico" rel="icon"/>
	<link href="'.$url.$visd.'favicon.ico" rel="shortcut icon"/>';
	if (isset($gcfg['feed']) && $gcfg['feed'] != '') $head .= N.T.'<link rel="alternate" type="application/rss+xml" href="'.$gcfg['feed'].'"/>';
	$head .= N.T.'<link rel="sitemap" href="'.$url.'xml/sitemap.xml" type="text/xml"/>';
	/*
	<meta name="distribution" content="Global" />
	
	Q: '.$cls_cfg->tq.'
	S. T.: '.$cls_cfg->smtime.'
	F. T.: '.$cls_cfg->ftime().'
	T. T.: '.$cls_cfg->ttime.'
	 */
	$head .= '
	<!--
	'.ZW_V.'  (c) 2009-'.date('Y').' ZihWeb CMS (www.zihweb.com)
	-->';
	$this->css .= '
	@import url('.$url.'lib/css.zw);
	@import url('.$url.$visd.'style.css);'.N;// 
		if(isset($gcfg['jqp'])) {
			$head.= N.T.'<script src="'.ZW_URL.'lib/js.zw"></script>';
			$jqp = explode(',', $gcfg['jqp']);
			foreach($jqp as $jp) {
				if($jp == 'pngfix') {
					$this->jsjq .= N.T.'jQ(function(){jQ(document).pngFix();});';
				}
				if ($jp == 'lazyload') {
					$this->jsjq .= N.T.'jQ("img").lazyload({effect : "fadeIn",threshold : 200});';
				}
				if($jp == 'pph') {
					if (isset($conf['pph'])) {
					$this->jsjq .= N.T.'jQ("a[rel^=\'pph\']").prettyPhoto({theme: \''.$conf['pph'].'\', hideflash: true});';
					} else {
					$this->jsjq .= N.T.'jQ("a[rel^=\'pph\']").prettyPhoto({theme: \'dark_square\', hideflash: true});';
					}
				}
			}
		}
		if (file_exists($cfg['dir'].$visd.$visual.'.js')) {
				$head .= N.T.'<script src="'.$url.$visd.$gcfg['visual'].'.js"></script>';
		}
		$head .= '';
		if(isset($gcfg['w2']) && $gcfg['w2'] == 1 && !isset($gcfg['w2fx'])) {
			$this->jsjq .= N.T.'jQ("#w2 a").fadeTo("fast", 0.5); jQ("#w2 a").hover( function(){ jQ(this).fadeTo("fast", 1.0); }, function(){ jQ(this).fadeTo("fast", 0.5); });'.N;
		}
		$this->jsjq .= N.T.T.'';
		if (isset($this->jo['bp-jq']) && (isset($gcfg['feed']) || $gcfg['feed'] != '')) {
			if(isset($gcfg['feed-title']) && $gcfg['feed-title'] != '') {
				$feedtit = $gcfg['feed-title'];
			} else {
				$feedtit = textoEnIdioma('[es]En Nuestro Blog[/es][en]In Our Blog[/en]');
			}
			
			if (isset($gcfg['feed-script']) && $gcfg['feed-script'] != '') {
				$this->jsjq .= $gcfg['feed-script'];
			} else {
			$this->jsjq .= "
		jQ(document).getFeed({
			url: '".$url."feedproxy/".$gcfg['feed']."',
			success: function(feed) {
				var html = '<h4>".$feedtit."</h4>';
				for(var i = 0; i < feed.items.length && i < 1; i++) {
					var item = feed.items[i];
					html += '<div class=\"feedItem\">';
					html += '<a href=\"".$gcfg['feed']."\" class=\"pbrss\">feed rss</a> <span id=\"pbpt\">'+item.title+'</span>';
					html += '<p>'+item.description+' </p></div>';
					html += '<a href=\"'+item.link+'\">".textoEnIdioma('[es]leer mas[/es][en]read more[/en]')." &raquo;</a>';
				}
				jQ('#pb').append(html).toggle();
				jQ('#pb').slideDown('slow');
				jQ('#pb img').css({width: '100%'});
			}
		});";
			$this->jo['bp-jq'] = true;
			}
		}
		
		if ($this->jo['shareit'] == true) {
			$this->jsjq .= $this->shareit('jsjq');
		}
		/**
		 * EXTRA META
		 */
		if(isset($gcfg['verif'])) $head .= N.T.'<meta name="google-site-verification" content="'.$gcfg['verif'].'"/>';
		if(isset($gcfg['geo'])) {
		$head .= N.T.'<meta name="geo.placename" content="'.$gcfg['geo-place'].'" />
	<meta name="geo.region" content="'.$gcfg['geo-reg'].'"/>
	<meta name="geo.position" content="'.$gcfg['geo'].'"/>
	<meta name="ICBM" content="'.$gcfg['geo'].'"/>';
		}
		if (isset($gcfg['category'])) {
			$head .= N.T.'<meta name="category" content="'.$gcfg['category'].'" />';
		}
		/**
		 * Google Analytics
		 */
		if(isset($gcfg['ga'])) { // && $gcfg['ga'] == true && !WASA
			$this->js .= '';
			if (isset($gcfg['gatl']) && $gcfg['gatl'] == 1) {
				$this->js .= '';
			}
			/*

			//js
			var _gaq = _gaq || [];

			//gatl
			var pluginUrl = \'//www.google-analytics.com/plugins/ga/inpage_linkid.js\';
			_gaq.push([\'_require\', \'inpage_linkid\', pluginUrl]);
			
			_gaq.push([\'_setAccount\', \''.$gcfg['ga'].'\']);
			_gaq.push([\'_trackPageview\']);
			  (function() {
				var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
				ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
				var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
			  })();
			 */
			$this->js .= '
  (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

  ga(\'create\', \''.$gcfg['ga'].'\', \'feelriviera.com\');
  ga(\'send\', \'pageview\');
			  ';
		}
		$head .= '
	<style>
	'.$this->css;
		if ($this->jo['shareit'] == true) {
			$head .= $this->shareit('css');
		}
		$head .= '
	</style>
		'.N.T;
		
		$this->jsfull = $this->js.'
		jQ(document).ready(function(){
			'.$this->jsjq.N.'
		});';
		
		$head .= '
	<script>
	//<![CDATA[
	'.$this->js.'
		var jQ = jQuery.noConflict();
		jQ(document).ready(function(){
			'.$this->jsjq.N.'
		});
	//]]>
	</script>
		';
		/*if(isset($gcfg['hdx'])) {
			$head .= $gcfg['hdx'];
			} else {*/
			$head .= $this->getContent('hdx');
		//}
		if ($tip == 'cont' || $tip == 'mobi') {
			$head .= '
	</head>';
		}
		return $head;
	}


	private function &saveTmp($navid='nav', $iid, $menu) {
		global $cls_cfg;
		//global $gcfg, $ruri, $cfg, $req_uri, $cls_cfg;
		//$m = $cls_cfg->cfg($mnid);	
		// 		if (!isset($m[''.$mnid])) {
		// 			$cls_cfg->query("INSERT INTO ".TBLPRE."cnt_cfg (nom, idi, val) VALUES ('".$mnid."', '".$cfg['idi']."-m', ".fixSql($menu).")");
		// 		}
		// 		if ($cls_cfg->gcfg['tmp'] == 0 || isset($m[''.$mnid])) {
		// 			$cls_cfg->query(sprintf("UPDATE %s SET val = %s WHERE idi LIKE %s AND nom LIKE %s", TBLPRE.'cnt_cfg', fixSql($menu),  fixSql($cfg['idi']."-m"), fixSql($mnid)));
		// 		}
		$menuss = $cls_cfg->cfg($iid);
		if(!isset($menuss[$navid])) {
			$cls_cfg->query("INSERT INTO ".TBLPRE."cnt_cfg (nom, idi, val) VALUES ('".$navid."', '".$iid."', '".fixSql($menu)."')");
		}
		if ($cls_cfg->gcfg['tmp'] == 0 || isset($menuss[$navid])) {
			$cls_cfg->query(sprintf("UPDATE %s SET val = %s WHERE idi LIKE %s AND nom LIKE %s", TBLPRE.'cnt_cfg', fixSql($menu), fixSql($iid) ,fixSql($navid)));
		}
		return $iid;
	}

	/**
	 * 
	 * Crear menus
	 * @param str id del menu $mnid
	 * @param boolean ver submenu $vs
	 * @param str submenu class $smc
	 */
	public function &getMenu($mnid='nvg', $vs=0, $smc='sub') {
		global $gcfg, $ruri, $cfg, $req_uri, $cls_cfg;
		//$mnid = $mnid.'-'.$cls_cfg->idis.'-m';
		$m = $cls_cfg->cfg($cls_cfg->idis.'-m');
		if (isset($m[$mnid]) && isset($cls_cfg->gcfg['tmp']) && $cls_cfg->gcfg['tmp'] == 1){
			$menu = '
			<!-- cmn--tmp -->
			'.$m["$mnid"];
		} else {
		$menu = '';
		$rel = '';//' rel="nofollow"';
		$mn = "SELECT id, sup, uri, idi, meta FROM ".TBLPRE."cnt WHERE
		sup LIKE '%.0' AND idi = '".$cfg['idi']."' OR idi = '00' AND sup LIKE '%.0' ORDER BY sup
		#menuq";
		$mn = $cls_cfg->query($mn);
		$uril = '';
		if(in_array($this->fldr[0], $this->uril)) $uril = $this->fldr[0].'/';
		$menu .= '
	<!-- m i:'.$mnid.' -->
	<nav id="'.ucfirst($mnid).'">
	<ul id="'.$mnid.'">';
		while($fil = $cls_cfg->fetch($mn,'array')) {
			list($pos, $pad) = explode('.', $fil['sup']);
			$filsupid = $fil['id'];
			$meta = unserialize($fil['meta']);
			$mna = "SELECT id, sup, uri, idi, meta FROM ".TBLPRE."cnt WHERE
			sup LIKE '%.".$filsupid."' AND idi = '".$cfg['idi']."' OR idi = '00' ORDER BY sup
			#submenuq";
			$mna = $cls_cfg->query($mna);
			
			$link = ZW_URL.$uril.$fil['uri'];
			if(preg_match('/http/', $fil['uri'])) {
				$link = $fil['uri'];
			}
			if(preg_match('/#/', $fil['uri'])) {
				$link = $fil['uri'];
			}
			if(!isset($meta['class']) || $meta['class'] == 'class') { $meta['class'] = ''; }
			if( $fil['uri'] == $ruri || ($fil['uri'] == '' && $ruri == 'index.html')) {
				$cla = ' cc';
			} else {
				$cla = '';
			}
			$cla = $meta['class'].$cla;
			if($cla != '') $cla = ' class="'.$cla.'"';
			
			$smenu ="";
			if($cls_cfg->numrows($mna) >= 1 && $vs == 1) {
				$smenu = '
				<ul class="'.$smc.'">';
				while($filu = $cls_cfg->fetch($mna, 'array')) {
					$metau = unserialize($filu['meta']);
					list($posi, $padi) = explode('.', $filu['sup']);
					if($posi != 0) {
						
						$mnlk = ZW_URL.$uril.$filu['uri'];
						if(preg_match('/http/', $filu['uri'])) {
							$mnlk = $filu['uri'];
						}
						if(preg_match('/#/', $filu['uri'])) {
							$mnlk = $filu['uri'];
						}
					$smenu.= N.T.T.T.T.'<li'.$cla.'><a href="'.$mnlk.'"'.$cla.'>'.$metau['label'].'</a></li>';
					}
				}
				$smenu.= N.T.T.T."</ul>";
			}
			if(isset($_GET['visual'])) {
				$link .='?visual='.$_GET['visual'];
			}
			if($pos != 0) {
					$menu .= N.T.T.'<li'.$cla.'><a href="'.$link.'"'.$cla.' '.$rel.' >'.htmlspecialchars($meta['label']).'</a>'.$smenu.'</li>';
			}
		}
		$menu.= '
	</ul>
	</nav>';
// 		if (!isset($m[''.$mnid])) {
// 			$cls_cfg->query("INSERT INTO ".TBLPRE."cnt_cfg (nom, idi, val) VALUES ('".$mnid."', '".$cfg['idi']."-m', ".fixSql($menu).")");
// 		}
// 		if ($cls_cfg->gcfg['tmp'] == 0 || isset($m[''.$mnid])) {
// 			$cls_cfg->query(sprintf("UPDATE %s SET val = %s WHERE idi LIKE %s AND nom LIKE %s", TBLPRE.'cnt_cfg', fixSql($menu),  fixSql($cfg['idi']."-m"), fixSql($mnid)));
// 		}

		//$this->saveTmp($mnid, $cls_cfg->idis.'-m', $menu);
		}

		return $menu;
	}

	public function &getSubMenu($mnid='subm', $supid=0) {
		global $gcfg, $ruri, $cfg, $req_uri, $cls_cfg;
		$sm = $cls_cfg->cfg($cls_cfg->idis.'-sm');
		if ($gcfg['tmp'] == 1 && isset($sm[''.$mnid.$supid.$cls_cfg->idis])){
			return $sm[''.$mnid.$supid.$cls_cfg->idis];
		} else {
			$menu = '';
			$rel = ' rel="nofollow"';
			//$mn = "SELECT id, sup, uri, idi, meta FROM ".TBLPRE."cnt WHERE sup LIKE '%.".$supid."' AND idi = '".$cfg['idi']."' OR idi = '00' AND sup LIKE '%.".$supid."' ORDER BY sup";
			$mn = "SELECT id, sup, uri, idi, meta FROM ".TBLPRE."cnt WHERE sup LIKE '%.".(int)$supid."' ORDER BY sup;";
			$mn = $cls_cfg->query($mn);
			$uril = '';
			if(in_array($this->fldr[0], $this->uril)) $uril = $this->fldr[0].'/';
			$menu .= '
	<!-- m i:'.$mnid.' -->
	<nav id="'.ucfirst($mnid).'">
	<ul id="'.$mnid.'">';
			while($fil = $cls_cfg->fetch($mn, 'array')) {
				list($pos, $pad) = explode('.', $fil['sup']);
				$filsupid = $fil['id'];
				$meta = unserialize($fil['meta']);
				$link = ZW_URL.$uril.$fil['uri'];
					
				if(preg_match('/http/', $fil['uri'])) {
					$link = $fil['uri'];
				}
				if(preg_match('/#/', $fil['uri'])) {
					$link = $fil['uri'];
				}
				if(!isset($meta['class']) || $meta['class'] == 'class') { $meta['class'] = ''; }
				if( $fil['uri'] == $ruri || ($fil['uri'] == '' && $ruri == 'index.html')) {
					$cla = ' cc';
				} else {
					$cla = '';
				}
				$cla = $meta['class'].$cla;
				if($cla != '') $cla = ' class="'.$cla.'"';
					
				if($pos != 0) {
					$menu .= N.T.T.'<li'.$cla.'><a href="'.$link.'"'.$cla.' '.$rel.' >'.htmlspecialchars($meta['label']).'</a></li>';
				}
			}
			$menu.= '
	</ul>
	</nav>';
	
			// 		if(!isset($sm[''.$mnid.$supid.$cfg['idi']])) {
			// 			$cls_cfg->query("INSERT INTO ".TBLPRE."cnt_cfg (nom, idi, val) VALUES ('".$mnid.$supid.$cfg['idi']."', '".$cfg['idi']."-sm', ".fixSql($menu).")");
			// 		}
			// 		if (isset($sm[''.$mnid.$supid.$cfg['idi']]) && $gcfg['tmp'] == 0) {
			// 			$cls_cfg->query(sprintf("UPDATE %s SET val = %s WHERE idi LIKE %s AND nom LIKE %s", TBLPRE.'cnt_cfg', fixSql($menu), fixSql($cfg['idi']."-sm") ,fixSql($mnid.$supid.$cfg['idi'])));
			// 		}
			//if (isset($mnid.$supid.$cfg['idi'])) {
			//$this->saveTmp($mnid, $cls_cfg->idis.'-m', $menu);
			//	$this->saveTmp($mnid, $cls_cfg->idis.'-sm', $menu);
			//}
				
				return $menu;
		}
	}
	
	/**
	 * 
	 * @param string $med small|medium|standard|tall
	 * @param string $con
	 * @param string $url
	 * @return string
	 */
	public function &plusOne($med='small', $con=true, $url = ZW_URL) {
		if ($this->idis == 'en') {
			$js = '';
		} else {
			$js = N.T.T.'window.___gcfg = {lang: \''.$this->idis.'\'};'.N.T;
		}
		$ds = 'data-size="'.$med.'" ';
		$plusone = '<div class="g-plusone" '.$ds.'data-href="'.$url.'"></div>'.N.T.
		'<script type="text/javascript">'.$js.'
			(function() {
			var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
			po.src = \'https://apis.google.com/js/plusone.js\';
			var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
			})();
		</script>'.N.T;
		$this->jo['plusone'] = true;
		return $plusone;
	}
	
	public function &fbLike($url = ZW_URL, $action= 'recommend') {
		global $cls_cfg;
		if (isset($cls_cfg->gcfg['fb-apid'])) {
			$appid = $cls_cfg->gcfg['fb-apid'];
		} else { $appid = ''; }
		$fblike = '<div class="fb-like" data-href="'.$url.'" data-layout="button_count" data-action="'.$action.'" data-show-faces="true" data-share="true"></div>';
		$bodystart = '<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId='.$appid.'";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';
		$i = 0; $i++;
		if (isset($this->jo['fblike']) && $this->jo['fblike'] == true || $i>0) {
			$this->bodystart = $bodystart;
		}
		$this->jo['fblike'] = true;
		return $fblike;
	}
	
	public function &tweet($url = ZW_URL){
		global $cls_cfg;
		$at = 'feelRiviera';
		if (isset($cls_cfg->gcfg['w2-twitter'])) {
			$at = $cls_cfg->gcfg['w2-twitter'];
		}
		$this->jo['tweet'] = true;
		/*
		 * <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://feelriviera/directory/" data-text="Check" data-via="micyaotl">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
*/

		$src =  '<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$url.'" data-dnt="true" data-count="none" data-via="'.$at.'">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		return $src;
	}
	
	public function &video($o) {
		$id = 'mXgLAcuy9Rc';
		$client = 'youtube';
		$wh = '420x315';
		$o = explode(' ', $o);
		//list($id, $client, $wh) = ;
		if (isset($o[0])) $id = $o[0];
		if (isset($o[1])) $client = $o[1];
		if (isset($o[2])) $wh = $o[2];
		if (isset($wh)) list($w, $h) = explode('x', $wh);
		$text = <<< EOD
<object width="$w" height="$h">
  <param name="movie" value="https://www.youtube.com/v/$id?version=3"></param>
  <param name="allowFullScreen" value="true"></param>
  <param name="allowScriptAccess" value="always"></param>
  <embed src="https://www.youtube.com/v/$id?version=3" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="$w" height="$h"></embed>
</object>i:$id c:$client w:$w h:$h
EOD;
		return $text;
	}

	private function &w2li($uri, $class, $title) {
		$w2li = '<li><a href="'.$uri.'" target="zwSocial" class="'.$class.'" rel="nofollow">'.$title.'</a></li>'.N.T;
		return $w2li;
	}

	public function &getSocial( $w2t=false, $bp = false ) {
		global $cfg, $gcfg;
//		$gcfg = $cls_cfg->gcfg;
		$w2 = '';
		if ($w2t != false) {
			$w2t = '';
		} else {
			if (isset($gcfg['finduson'])) {
				$w2t = '<h4 class="'.$cfg['idi'].'">'.$gcfg['finduson'].'</h4>';
			} else {
				$w2t = '<h4 class="'.$cfg['idi'].'">'.textoEnIdioma('[es]Siguenos en[/es][en]Follow us on[/en][it]Seguici su[/it][fr]Suivez-nous sur[/fr]').'</h4>';
			}
		}
	
		/*if(!isset($gcfg['w2fx'])) {
			$this->jsjq = N.T.T.'$("#w2 a").fadeTo("fast", 0.5);
			$("#w2 a").hover( function(){ $(this).fadeTo("fast", 1.0); }, function(){ $(this).fadeTo("fast", 0.5); });';
		}*/
		if(isset($gcfg['w2']) && $gcfg['w2'] == 1) {
			$w2 = N.T.T.'<div id="w2">'.N.T.T.$w2t.N.T.T.T.'<ul>'.N.T.T;
			if(isset($gcfg['w2-facebook']) && $gcfg['w2-facebook'] != '') {
				if(strstr($gcfg['w2-facebook'], 'http')) { $url = ''; } else { $url = 'http://www.facebook.com/pages/'; }
				$w2.= $this->w2li($url.$gcfg['w2-facebook'],'fb','Facebook');
			}
			if(isset($gcfg['w2-plus']) && $gcfg['w2-plus'] != '') {
				if(strstr($gcfg['w2-plus'], 'http')) { $url = ''; } else { $url = 'https://plus.google.com/b/'; }
				$w2.= $this->w2li($url.$gcfg['w2-plus'].'/','gp','+Google');
			}
			if(isset($gcfg['w2-tripadvisor']) && $gcfg['w2-tripadvisor'] != '') {
				if(strstr($gcfg['w2-tripadvisor'], 'http')) { $url = ''; } else { $url = 'http://www.tripadvisor.com/'; }
				$w2.= $this->w2li($url.$gcfg['w2-tripadvisor'].'/','ta','Trip Advisor');
			}
			if(isset($gcfg['w2-twitter']) && $gcfg['w2-twitter'] != '') { $w2.= $this->w2li('http://twitter.com/'.$gcfg['w2-twitter'], 'tw', 'Twitter');}
			if(isset($gcfg['w2-tumblr']) && $gcfg['w2-tumblr'] != '') { $w2.= $this->w2li($gcfg['w2-tumblr'],'tm','Tumblr'); }
			if(isset($gcfg['w2-flickr']) && $gcfg['w2-flickr'] != '') {
				if (strstr($gcfg['w2-flickr'], 'http')) {
					$w2.= $this->w2li($gcfg['w2-flickr'],'fl','Flickr');
				} else {
					$w2.= $this->w2li('http://www.flickr.com/photos/'.$gcfg['w2-flickr'],'fl','Flickr');
				}
			}
			if(isset($gcfg['w2-youtube']) && $gcfg['w2-youtube'] != '') { $w2.= $this->w2li($gcfg['w2-youtube'],'yt','YouTube'); }
			if(isset($gcfg['w2-linkedin']) && $gcfg['w2-linkedin'] != '') {
				if (strstr($gcfg['w2-linkedin'], 'http')) {
					$w2.= $this->w2li($gcfg['w2-linkedin'],'li','Linkedin'); 
				} else {
					$w2.= $this->w2li('http://www.linkedin.com/user/'.$gcfg['w2-linkedin'],'li','Linkedin'); 
				}
			}
			if(isset($gcfg['w2-friendfeed']) && $gcfg['w2-friendfeed'] != '') { $w2.= $this->w2li('http://www.linkedin.com/user/'.$gcfg['w2-friendfeed'],'ff','FriendFeed'); }
			if(isset($gcfg['w2-feed']) && $gcfg['w2-feed'] != '' ) { $w2.= $this->w2li($gcfg['w2-feed'],'fe','RSS feed'); }
			if (isset($gcfg['w2-dev'])) {
				if($gcfg['w2-dev'] == 'feelriviera') { $w2.= $this->w2li('http://feelriviera.com/','feelr','feelRiviera'); }
				if($gcfg['w2-dev'] == 'zihweb') { $w2.= $this->w2li('http://www.zihweb.com/','zihweb','ZihWeb'); }
			}
			if ($bp == true && $this->jo['bp'] != true) {
				$this->jo['bp'] = true;
				$bp = N.T.T.'<div id="pb"></div>';
			}
			$w2.= N.T.'</ul>'.$bp.N.T.T.'</div>'.N;
		}
		return $w2;
	}
	
	private function &getContactForm($var = null) {
		global $cls_cfg, $cls_eml, $gcfg, $cfg, $visd;
		if(isset($gcfg['visual']) && file_exists($cfg['dir'].$visd.$gcfg['visual'].'form.php')) {
			include $cfg['dir'].$visd.$gcfg['visual'].'form.php';
			if(isset($mailsended)) {
				$ret = $mailsended;
			} else {
				$ret = $contactform;
			}
		} else {
			$fnom = textoEnIdioma('[es]Nombre[/es][en]Name[/en][it]Nome[/it]');
			$femp = textoEnIdioma('[es]Empresa[/es][en]Company[/en][it]Azienda[/it]');
			$ftel = textoEnIdioma('[es]Tel&eacute;fono[/es][en]Phone[/en][it]Telefono[/it]');
			$fasu = textoEnIdioma('[es]Asunto[/es][en]Subject[/en][it]Soggetto[/it]');
			$ftm = textoEnIdioma('[es]Mensaje[/es][en]Message[/en][it]Messaggio[/it]');
			$fco = textoEnIdioma('[es]Comentario[/es][en]Comment[/en][it]Commento[/it]');
			$fcn = textoEnIdioma('[es]Consulta[/es][en]Consultation[/en][it]Consultazione[/it]');
			$fsu = textoEnIdioma('[es]Sugerencia[/es][en]Suggestion[/en][it]Suggerimento[/it]');
			$fenv = textoEnIdioma('[es]Enviar[/es][en]Send[/en][it]Spedire[/it]');
			
			if (isset($_POST['sendmailcontactform']) ) {
				include_once (ZW_DIR . 'sis'.Ds.'api'.Ds.'class.phpmailer.php');
			
				$mensajesend = '
	<a href="ZW_URL"><img src="'.ZW_URL.'webimg/logo.jpg" alt="'.$gcfg['title'].'"></a><br />
	<strong>'.$fnom.':</strong> '.$_POST['nom'].'<br />
	<strong>'.$femp.':</strong> '.$_POST['emp'].'<br />
	<strong>'.$ftel.':</strong> '.$_POST['tel'].'<br />
	'.$_POST['tipmsg'].':</strong>
	'.$_POST['com'].'<br />
<br />
 -- Datos adicionales<br />
   Mensaje enviado desde la web
   '.$gcfg['title'];
				$mensajesend = utf8_decode($mensajesend);
			
				$emlusr = $_POST['eml'];
				$nomusr = $_POST['nom'].' >> '.$gcfg['title'];

				$cls_eml->mail->IsSendmail();
			
				$cls_eml->mail->CharSet = "UTF-8";
				if ($cfg['idi'] != 'en') { $cls_eml->mail->SetLanguage ($cfg['idi']); }
				$cls_eml->mail->SetFrom($emlusr, $nomusr);
				$cls_eml->mail->Subject = $_POST['tipmsg'];
			
				$cls_eml->mail->AddAddress ($gcfg['mailto']);
			
				//$mail->AddReplyTo ($_POST['eml'], $_POST['nom']);
				$cls_eml->mail->From = $emlusr;
				$cls_eml->mail->FromName = $_POST['nom'].' <'.$_POST['eml'].'>';
			
				$cls_eml->mail->WordWrap = 80;
				$cls_eml->mail->AltBody = strip_tags($mensajesend);
				$cls_eml->mail->Body = $mensajesend;
			
				//$mail->MsgHTML($mensajesend);
				//$mail->IsHTML (true);
			
					if ( $cls_eml->mail->Send() ) {
					//$contactform = $mail->Send();
						$mailsended = '<div class="cnt">'.textoEnIdioma('[en]Your Message has been sent[/en][es]Su mensaje ha sido enviado[/es][it]Il tuo messaggio è stato inviato[/it]').'</div>';
						$ret = $mailsended;
					} else {
						$mailsended = '<div class="cnt">'.textoEnIdioma('[en]Your Message has NOT been sent[/en][es]Su mensaje NO ha sido enviado[/es][it]Il tuo messaggio NO è stato inviato[/it]').'</div>';
						$ret = $mailsended;
					}
				} else {
			
			
				$ret = <<< EOPAGE
			<script>
				//<![CDATA[
				jQ(function(){
					jQ('input[title]').each(function(){
							if (jQ(this).val() === '') { jQ(this).val(jQ(this).attr('title')); }
							jQ(this).focus(function(){
								if (jQ(this).val() === jQ(this).attr('title')) { jQ(this).val('').addClass('focused'); }
							});
							jQ(this).blur(function(){
								if (jQ(this).val() === '') { jQ(this).val(jQ(this).attr('title')).removeClass('focused'); }
							});
					});
				jQ('p.ctaBtn a')
			    .css({ 'backgroundPosition': '0 0' })
				.hover( function(){ jQ(this).stop().animate({ 'opacity': 0 }, 350); },
						function(){ jQ(this).stop().animate({ 'opacity': 1 }, 350); } );
				});
				function send(){ document.mailform.submit(); }
				//]]>
			</script>
		<form name="mailform" enctype="multipart/form-data" method="post" class="cform">
		<div style="float: left; position: relative; margin: 0; width:50%;">
		<input title="$fnom" maxlength="164" name="nom" size="20" type="text" /><br />
		<input title="$femp" maxlength="164" name="emp" size="20" type="text" /><br />
		<input title="$ftel" maxlength="164" name="tel" size="20" type="text" /><br />
		<input title="Email" maxlength="164" name="eml" size="20" type="text" /><br />
		<input title="$fasu" maxlength="164" name="tipmsg" size="20" type="text" /><br /><br />
		<p class="ctaBtn"><a onclick="send()">$fenv</a></p>
		</div>
		<div style="float: right; position: relative; margin: 0; width:50%; height:300px;">
		$ftm
		<textarea title="$ftm" cols="28" rows="10" name="com"></textarea><br style="clear:both;" />
		<input type="hidden" name="sendmailcontactform" />
		</div>
		</form>
EOPAGE;
			}
		}
		return $ret;
	}
	
	public function &getBannersMap($banners) {
		global $cls_cfg;
		$banner = str_replace('banners,', '', $banners);
		$banner = explode(',',$banner);
		$bana = 50;
		$banb = 0;
		$ret = N.T.'<map name="adsgd" id="adsgd">';
		foreach ($banner as $ban) {
			$ret .= N.T.T.'<area shape="rect" coords="0,'.$banb.',200,'.$bana.'" href="'.ZW_URL.$ban.'" target="_self" title="'.$ban.'" alt="'.$ban.'" />';
			$banb = $bana+5;
			$bana = $banb+50;
		}
		$ret .= '
		</map>
		<img src="'.ZW_URL.'webimg/ads/'.$banners.'.jpg" usemap="#adsgd">';
		return $ret;
	}
	
	public function &getReserveForm($tip='cnt') {
		global $gcfg, $cfg, $visd;
		if(file_exists($cfg['dir'].$visd.'resform.php')) {
			include_once $cfg['dir'].$visd.'resform.php';
			$form = getReserveForm($tip);
		} else {
			include_once ZW_DIR.'sis'.Ds.'api'.Ds.'contactforms.php';
			$form = getReserveForm($tip);
		}
		return $form;
	}
	
	public function &shareit($type='html',$url='',$style='default') {
		global $gcfg, $cfg;
		$libimg = $GLOBALS['lib'].'shrit';
		$title = $gcfg['title'];
		$url = ZW_URL;
		$txt = $this->textoEnIdioma('[es]Compartir[/es][en]Share[/en]');
		
		$jsjq = <<< EOPAGE
		jQ('a[rel=shareit], #shareit-box').mouseenter(function() {
		
		var height = jQ(this).height();
		var top = jQ(this).offset().top;
		var left = jQ('#shareit').offset().left / 2 + (jQ(this).width() /2) - (jQ('#shareit-box').width() / 2);
		var value = jQ(this).attr('name').split('|');
		var field = value[0];
		var url = encodeURIComponent(value[0]);
		var title = encodeURIComponent(value[1]);
		
		jQ('#shareit-header').height(height);
		jQ('#shareit-box').show();
		jQ('#shareit-box').css({'top':top, 'left':left});
		jQ('#shareit-field').val(field);
		jQ('a.shareit-sm').attr('target','_blank');
		
		jQ('a[rel=shareit-mail]').attr('href', 'mailto:?subject=' + title);
		jQ('a[rel=shareit-twitter]').attr('href', 'http://twitter.com/home?status=' + title + '%20-%20' + url);
		jQ('a[rel=shareit-facebook]').attr('href', 'https://www.facebook.com/sharer.php?u=' + url + '&amp;t=' + title);
		jQ('a[rel=shareit-delicious]').attr('href', 'http://del.icio.us/post?v=4&amp;noui&amp;jump=close&amp;url=' + url + '&title=' + title);
		jQ('a[rel=shareit-reddit]').attr('href', 'http://reddit.com/submit.php?url='  + url + '&amp;title=' + title);
		jQ('a[rel=shareit-digg]').attr('href', 'http://digg.com/submit?phase=2&amp;url=' + url + '&amp;title=' + title);
		jQ('a[rel=shareit-stumbleupon]').attr('href', 'http://www.stumbleupon.com/submit?url=' + url + '&title=' + title);
		
	});
	jQ('#shareit-box').mouseleave(function () {
		jQ('#shareit-field').val('');
		jQ(this).hide();
	});
	jQ('#shareit-field').click(function () {
		jQ(this).select();
	});
EOPAGE;
		
		$css = <<< EOPAGE
		#shareit {  }
		#shareit-box { position:absolute; display:none;border: none; box-shadow: none; }
		#shareit-box img{box-shadow:none;border: none;margin:0;}
		#shareit-box ul li{font:1/1em normal;}
		#shareit-header { width:138px; }
		#shareit-body { width:138px; height:100px; background:url($libimg/shareit.png); }

		#shareit-blank { height:20px; }

		#shareit-url { height:40px; text-align:center; }
		#shareit-url input.field{
			width:100px; height:26px;
				background: transparent url($libimg/field.gif) no-repeat;
				border:none; outline:none;
				padding:7px 5px 0 5px;
				margin:3px auto;font-size:11px;
			}

		#shareit-icon  { height:20px; }
		#shareit-icon ul { list-style:none; width:130px; margin:0; padding:0 0 0 8px; }
		#shareit-icon ul  li{ float:left; padding:0 2px; }
			
			#shareit-icon ul  li img{
				border:none;
			}			
EOPAGE;

			$html = <<< EOPAGE
			<a rel="shareit" href="#" name="$url|$title">$txt</a>
<div id="shareit-box">
	<div id="shareit-header"></div>
	<div id="shareit-body">
		<div id="shareit-blank"></div>
		<div id="shareit-url"><input type="text" value="" name="shareit-field" id="shareit-field" class="field"/></div>
		<div id="shareit-icon">
		<ul>
			<li><a href="#" rel="shareit-mail" class="shareit-sm"><img src="$libimg/sm_email.png" width="16" height="16" alt="Mail" title="Mail" /></a></li>
			<li><a href="#" rel="shareit-delicious" class="shareit-sm"><img src="$libimg/sm_delicious.gif" width="16" height="16" alt="Delicious" title="Delicious" /></a></li>
			<li><a href="#" rel="shareit-facebook" class="shareit-sm"><img src="$libimg/sm_facebook.png" width="16" height="16" alt="facebook" title="facebook" /></a></li>
			<li><a href="#" rel="shareit-digg" class="shareit-sm"><img src="$libimg/sm_digg.gif" width="16" height="16" alt="Digg" title="Digg" /></a></li>
			<li><a href="#" rel="shareit-stumbleupon" class="shareit-sm"><img src="$libimg/sm_stumbleupon.gif" width="16" height="16" alt="StumbleUpon" title="StumbleUpon" /></a></li>
			<li><a href="#" rel="shareit-twitter" class="shareit-sm"><img src="$libimg/sm_twitter.gif" width="16" height="16" alt="Twitter" title="Twitter" /></a></li>
		</ul>
		</div>
	</div>
</div>
EOPAGE;
		if($type == 'html') $this->jo['shareit'] = true;
		return $$type;
	}
}
$cls_cnt = new Content();

/**
 * OBSOLETAS
 * funciones a descontinuar/eliminar
 */
function remTxt($txt) {
	global $cls_cnt;
	return $cls_cnt->remTxt($txt);
}
function getHead($tip='pag') {
	global $cls_cnt, $url, $lib, $visd, $visual, $pag_id, $cls_cfg, $js,$jsjq,$css;
	return $cls_cnt->getHead($tip);
}
function getContent($tip) {
	global $cls_cnt, $fldr, $cls_cfg, $ext_cls;
	return $cls_cnt->getContent($tip);
}

function getContactForm() {
	global $cls_cnt;
	return $cls_cnt->getContactForm();
}

function getMenu($mnid='nvg', $vs=0, $smc='sub') {
	global $cls_cnt, $gcfg, $ruri, $cfg, $req_uri, $cls_cfg;
	return $cls_cnt->getMenu($mnid, $vs, $smc);
}

function getSocial( $w2t=false, $bp=false ) {
	global $cls_cnt;
	return $cls_cnt->getSocial($w2t, $bp);
}

function seccion() {
	global $ruri;
	if(strstr( $ruri, '.html') or (strpos('/', $ruri)==0) ) {
		$suri = str_replace(".html", '', $ruri);
		echo ' '.$suri; 
	} else {
		echo ' index';
	} 
}

function seccvar() {
	global $ruri;
	if(strstr( $ruri, '.html') or (strstr( $ruri, '/') && strpos('/', $ruri, 1)==0) ) {
		$suri = str_replace(".html", '', $ruri);
		$secc = $suri; 
	} else {
		$secc = 'index';
	}
	return $secc;
}