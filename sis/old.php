
function remTxt($txt) {
	global $url, $lib, $gcfg;
	/**
	 * Reemplazo txt
	 * @var unknown_type
	 */
	$pat = array(
		"/\[lib\]/sU",
		"/\[site\]/sU",
		"/\[yyyy\]/sU",
		"/\[webdev\]/sU",
		"/\[madein\]/sU",
		"/\[todotulum\]/sU",
		"/\[imgurl\]/sU",
		"/\[siteurl\]/sU",
		"/\[contacto\]/sU",
		"/\[webimg\]/sU",
		"/\[contactform\]/sU",
		"/\[mapkey\]/sU",
		"/\[sitemap\]/sU",
	);
	
	if(isset($gcfg['mapkey'])) { $mapkey = $gcfg['mapkey']; } else { $mapkey = ''; }  
	$rem = array(
		$lib,
		'<a href="'.$url.'">'.$gcfg['title'].'</a>',
		date('Y'),
		'<a href="http://feelriviera.com/" id="devfr" title="feelRiviera Web Development" >feelRiviera</a>',
		'<a href="http://www.todotulum.com/" id="ltt">Made in Tulum</a>',
		'http://www.todotulum.com/',
		$url.'images/',
		$url,
		'[en]contact[/en][es]contacto[/es][fr]contact[/fr][de]kontakt[/de][it]contatto[/it][ja]コンタック[/ja][zh]联络[/zh]',
		$url.'webimg/',
		getContactForm(),
		$mapkey,
		getMenu('sitemap', 1, 'mnsb'),

	);
	/*if($gcfg['twitter'] != '') {
		$pat[] = "/\[twitter\]/sU";
		$rem[] = pagTwitter();
	}
	*/
	$ret = preg_replace($pat, $rem, $txt);
	//preg_replace_callback('|\[swf\](.+?),\s*(\d+)\s*,\s*(\d+)\s*(,(.+?))?\[/swf\]|i', 'wpswfObject', $text);
	$ret = preg_replace_callback('|\[lista\](.+?)\[/lista\]|i', 'lista', $ret);
	$ret = preg_replace_callback('|\[ext\](.+?)\[/ext\]|i', 'extension', $ret);
	$ret = textoEnIdioma($ret);
	return $ret;
}


function buscador() {
	global $idi;
	echo '<br />
	<form action="http://feelriviera.com/" id="cse-search-box">
	<div style="text-align:center;">
		<input type="hidden" name="z" value="'.$idi['uri'][9].'" />
		<input type="hidden" name="cof" value="FORID:10" />
		<input type="hidden" name="ie" value="UTF-8" />
		<input type="text" name="q" size="20" />
	</div>
	<div style="padding-left:15px">
	<strong>'.textoEnIdioma('[es]Buscar e[/es][en]Search i[/en]n:').'</strong><br />
  	&nbsp;&nbsp;<input type="radio" name="cx" value="003354711674453649159:bxzvh9bcaba" checked="checked" /> feelRiviera.com<br />
    &nbsp;&nbsp;<input type="radio" name="cx" value="003354711674453649159:9r5rbslv2ke" /> Internet<br />
    </div>
	<div style="text-align:center;">
    	<input type="submit" name="sa" value="'.$idi['uri'][9].'" />
	</div>
	</form>';
}

function secReq() {
	global $req_uri;
	$ret = explode('/', $req_uri);
	$ret = serialize($ret);
	return $ret;
}

/**
 * Generar el menu principal de navegacion
 * @param $mnid id css del menu
 * @param $vs ver sub menus, default no
 * @return string html
 */
function getMenu($mnid='nvg', $vs=0, $smc='sub') {
	global $gcfg, $ruri, $cfg, $req_uri;
	$menu = '';
	if($ruri == 'index.html' || $req_uri == '') {
		$rel = '';
	} else {
		$rel = ' rel="nofollow"';
	}

	if(isset($gcfg['menu2']) && $gcfg['menu2'] != '') {
		$menu2 = $gcfg['menu2'];
		$menu2 = split("\n", $menu2, -1);
		$menu.= '<!-- m2 -->
	<nav>
	<ul id="'.$mnid.'">';
		foreach($menu2 as $link) {
			list($uri, $tit, $cla) = explode("|", $link);

		if( $uri == $ruri || ($uri == '' && $ruri == 'index.html')) {
			$cla = $cla.' cc';
		}
		$cla = ' class="'.$cla.'"';
		$cla = str_replace("\r", "", $cla);
		$cla = str_replace("\n", "", $cla);
		if(strstr($uri, 'http://')) {
			$link = $uri;
			$tgt = ' target="_blank" ';
		} else {
			$link = ZW_URL.$uri;
			$tgt = '';
		}
			$menu.= '
		<li'.$cla.'><a href="'.$link.'"'.$cla.$rel.$tgt.'>'.$tit.'</a></li>';
		}
		$menu.= '
	</ul>
	</nav>';
	} elseif(isset($gcfg['menu']) && $gcfg['menu'] != '') {
		$menu = '<!-- m1 -->'.$gcfg['menu'];
	} else {
		/**
		AUTO MENU
		*/
		$mn = "SELECT id, sup, uri, idi, meta FROM ".TBLPRE."cnt WHERE sup LIKE '%.0' AND idi = '".$cfg['idi']."' OR idi = '00' AND sup LIKE '%.0' ORDER BY sup";
		$mn = mysql_query($mn);
		$menu .= '
	<!-- m i:'.$mnid.' -->
	<nav id="'.ucfirst($mnid).'">
	<ul id="'.$mnid.'">';
		while($fil = mysql_fetch_array($mn)) {
			list($pos, $pad) = explode('.', $fil['sup']);
			$filsupid = $fil['id'];
			$meta = unserialize($fil['meta']);
			$mna = "SELECT id, sup, uri, idi, meta FROM ".TBLPRE."cnt WHERE sup LIKE '%.".$filsupid."' AND uri NOT LIKE '%.xml' AND idi = '".$cfg['idi']."' OR sup LIKE '%.".$filsupid."' AND uri NOT LIKE '%.xml' AND idi = '00' ORDER BY sup";
			$mna = mysql_query($mna);
	
			/*if(substr($fil['uri'], 0) == '#') {
				$link = $fil['uri'];
			} else {
				$link = ZW_URL.$fil['uri'];
			}*/
			$link = ZW_URL.$fil['uri'];
			if(preg_match('/http/', $fil['uri'])) {
				$link = $fil['uri'];
			}
			if(preg_match('/#/', $fil['uri'])) {
				$link = $fil['uri'];
			}
			/*if() {
				$link = $fil['uri'];
			}*/
			if(!isset($meta['class']) || $meta['class'] == 'class') { $meta['class'] = ''; }
			if( $fil['uri'] == $ruri || ($fil['uri'] == '' && $ruri == 'index.html')) {
				$cla = ' cc';
			} else {
				$cla = '';
			}
			$cla = $meta['class'].$cla;
			if($cla != '') $cla = ' class="'.$cla.'"';
			
			$smenu ="";
			if(mysql_num_rows($mna) >= 1 && $vs == 1) {
				$smenu = '
				<ul class="'.$smc.'">';
				while($filu = mysql_fetch_array($mna)) {
					$metau = unserialize($filu['meta']);
					list($pos, $pad) = explode('.', $filu['sup']);
					if($pos != 0) {
						$mnlk = ZW_URL.$filu['uri'];
						if(preg_match('/http/', $filu['uri'])) {
							$mnlk = $filu['uri'];
						}
						if(preg_match('/#/', $filu['uri'])) {
							$mnlk = $filu['uri'];
						}
					$smenu.= '
				<li'.$cla.'><a href="'.$mnlk.'"'.$cla.'>'.$metau['label'].'</a></li>';
					}
				}
				$smenu.= "\r\t\t\t\t</ul>";
			}
			
			if($pos != 0) {
					$menu .= '
			<li'.$cla.'><a href="'.$link.'"'.$cla.' '.$rel.' >'.htmlspecialchars($meta['label']).'</a>'.$smenu.'</li>';
			}
		}
		$menu.= '
	</ul>
	</nav>
	';
	}
	return $menu;

}


function pagTwitter() {
	global $cfg,$gcfg;
	//$censored = array('herziliagato');
	if ($gcfg['twitter-oauth'] != '' && isset($gcfg['twitter']) && isset($gcfg['twitter-oauth']) ) {
		include_once(ZW_DIR.'sis/api/twitter/twitter.class.php');
		Twitter::$cacheDir = $cfg['dir'].'tmp';
		$twtr = explode(',', $gcfg['twitter']);
		$twitter = new Twitter($twtr[0], $twtr[1]);
		if (isset($_POST['posttwitterstatus'])) {
			$status = $twitter->send($_POST['posttwitterstatus']);
			echo $status ? 'OK' : 'ERROR';
		}
		$channel = $twitter->load(Twitter::ME_AND_FRIENDS);
		if($channel != TwitterException) {
			$ret = "
			<ul>";
			foreach ($channel->status as $status) {
			$ret .= '<li><a href="http://twitter.com/'.$status->user->screen_name.'">';
			/*if (in_array($censored, $status->user->screen_name)) {
				$ret .= '<img src="http://www.feelriviera.com/images/logo.png">';
			} else {*/
				$ret .= '<img src="'.$status->user->profile_image_url.'">';
			//}
			$ret .= ' '.$status->user->name.'</a>:
			'.$status->text.'
			<small>at '.date("j.n.Y H:i", strtotime($status->created_at)).'</small>
			</li>';
			}
			$ret .= '
			</ul>';
		} else {
			$ret = 'Twitter ERROR! o_O';
		}
		return $ret;
	}
}

/*
function displayPoint(marker, index){
	$("#message").hide();
	var moveEnd = GEvent.addListener(document.map, "moveend", function(){
		var markerOffset = map.fromLatLngToDivPixel(marker.getLatLng());
		$("#message")
			.fadeIn()
			.css({ top:markerOffset.y, left:markerOffset.x });
	
		GEvent.removeListener(moveEnd);
	});
	map.panTo(marker.getLatLng());
}
*/
/*function vLgr(nom, adr, pnt, tip, txt) {
	var icn = new GIcon();*/
	/*icn.image = "http://maps.google.com/mapfiles/ms/icons/"+tip+".png";
	icn.shadow = "http://maps.google.com/mapfiles/ms/icons/"+tip+".shadow.png";
	icn.iconSize = new GSize(32, 32);
	icn.shadowSize = new GSize(59, 32);
	icn.iconAnchor = new GPoint(16, 16);
	icn.infoWindowAnchor = new GPoint(16, 16);
	icn.infoShadowAnchor = new GPoint(18, 25);*/
	/*icn.image = "http://labs.google.com/ridefinder/images/mm_20_red.png";
	icn.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
	icn.iconSize = new GSize(12,20);
	icn.shadowSize = new GSize(22,20);
	icn.iconAnchor = new GPoint(6,20);
	icn.infoWindowAnchor = new GPoint(6,1);
	icn.infoShadowAnchor = new GPoint(13,13);
	var lgr = new GMarker(pnt, icn);
	if(nom != '') {
		if(adr != '') {
			var dir = "<div align=\"right\"><em>"+adr+"</em></div>";
		} else {
			var dir = '';
		}
		GEvent.addListener(lgr, "click", function() {
			GInfoWindowOptions.maxWidth(50);
			lgr.openInfoWindowHtml("<div style=\"padding:10px; margin:10px; width:150px;\"><h3>"+nom+"</h3>"+dir+txt+"</div>"); 
			});
	}
	return lgr;
}*/
/*
function createMarker(point, color) {
	var f = new GIcon();
	f.image = "//labs.google.com/ridefinder/images/mm_20_" + color + ".png";
	f.shadow = "//labs.google.com/ridefinder/images/mm_20_shadow.png";
	f.iconSize = new GSize(12,20);
	f.shadowSize = new GSize(22,20);
	f.iconAnchor = new GPoint(6,20);
	f.infoWindowAnchor = new GPoint(6,1);
	f.infoShadowAnchor = new GPoint(13,13);
	marker = new GMarker(point,{draggable:true});
	return marker;
}
*/
/*
	GEvent.addListener(document.map, "click", function(overlay, point) {
		$('#lat').attr('value', point.y);
		$('#lon').attr('value', point.x);
		if (marker == null) {
			marker = createMarker(point, 'green');
			//marker.enableDragging();
			GEvent.addListener(marker, "dragend", function() {
				$('#lat').attr('value', marker.getPoint().y);
				$('#lon').attr('value', marker.getPoint().x);
			});
			document.map.addOverlay(marker);
		} else {
			marker.setPoint(point);
		}
	});


$(markers).each(function(i,marker){
	$("<li />")
		.html("Point "+i)
		.click(function(){
			displayPoint(marker, i);
		})
		.appendTo("#list");
	
	GEvent.addListener(marker, "click", function(){
		displayPoint(marker, i);
	});
});
*/