<?php
class Dynamics {
	var $xml;
	var $js;
	var $css;
	var $cssfonts;
	
	public function __construct() {
		global $cls_cfg;
		if ($cls_cfg->ruri == 'dyn.zw') {
			$this->getDynamics();
			exit;
		}
	}
	
	public function getDynamics() {
		global $cls_cfg;
		$xml = $cls_cfg->req_uri;
		$xml = explode('dyn.zw?', $xml);
		$xmlf = $xml[1];
		if (strstr($xmlf, '&')) {
			$xmlp = explode('&', $xmlf);
			$xmlf = $xmlp[0];
		}
		$cls_cfg->sendHeaders($ext='html', $h='200');
		/*if (function_exists($xmlf) && count($xmlp) > 0) {
			if (isset($xmlp[2])) {
				echo $this->$xmlf($xmlp[1], $xmlp[2]);
			} else {
				echo $this->$xmlf($xmlp[1]);
			}
		} else {*/
		if (!isset($xmlp[2])) {
			$xmlp[3] = $xmlp[2] = '';
		}
		echo $this->$xmlf($xmlp[1],$xmlp[2],$xmlp[3]);
		//}
	}
	
	private function curly($url) {
		global $cls_cfg;
		$jsc = curl_init();
		curl_setopt($jsc, CURLOPT_URL,$url);
		curl_setopt($jsc, CURLOPT_HEADER, false);
		curl_setopt($jsc, CURLOPT_USERAGENT, $cls_cfg->agent);
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
	
	public function javascript() {
		global $cls_cfg, $local;
		$gcfg = $cls_cfg->gcfg;
		$jqp = explode(',', $cls_cfg->gcfg['jqp']);
		$script = '/* ZihWeb CMS JavaScript */';
		if ($local == true) {
			$html5 = file_get_contents(ZW_DIR.'lib'.Ds.'html5.js');
			$jquery = file_get_contents(ZW_DIR.'lib'.Ds.'jquery.js'); 
			$jqueryui = file_get_contents(ZW_DIR.'lib'.Ds.'jquery.ui.js');
		} else {
			$html5 = $this->curly('https://html5shiv.googlecode.com/svn/trunk/html5.js');
			$jquery = $this->curly('https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
			$jqueryui = $this->curly('https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');
		}
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
			$script .= $html5;
		}
		$script .= file_get_contents(ZW_DIR.'lib'.Ds.'swfobject.js');
		$script .= N.N.'/** jQuery **/'.N;
		$script .= $jquery;
		
		/*if(isset($gcfg['w2']) && $gcfg['w2'] == 1 && !isset($gcfg['w2fx'])) {
			$script .= N.T.'$("document #w2 a").fadeTo("fast", 0.5);
		$("document #w2 a").hover( function(){ $(this).fadeTo("fast", 1.0); }, function(){ $(this).fadeTo("fast", 0.5); });'.N;
		}*/
		foreach($jqp as $jp) {
			//if($jp != '') { // && file_exists(ZW_DIR.'lib'.Ds.$jp.'.js')
				$script .= N.N.'/** '.ucwords($jp).' **/'.N.N;
				/*if ($jp == 'ui') {
					$script .= $jqueryui;
				} else {*/
					$script .= file_get_contents(ZW_DIR.'lib'.Ds.'jquery.'.$jp.'.js');
				//}
			//}
		/*
		if($jp == 'pngfix') {
						$script .= N.N.'$(function(){$(document).pngFix();});';
					}
					if ($jp == 'lazyload') {
						$script .= N.N.'$("img").lazyload({effect : "fadeIn",threshold : 200});';
					}
					if($jp == 'pph') {
						if (isset($conf['pph'])) {
						$script .= N.N.'$("a[rel^=\'pph\']").prettyPhoto({theme: \''.$conf['pph'].'\', hideflash: true});';
						} else {
						$script .= N.N.'$("a[rel^=\'pph\']").prettyPhoto({theme: \'dark_square\', hideflash: true});';
						}
					}
					
					if ($jp == 'uploadify') {
						$script .= N.N."
			$('#uploads').uploadify({
				'uploader'		: ''.$uldfil.'.swf',
				'script'		: ''.$uldfil.'.php',
				'cancelImg'		: ''.$cancel.'.png',
				'folder'		: '/img/properties/'.$editid.'',
				'queueID'		: 'fileQueue',
				'fileExt'		: '*.jpg;*.jpeg;*.gif;*.png;*.txt',
				'auto'			: false,
				'multi'			: true,
				//'queueSizeLimit': 0,
				'buttonText'	: 'Buscar Archivos'
			});
";
					}
					
					if ($jp == 'jfeed') {
					if ($cls_cnt->jo['bp-jq'] != true && (isset($gcfg['feed']) || $gcfg['feed'] != '')) {
			if(isset($gcfg['feed-title']) && $gcfg['feed-title'] != '') {
				$feedtit = $gcfg['feed-title'];
			} else {
				$feedtit = textoEnIdioma('[es]En Nuestro Blog[/es][en]In Our Blog[/en]');
			}
				if (isset($gcfg['feed-script']) && $gcfg['feed-script'] != '') {
					$script .= $gcfg['feed-script'];
				} else {
					$script .= N.T."$.getFeed({
			url: '".ZW_URL."feedproxy/".$gcfg['feed']."',
			success: function(feed) {
				var html = '<h4>".$feedtit."</h4>';
				for(var i = 0; i < feed.items.length && i < 1; i++) {
					var item = feed.items[i];
					html += '<div class=\"feedItem\">';
					html += '<a href=\"".$gcfg['feed']."\" class=\"pbrss\">feed rss</a> <span id=\"pbpt\">'+item.title+'</span>';
					html += '<p>'+item.description+' </p></div>';
					html += '<a href=\"'+item.link+'\">".textoEnIdioma('[es]leer mas[/es][en]read more[/en]')." &raquo;</a>';
				}
				$('#pb').append(html).toggle();
				$('#pb').slideDown('slow');
				$('#pb img').css({width: '100%'});
			}
		});";
			$cls_cnt->jo['bp-jq'] = true;
			}
		}
					}*/
		
		}
	$cls_cfg->sendHeaders('js','200');
	//$p = array("\r",N,T);
	//$r = array('','','');
	//$script = str_replace($p, $r, $script);
	
	echo $script;
	exit;
	}
	
	public function styles() {
		global $cls_cfg,$visd,$lib, $cssg, $csslab;
		$gcfg = $cls_cfg->gcfg;
		$jqp = explode(',', $gcfg['jqp']);
		$script = '';
		$cssg .= file_get_contents(ZW_DIR.'lib'.Ds.'g.css');
	
		if ($cls_cfg->admin = true || esClient()) {
			$csslab .= '#lab { position:fixed;top:2px;right:2px;z-index:1000; }';
		}
		$cssvisual = file_get_contents($cls_cfg->cfg('dir').$visd.'style.css');
		//$fsp = "/\@import url\(http\:\/\/fonts\.googleapis\.com\/css\?family\=(.+?)\)\;/i";
		//"/\@import url\(http\:\/\/fonts\.googleapis\.com\/css\?family\=(.*)\)\;/"
		//$cssfonts = preg_match_all($fsp, $cssvisual, $cssfonts);
		/*ob_start();
		print_r($cssfonts);
		$cssfonts = ob_get_contents();
		ob_end_clean();
		*//*
		$cssfonts = preg_replace_callback($fsp, create_function('$coi',
		'
		$f = preg_replace("/\@import url\(http\:\/\/fonts\.googleapis\.com\/css\?family\=/sU","", $coi[0]);
		$f = preg_replace("/\)\;/sU","",$f);
		$f = explode("|", $f);
		foreach($f as $sd) {
			$cssfonts = "@import url(http://fonts.googleapis.com/css?family=$sd);";
		}
		$cssfonts = $coi[1];
		'), $cssvisual);*/
		
		/*$cssfonts = preg_split($fsp, $cssvisual);
		foreach($cssfonts as $sd) {
			$cssfonts .= "@import url(http://fonts.googleapis.com/css?family=$sd);".N;
		}*/
		$p = array('url(', 'url('.ZW_URL.$visd.'http://');
		$r = array('url('.ZW_URL.$visd, 'url(http://');
		$cssvisual = str_replace($p, $r, $cssvisual);
		//$cssvisual = preg_replace("/\@import url\(http\:\/\/fonts\.googleapis\.com\/css\?family\=(.+?)\)\;/sU","", $cssvisual);

		$jqcss = '';
		foreach($jqp as $jp) {
			if ($jp == 'ui' || $jp == 'ui.redmond') {
				//$script .= file_get_contents(ZW_DIR.'lib'.Ds.'themes/base/jquery.ui.all.css');
				if ($jp == 'ui.redmond') {
					$jqtheme = 'redmond';
				} else {
					$jqtheme = 'smoothness';
				}
				$jqui  = file_get_contents(ZW_DIR.'lib'.Ds.$jqtheme.Ds.'jquery-ui-1.9.2.custom.min.css');
				//$jqui  .= file_get_contents(ZW_DIR.'lib'.Ds.'themes'.Ds.'base'.Ds.'jquery.ui.theme.css');
				$p = array('url("jquery.ui',
				'url(images/',
				'/ui-icons_222222_256x240.png');
				$r = array('url("themes/base/jquery.ui',
				'url('.$lib.$jqtheme.'/images/',
				'/ui-icons_2e83ff_256x240.png');
				$jqcss .= str_replace($p, $r, $jqui);
			} else if(file_exists(ZW_DIR.'lib'.Ds.$jp.'.css')) {
				$jqcss .= file_get_contents(ZW_DIR.'lib'.Ds.$jp.'.css');
			}
		}
		//$script .= '/*VISUAL*/'.N.$cssvisual;
		$script .= '/*GLOBAL*/'.N.$cssg;
		$script .= N.'/*JQ*/'.N.$jqcss;
		$script .= N.'/*LAB*/'.N.$csslab;
		
		$cls_cfg->sendHeaders('css','200');
		echo $script;
		exit;
		
	}
	/*
	 * listar imagenes de un directorio especificado como $gal
	 * $gal string directorio a listar
	 */
	private function imglst($gal) {
		global $gcfg, $cfg;
		$gal = str_replace('_', Ds, $gal);
		$dir = ZW_DIRC.$gal;
		$full_path = $dir;
			$images = array();
			$d2 = dir($full_path);
			while (false !== ($image = $d2->read())) {
				//if (eregi('.jpg|.gif|.png|.swf|.flv', $image)) {
				$match = '/\.jpg|.gif|.png|.swf|.flv\//';
				if(preg_match($match, $image)) {
					$images[] = $image;
				} 
			}
		return $images;
			$d2->close();
	}
	
	public function slide($gal='img', $wxh = '400x500') {
		$images = $this->imglst($gal);
		$server = ZW_URL;
		//$wxh = $wxh.'.';
		$rel_path = 'webimg/'.$wxh.'.';
		$path = $server. $rel_path;
		$o = '';
		if ($gal == 'img') {
			$album  = str_replace('img', '', $gal);
		} else {
			//$album  = str_replace('%5C', Ds,$gal).'_';
			$album  = str_replace('img_', '',$gal).'_';
			$album  = str_replace(Ds, '_',$album);
		}
		if (!empty($images)) {
			$o = '<div id="slide">'.N.T.T;
				//natcasesort($images);
				$gal  = str_replace('img_', '', $gal);
				$gal = str_replace(Ds, '_', $gal);
				foreach($images as $i) {
					$link = $caption = $title = '';
					//$title = ucwords($gal); //preg_replace('/\/|_|-|img/', ' ', $gal)
					$title = ucwords(preg_replace('/\/|_|-|img\//', ' ', $gal));
					$d = ucwords(preg_replace('/img\//', ' ', $gal));
					//$link = $server.'webimg/'.$phs.'.'.$album. $i;
					$o .= N.T.T.'<img src="' . $path .$album. $i . '" title="'.$title.'" alt="'.$title.'" />';
			}
			$o .= '</div>';
		}
		return $o;
	}
	
	public function thumbs($gal='img', $wxh = '50x50', $phs ='n', $lmt='0') {
		global $gcfg, $cfg;
		$gal = str_replace('_', '/', $gal);
		$wxh = $wxh.'.';
		$dir = ZW_DIRC.$gal;
		$server = ZW_URL;
		$rel_path = 'webimg/'.$wxh;
		$path = $server. $rel_path;
		$o = '';
		if ($gal == 'img') {
			$album  = str_replace('img', '', $gal);
		} else {
			//$album  = str_replace('%5C', Ds,$gal).'_';
			$album  = str_replace('img/', '',$gal).'_';
			$album  = str_replace('/', '_',$album);
		}
		$full_path = $dir;
			$images = array();
			$d2 = dir($full_path);
			while (false !== ($image = $d2->read())) {
				//if (eregi('.jpg|.gif|.png|.swf|.flv', $image)) {
				$match = '/\.jpg|.gif|.png|.swf|.flv\//';
				if(preg_match($match, $image)) {
					$images[] = $image;
				} 
			}
			$d2->close();
			if (!empty($images)) {
				natcasesort($images);
				$gal = str_replace(Ds, '_', $gal);
				$o .= '<ul class="tmbl">';
				$in = 1;
				foreach($images as $i) {
					$in++;
					$link = $caption = $title = '';
					$title = ucwords(preg_replace('/\/|_|-|img/', ' ', $gal));
					$d = ucwords(str_replace('img', '', $gal));
					$link = $server.'webimg/'.$phs.'.'.$album. $i;
					if ($lmt == 0 || $in <= $lmt+1) {
						//a title="'.$i.'"  img title="'.$d.'"
					$o .= N.T.T.'<li class="tmbl">
			<a href="'.$link.'" rel="pph[\''.$gal.'\']" class="tmb"> 
				<img src="' . $path .$album. $i . '" class="lz" alt="'.$title.'" />
			</a>
		</li>';
					}
				}
				$o .= '</ul>';
			}
		return $o;
	}
	
	public function listdir ($dir='img') {
		$pth = ZW_DIRC.$dir;
		$d2 = dir($pth);
		$ld = $pd = $dir;
		//while (false !== ($dir = $d2->read())) {
		while (false !== ($dir = $d2->read())) {
			//if (eregi('..|.', $dir)) {
			if (!is_dir($dir)) {
				$o[] = $dir;
			}
		}
		$d2->close();
		if ($ld == 'img') {
			$ld  = str_replace('img', '', $ld);
		} else {
			//$album  = str_replace('%5C', Ds,$gal).'_';
			$ld  = str_replace('img/', '',$ld).'_';
		}
		if (!empty($o)) {
			natcasesort($o);
			//$dir = str_replace('/', '_', $dir);
			$r = N.T.T.T.'';
			foreach($o as $i) {
				$fp = $pth.Ds.$i;
				$ft = filetype($fp);
				$fz = filesize($fp);
				$server = ZW_URL;
				/*$link = $caption = $title = '';
				$link = $server.'webimg/'.$phs.'.'.$album. $i;*/
				$title = ucwords(preg_replace('/_|-/', ' ', $gal));
				$a = $af = '';
				if (is_dir($fp)) {
					$a = '<a href="'.ZW_URL.'dyn.zw?listdir&'.$pd.'/'.$i.'" class=""><img src="' . $server.'img/icons/button-yellow.png" alt="" />';
					$af = '</a>';
				}
				if (eregi('.jpg|.gif|.png', $fp)) {
					//$ad = str_replace('img/', '', $ld);
					//$ld = str_replace('img/', '', $ld);
					//$ld = str_replace('/', '_', $ld);
					$a = '<a href="'.ZW_URL.'webimg/n.'.str_replace('img', '', $ld).$i.'" class="">';
					$rel_path = 'webimg/t.';
					$path = $server.$rel_path;
					$a = $a.'<img src="' . $path .$ld. $i . '" alt="'.$title.'" />';
				}
				$r .= N.T.T.T.T.''.$a.$i.$af.''; 
			}
			$r .= N.T.T.T.'';
		}
		return $r;
	}
	public function mkdir($dir) {
		$tdir = ZW_DIRC.'img'.Ds.$dir;
		if(!file_exists($tdir) && esAdmin()) mkdir($tdir, 0777, true);
	}
	public function unlink($dir) {
		$tdir = ZW_DIRC.'img'.Ds.$dir;
		if(!file_exists($tdir) && esAdmin()) unlink($tdir);
	}
	public function girimg($img, $rad) {
		//$img = str_replace('_', Ds, $img);
		$imo = ZW_DIRC.$img;
		if(file_exists($imo) && esAdmin()) {
			imagerotate(imagecreatefromjpeg($imo), $rad, 0);
		}
	}
	public function suggest ($q) {
		global $zihweb, $cls_cfg, $cls_cnt;
		$q = explode('=', $q);
		$q = $q['1'];
		$results = '';
		$sql1 = <<< EOD
SELECT cid, title, cid5, lid FROM dir_neg WHERE title LIKE "%$q%" AND status > 0
UNION ALL
SELECT cid, title, pid, nom_my FROM dir_cat WHERE title LIKE "%$q%";
EOD;
		$sql1 = $cls_cfg->query($sql1);
		
		$ct = $cls_cfg->numrows($sql1);
		$c = 0;
		while($fila = $cls_cfg->fetch($sql1, 'array')) {
			$c++;
			$comma = ',';
			if ($c == $ct) { $comma = ''; }
			$title = $cls_cnt->textoEnIdioma($fila['title']);
			$urly = $zihweb->fixUri($title);
			$title = json_encode($title);
			if(isset($fila['lid']) && $fila['lid'] != '') {
				$lid = $fila['lid'];
				$url = $fila['lid'].'~'.$urly;
			} else {
				$lid = $fila['cid'];
				$url = 'cat'.$lid.'-'.$urly;
			}
			$results .= <<< EOD
		{ "value": $title, "data": {"cat": "{$fila['cid']}", "url": "{$url}" } }$comma

EOD;
		}
		//$results = json_encode($cls_cfg->fetch($sql, 'array')); {"value":"", "data":""}
		$ret = <<< EOD
{
	"query": "$q",
	"suggestions": [
$results		]
}
EOD;
		$cls_cfg->sendHeaders('txt', 200);
		return $ret;
	}
	private function delLnk($id) {
		return '<a href="'.ZW_URL.ZW_ADM.'?borcnt='.$id.'" class="del ui-state-default ui-corner-all ui-icon ui-icon-trash fltRgt">x</a>';
	}
	public function cntlst($tip) {
		global $cls_cfg, $cls_adm;
		if ($tip == 'src') {
			$sql = "SELECT id, sup, uri, idi, meta, tit FROM ".TBLPRE."cnt WHERE
			uri NOT LIKE '%.html' AND uri NOT LIKE '%/' AND uri NOT LIKE '#' ORDER BY idi, sup, uri
			#lstsrc";
			$tiped = '&editor=false';
		} elseif ($tip == 'all') {
			$sql = "SELECT id, sup, uri, idi, meta, tit FROM ".TBLPRE."cnt ORDER BY idi, sup, uri
			#lstall";
			$tiped = '';
		} else {
			$sql = "SELECT id, sup, uri, idi, meta, tit FROM ".TBLPRE."cnt WHERE
			sup LIKE '%.0' AND uri LIKE '%.html' OR
			sup LIKE '%.0' AND uri LIKE '%/' OR
			sup LIKE '%.0' AND uri LIKE '#'
			ORDER BY idi, sup, uri
			#lstpag";
			$tiped = '';
		}
		
		$sql = $cls_cfg->query($sql);
		//$fila = $cls_cfg->fetch($sql, 'array');
		$listedit = '<ul class="cntlst">';
		while($fila = $cls_cfg->fetch($sql, 'array')) {
			$meta = unserialize($fila['meta']);
			// lastmod index
			if (!isset($meta['lastmod'])) $meta['lastmod'] = $cls_cfg->lastmod();
			
			//if($fila['uri'] == 'thankyou.html' && $master != 1 ) { echo '<!--'; }
	
			$listedit.= N.T.'<li draggable="true">'.$fila['idi'].' | <a href="'.ZW_URL.$fila['uri'].'">ver</a> | ';
			// borrar contenido
			if(WASA==1 && _UID == 1) { $listedit.= $this->delLnk($fila['id']); }
			$listedit.= '<a href="'.ZW_URL.ZW_ADM.'?edita='.$fila['id'].$tiped.'">'.$meta['label'].' - <strong>'.$fila['tit'].'</strong> <em class="fltRgt">'.$fila['uri'].'</em>
			<br />'.$meta['lastmod'].'</a>';
	
			list($pos, $pad) = explode('.', $fila['sup']);
			$filsupid = $fila['id'];
		$sqla = "SELECT id, sup, uri, idi, meta, tit FROM ".TBLPRE."cnt WHERE
		sup LIKE '%.".$filsupid."' AND uri LIKE '%.html' OR
		sup LIKE '%.".$filsupid."' AND uri LIKE '%/' ORDER BY idi, sup, uri
		#lstint";
			$sqla = $cls_cfg->query($sqla);
			if ($cls_cfg->numrows($sqla) == 0) {
				$listedit.= '</li>';
			}  else {
				$listedit.= '
				<ul>';
				while($filu = $cls_cfg->fetch($sqla, 'array')) {
					$meta = unserialize($filu['meta']);
					// lastmod index
			if (!isset($meta['lastmod'])) $meta['lastmod'] = $cls_cfg->lastmod();
					$listedit.='
					<li>'.$filu['idi'].' | <a href="'.ZW_URL.$filu['uri'].'">ver</a> | ';
					if(WASA == 1 && _UID == 1) {
						$listedit.= $this->delLnk($filu['id']);//'<a href="'.ZW_URL.ZW_ADM.'?borcnt='.$filu['id'].'" class="del ui-state-default ui-corner-all ui-icon ui-icon-trash fltRgt">x</a>';
					}
					$listedit.= '<a href="'.ZW_URL.ZW_ADM.'?edita='.$filu['id'].'">'.$meta['label'].' - <strong>'.$filu['tit'].'</strong> <em class="fltRgt">'.$filu['uri'].'</em>
					<br />'.$meta['lastmod'].'</a>';
				}
				$listedit.= '
				</li>
			</ul>';
			}
			//if($fila['uri'] == 'thankyou.html' && $master != 1 ) { echo '-->'; }
		}
		$listedit.= '
		</ul>';
		return $listedit;
	}
}
$cls_dyn = new Dynamics();
/*
function img($gal='img', $wxh = '50x50') {
	global $cls_dyn;
	$cls_dyn->img($gal, $wxh);
}*/
