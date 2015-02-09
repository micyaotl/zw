<?php

class Admin {
	var $a;
	var $hdx;
	var $css;
	var $js;
	var $jsjq;
	
	public function __construct() {
		if (isset($_GET['a'])) {
			$this->a = $_GET['a'];
		} else {
			$this->a = '';
		}
	}

	public function &admHead() {
		global $zihweb, $gcfg, $lib, $cfg;
		$as = $this->a;
		$hdx = $css = $js = $jsjq = '';
		$hdx .= '
	<!--
	'.ZW_V.'  (c) 2009-'.date('Y').' ZihWeb CMS (www.zihweb.com)
	-->
	<script src="'.ZW_URL.'lib/js.zw"></script>
	<script src="'.ZW_URL.'lib/jquery.pph.js"></script>
	<script src="'.ZW_URL.'lib/jquery.ui.redmond.js"></script>';
$hdx .= N.T.'';
	$js .= N.T.T.'var siteurl = "'.ZW_URL.'";';
	$css .= '
	@import url('.ZW_URL.'lib/css.zw);
	@import url("'.$lib.'adm.css");
	';

	if ($as == '' && !isset($_GET['edita'])) {
		$jsjq .= $this->docList('jsjq');
	}
	
	if($as == 'uld') {
		$hdx .= $this->uldPanel('hdx');
		$css .= $this->uldPanel('css');
		$js .= $this->uldPanel('js');
		$jsjq .= $this->uldPanel('jsjq');
	}

	if($as == 'map') {
		$hdx .= $this->mapHead('hdx');
		$js .= $this->mapHead('js');
		$css .= $this->mapHead('css');
	}

	$jsjq .= <<< EOD
			jQ('input[title]').each(function(){
				if (jQ(this).val() === '') {
					jQ(this).val(jQ(this).attr('title'));
				}
				jQ(this).focus(function(){
					if (jQ(this).val() === jQ(this).attr('title')) {
						jQ(this).val('').addClass('focused');
					}
				});
				jQ(this).blur(function(){
					if (jQ(this).val() === '') {
						jQ(this).val(jQ(this).attr('title')).removeClass('focused');
					} 
				});
			});
EOD;

		if ($as == 'cfg') {
			$this->cfgPanel('hdx');
		}

		if($js != '') {
			$js = N.T.'<script>
		//<![CDATA[
		'.$js.$this->js.'
		var jQ = jQuery.noConflict();
		jQ(document).ready(function(){
			'.$jsjq.$this->jsjq.'
		});
		//]]>
	</script>';
		}
		if($css != '') $css = N.T.'<style>'.$css.N.T.'</style>';
	
		if (esAdmin() && isset($_GET['edita']) && !isset($_GET['editor']) ) {
			$js .= $zihweb->viseditor('textarea.cnt', 'default', 'jQ');
		}
		if(esAdmin() && $as == 'eml') {
			$js .= $zihweb->viseditor('.msgedit', 'default', 'jQ');
		}
		$rett = $hdx.$this->hdx.$css.$this->css.$js;
		return $rett;
	}

	/**
	 * 
	 * @param string $type menu || panel || icons
	 * @return string
	 */
	public function admMenu($type='menu') {
		global $cfg, $gcfg;
		$ret = '';
		$actfunc = array('general', 'pages', 'folders', 'users', 'mails', 'maps', );
		$mails = '<li><a href="'.ZW_URL.ZW_ADM.'?a=eml">mails</a></li>
			';
		if (WASA == 1) {
			$adpag = '<li><a href="'.ZW_URL.ZW_ADM.'?edita=0">+pag</a></li>
		<li><a href="'.ZW_URL.ZW_ADM.'?a=cfg">config</a></li>';
			$mapscr= '<li><a href="'.ZW_URL.ZW_ADM.'?a=map">mapa</a></li>';
		}
			//	<li><a href="'.ZW_URL.ZW_ADM.'?a=general">settings</a></li>
		$ret .= N.T.'<menu>
		<li><a href="'.ZW_URL.ZW_ADM.'">admin</a></li>'.N;
		if(WASA == 1) {
			$ret .= '
		'.$adpag.'
		<li><a href="'.ZW_URL.ZW_ADM.'?a=usr">usuario</a></li>
		'.$mapscr.'
		<li><a href="'.ZW_URL.ZW_ADM.'?a=uld">uploads</a></li>
		'.$mails.'
		<li><a href="'.ZW_URL.ZW_ADM.'?a=btt" color="red">beta</a></li>';
		}
		$ret .= N.T.'</menu>';
		return $ret;
	}

	private function docList($tip='cnt') {
		global $cls_cfg, $cls_dyn;
		$cntlst = $cls_dyn->cntlst('pag');
		if ($tip == 'cnt') {
			$select = <<< EOPAGE
			<select name="tip" id="tip">
				<option value="pag">Paginas</option>
				<option value="src">Scripts</option>
				<option value="all">All</option>
			</select>
			<div id="contl">$cntlst</div>
EOPAGE;
			return $select;
		}
		
		if ($tip == 'jsjq') {
			$borrcntdlg = 'jQ("a.del").click(function() {
			var href = jQ(this).attr("href");
			jQ( "#dialog:ui-dialog" ).dialog( "destroy" );
			jQ( "#dialog-confirm" ).dialog({
				resizable: false,
				draggable: false,
				height:200,
				width:360,
				modal: false,
				buttons: {
					"Borrar pagina": function() {
						jQ.ajax({
							url: href,
							success: function() {jQ(this).dialog("close"), location.reload()}
							});
					},
					"Cancelar": function() {
						jQ( this ).dialog( "close" );
					}
				}
			});
			event.preventDefault();
		});';
			/*jQ(function(){
			 var tip = jQ('#tip').val();
					fl = siteurl+'dyn.zw?cntlst&'+tip;
					jQ.ajax({ url: fl, success: function(html){ jQ('#contl').html(html) } });
					});*/
			$this->jsjq .= <<< EOD
		jQ('#tip').change(function(){
			var tip = jQ(this).val();
			fl = siteurl+'dyn.zw?cntlst&'+tip;
			jQ.ajax({ url: fl, success: function(html){ jQ('#contl').html(html) } });
		});
		jQ("a[rel^='pph']").prettyPhoto({theme: 'dark_square', hideflash: true});
		$borrcntdlg
EOD;
			$this->jsjq .= '
';
			return $this->jsjq;
		}
	}
	
	public function admPanel($class='html') {
		global $req_uri, $cfg, $master, $deldlg, $cls_cfg;
		if (isset($_GET['borcnt']) && esAdmin()) {
			$sql = sprintf("DELETE FROM %s WHERE id = %u LIMIT 1", TBLPRE.'cnt', $_GET['borcnt']);
			$cls_cfg->query($sql);
			@header('Location: '.ZW_URL.ZW_ADM);
		}
		$listedit = $this->docList('cnt');
		$ret = <<< EOPAGE

$listedit
<div id="dialog-confirm" style="display:none;" title="Borrar pagina?">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Esta pagina sera borrada permanentemente y no se puede deshacer. Estas seguro?</p>
	</div>
EOPAGE;
		echo $ret;
	}
	
	public function mapPanel() {
		global $cls_cfg;
		$cfg = $GLOBALS['cfg'];
		if (isset($_POST['editmarker']) && esAdmin()) {
			$idcnt = $_GET['emarker'];
			$des = serialize($_POST['desc']);
			$des = fixSql($des);
			if(isset($_POST['nuevo'])) {
				$sql = sprintf("INSERT INTO %s (`id`, `nom`, `tip`, `lat`, `long`, `cor`, `desc`, `link`) VALUES (%u, %s, %s, %s, %s, %s, %s, %s)",
								TBLPRE.'cnt_map', 0, fixSql($_POST['tit']), fixSql($_POST['tip']), fixSql($_POST['lat']), fixSql($_POST['lon']), fixSql($_POST['cor']), $des, fixSql($_POST['url']));
				$cls_cfg->query($sql);
			} elseif($_POST['tit'] == '' && esAdmin()) {
				$sql = sprintf("DELETE FROM %s WHERE id = %u LIMIT 1", TBLPRE.'cnt_map', $idcnt);
				$cls_cfg->query($sql);
			} else {
				$sql = sprintf("UPDATE %s SET `nom`=%s, `tip`=%s, `lat`=%s, `long`=%s, `cor`=%s, `desc`=%s, `link`=%s  WHERE `id` = %u LIMIT 1",
								TBLPRE.'cnt_map', fixSql($_POST['tit']), fixSql($_POST['tip']), fixSql($_POST['lat']), fixSql($_POST['lon']), fixSql($_POST['cor']), $des, fixSql($_POST['url']), $idcnt);
					/*$sql = sprintf("UPDATE %s SET nom=%s, `desc`=%s, link=%s  WHERE id=%u LIMIT 1",
								'cnt_map', fixSql($_POST['tit']), $des, fixSql($_POST['link']), $idcnt);*/
				$cls_cfg->query($sql);
			}
		}
		if (isset($_GET['emarker'])) {
			$mrkid = $_GET['emarker'];
			$sql = "SELECT * FROM `".TBLPRE."cnt_map` WHERE id = $mrkid LIMIT 1";
			$sql = $cls_cfg->query($sql);
			$mrk = $cls_cfg->fetch($sql, 'assoc');
			$desc = unserialize($mrk['desc']);
			if(!is_array($desc)) {
				$desc['txt'] = $mrk['desc'];
				$desc['dir'] = $mrk['cor'];
				if ($desc['uuid'] == '') { $desc['uuid'] = $cls_cfg->uuid(); }
			}
		
		} else {
			$mrkid = 0;
			$mrk = array();
			$mrk['uuid'] = $cls_cfg->uuid();
		}
		if ($mrkid==0) {
			$mrkid = $cls_cfg->query("SELECT id FROM ".TBLPRE."cnt_map ORDER BY id DESC LIMIT 1");
			$mrkid = $cls_cfg->fetch($mrkid, 'assoc');
			$mrkid = $mrkid['id']+1;
			$desc['uuid'] = $cls_cfg->uuid();
			$nuevo = true;
		}
		$ret = '
<form method="post" action="javascript:void(0)" onsubmit="irDireccion()">
<input type="text" id="direccion" size="50" title="Buscar direccion, ciudad, estado +enter" />
</form>
<div id="map"></div>
<form action="'.ZW_ADM.'?a=map&amp;emarker='.$mrkid.'" method="post" enctype="multipart/form-data">
<input type="text" name="lat" id="lat" title="lat" size="12" maxlength="15" value="'.$mrk['lat'].'" />
<input type="text" name="lon" id="lon" title="lon" size="12" maxlength="15" value="'.$mrk['long'].'" />
<input type="text" name="tit" id="tit" title="Titulo" size="30" maxlength="250" value="'.$mrk['nom'].'" />
<br /><input type="text" title="Direccion" name="desc[dir]" id="dir" size="30" maxlength="250" value="'.$desc['dir'].'" />
<textarea id="desc" name="desc[desc]" cols="50" rows="10" wrap="soft" style="width: 350px;">'.$desc['desc'].'</textarea>
<!--textarea id="cor" name="cor" class="cor" cols="2" rows="2" wrap="soft" style="">'.$mrk['cor'].'</textarea-->
<input type="text" name="desc[img]" id="img" title="Url de imagen" size="30" maxlength="250" value="'.$desc['img'].'" />
<input type="text" name="desc[icon]" id="url" title="Url del icono" size="30" maxlength="250" value="'.$desc['icon'].'" />
<br /><input type="text" name="url" id="url" title="Url de enlace" size="30" maxlength="250" value="'.$mrk['link'].'" />
<br /><input name="submit" type="submit" value="Enviar" />
<input name="desc[uuid]" type="text" value="'.$desc['uuid'].'" />
';
if(isset($nuevo)) { $ret .= '<input name="nuevo" type="hidden" />'; }
$ret .= '<input name="editmarker" type="hidden" />
</form>';
		return $ret;
	}
	
	public function mapList() {
		global $cls_cfg;
		$sql = "SELECT * FROM `".TBLPRE."cnt_map` WHERE 1";
		$sql = $cls_cfg->query($sql);
		$ret = '
	<ul style="width:100%; margin:0px; padding:0px;">';
		while ($row = @$cls_cfg->fetch($sql,'assoc')){
			$ret.= '
		<li><a href="'.ZW_ADM.'?a=map&emarker='.$row['id'].'">';
			$ret.= '<strong>'.$row['nom'].'</strong> ';
			$ret.= ' <em>'.$row['tip'].'</em> ';
			$ret.= ' linea: "'.$row['cor'].'" ';
			$ret.= ' desc:'.$row['desc'] . ' ';
			$ret.= '</a> <a href="'.$row['link'].'">web</a></li>';
		}
		$ret.= '
	</ul>';
		return $ret;
		//<ul id="list"></ul><div id="message" style="display:none;"> dddd </div>
	}
	
	private function mapHead($tip='hdx') {
		global $cfg;
		$hdx = N.T.'<script src="http://maps.google.com/maps/api/js?sensor=false&amp;language='.$cfg['idi'].'"></script>';
		$this->jsjq .= <<< EOD
	
			/* Map jQ */
				var loc = new google.maps.LatLng(20.215009,-87.451469);
				var optns = {
					zoom: 8,
					center: loc,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				var map = new google.maps.Map(document.getElementById("map"), optns);
				var geoLyr = new google.maps.KmlLayer(siteurl+'xml/georss');
				geoLyr.setMap(map);
			
				google.maps.event.addListener(map, 'click', function(e) {
					placeMarker(e.latLng, map);
					//infowindow = new google.maps.InfoWindow({ content: content });
					//infowindow.open(map, this);
				});
					
				function placeMarker(position, map) {
					var marker = new google.maps.Marker({
						position: position,
						map: map,
						draggable: true,
					});
					map.panTo(position);
					jQ('#lat').attr('value', position.lat());
					jQ('#lon').attr('value', position.lng());
				}
EOD;
		$js .= <<< EOD

	function irDireccion() {
		var address = jQ('#direccion').attr('value');
		var geocoder = new google.maps.Geocoder();
		if (geocoder){
			geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				var marker = new google.maps.Marker({
					map: map,
					position: results[0].geometry.location
				});
        	} else {
        		alert("Geocode was not successful for the following reason: " + status);
			}
			});
		}
	}

	function nvMark() {
		var image = new google.maps.MarkerImage('http://code.google.com/apis/maps/documentation/javascript/examples/images/beachflag.png',
		new google.maps.Size(20, 32),
		// The origin for this image is 0,0.
		new google.maps.Point(0,0),
		// The anchor for this image is the base of the flagpole at 0,32.
		new google.maps.Point(0, 32));
		var shadow = new google.maps.MarkerImage('http://code.google.com/apis/maps/documentation/javascript/examples/images/beachflag_shadow.png',
		// The shadow image is larger in the horizontal dimension
		// while the position and offset are the same as for the main image.
		new google.maps.Size(37, 32),
		new google.maps.Point(0,0),
		new google.maps.Point(0, 32));
		// Shapes define the clickable region of the icon.
		// The type defines an HTML &lt;area&gt; element 'poly' which
		// traces out a polygon as a series of X,Y points. The final
		// coordinate closes the poly by connecting to the first
		// coordinate.
		var shape = { coord: [1, 1, 1, 20, 18, 20, 18 , 1], type: 'poly' };

		var myLatLng = new google.maps.LatLng(20.63792419, -87.07763672);
		var beachMarker = new google.maps.Marker({
				position: mylatLng,
				map: map,
				icon: image,
				shape: shape,
				shadow: shadow,
				title: 'My Flag'
			});
	}
EOD;

		$css = N.T.'#map { float:left; width:500px; height:500px; position:relative; margin:0px; }
		#message { position:absolute; padding:10px; background:#555; color:#fff; width:75px; }
		#list { float:left; width:200px; background:#eee; list-style:none; padding:0; }
		#list li { padding:10px; }
		#list li:hover { background:#555; color:#fff; cursor:pointer; cursor:hand; }';
		return $$tip;
	}

	public function uldPanel($tip='cnt') {
		global $lib;
		if ($tip == 'cnt') {
			$url = ZW_URL;
			$dir = 'img';
			$pth = ZW_DIRC.$dir;
			$d2 = dir($pth);
			$ld = $pd = $dir;
			while (false !== ($dir = $d2->read())) {
				if (!is_dir($dir)) {
					$o[] = $dir;
				}
			}
			$d2->close();
			if ($ld == 'img') {
				$ld  = str_replace('img', '', $ld);
			} else {
				$ld  = str_replace('img/', '',$ld).'_';
			}
			$r = N.T.T.T.'<option value="img">img/</option>';
			if (!empty($o)) {
				natcasesort($o);
				foreach($o as $i) {
					$fp = $pth.Ds.$i;
					$ft = filetype($fp);
					$fz = filesize($fp);
					$server = ZW_URL;
					$a = $af = '';
					if (is_dir($fp)) {
						$a = '<option value="'.$pd.'/'.$i.'">'.$pd.'/'.$i.'/</option>';
					}
					$r .= N.T.T.T.T.''.$a; 
				}
			}
			$r .= N.T.T.T.'<option value="audio">audio/</option>';
			
			$ret = <<< EOD
				<p style="font-size:1.5em;clear:both;">Selecciona los archivos que se cargaran <input id="uploads" name="uploads" type="file" /></p>
				<span style="color:#888888;font-size:1.2em;">$url</span>
				<select name="dir" id="dir">
				$r
				</select>
				<div id="fileQueue"><!-- uploader --></div>
				<p>
					<a href="javascript:jQ('#uploads').uploadifyUpload();" style="border-radius:3px;background-color:#00ff00;border:solid 1px #33dd33;padding:5px; font-weight:bold;">Upload Files</a>
					<a href="javascript:jQ('#uploads').uploadifyClearQueue()">Cancel All Uploads</a>
				</p>
				<div id="currFiles"></div>
				<span style="clear:both;color:#888888;">$url<em></em></span>
				<fieldset class="cd">
				<legend>Crear directorio</legend>
					<input name="cd" id="nd" type="text" size="15" maxlength"30" /><a href="#" class="btn btnOrange" id="cda">Crear directorio</a>
				</fieldset>
EOD;
			return $ret;
		}
		
		if ($tip == 'hdx') {
			return '
	<script src="'.$lib.'jquery.pph.js"></script>
	<script src="'.$lib.'jquery.uploadify.js"></script>';
		}
		
		if ($tip == 'jsjq') {
			$uldfil = ZW_URL.'upload';
			$cancel = $lib.'cancel';
			return <<< EOD
		var siz = '&80x80';
		jQ(function(){
			var dir = jQ('#dir').val();
			jQ('span em').text(dir+'/');
			fl = siteurl+'dyn.zw?thumbs&'+dir+siz+'&o';
			jQ.ajax({ url: fl, success: function(html){ jQ('#currFiles').html(html) } });
		});

		jQ("#cda").bind("click",function(){
			var nd = jQ("#nd").val();
			jQ.ajax({ url: siteurl+'dyn.zw?mkdir&'+nd,
						success: function(html){ location.reload() }
			});
		});
		var img = jQ(this).attr('title');
		var ln = 'dyn.zw?girimg&'+img+'-90';
		jQ('.tmb').append('<a href="#" onclick="'+ln+'">-90</a>');

		jQ('#dir').change(function(){
			var dir = jQ(this).val();
			jQ('span em').text(dir+'/');
			jQ("#uploads").uploadifySettings('folder', dir, true);
			if(dir === 'img') { fl = siteurl+'dyn.zw?thumbs&'+dir+siz+'&o'; } else { fl = siteurl+'dyn.zw?thumbs&'+dir+siz+'&o'; }
			jQ.ajax({ url: fl, success: function(html){ jQ('#currFiles').html(html) } });
		});
		jQ("#uploads").uploadify({
			'uploader'		: '$uldfil.swf',
			'script'		: '$uldfil.php',
			'cancelImg'		: '$cancel.png',
			'folder'		: jQ('#dir').val(),
			'queueID'		: 'fileQueue',
			'fileExt'		: '*.jpg;*.jpeg;*.gif;*.png;*.txt',
			'auto'			: false,
			'multi'			: true,
			//'queueSizeLimit': 0,
			'buttonText'	: 'Buscar Archivos'
		});
		jQ(document).ready(function() {
			jQ("a[rel^='pph']").prettyPhoto({theme: 'dark_square', hideflash: true});
		});
EOD;
		}
		
		if ($tip == 'css') {
			return '
	#fileQueue { border: 1px solid #E5E5E5; margin-bottom: 10px; }
	.uploadifyQueueItem { background-color: #F5F5F5; border: 2px solid #E5E5E5; margin-top: 5px; padding: 10px; }
	.uploadifyError { background-color: #FDE5DD !important; border: 2px solid #FBCBBC !important; }
	.uploadifyQueueItem .cancel { float: right; }
	.uploadifyQueue .completed { background-color: #E5E5E5; }
	.uploadifyProgress { background-color: #E5E5E5; margin-top: 10px; width: 100%; }
	.uploadifyProgressBar { background-color: #0099FF; height: 3px; width: 1px; }';
		}
	}
	
	public function srcPanel() {
		global $req_uri, $cfg, $deldlg,$cls_cfg;
	
		$sql = "SELECT id, sup, uri, idi, meta, tit FROM ".TBLPRE."cnt WHERE uri NOT LIKE '%.html' AND uri NOT LIKE '%/' ORDER BY idi, sup, uri";
		$sql = $cls_cfg->query($sql);
	
		$listedit = '<ul class="cntlst">';
		while( $fila = @$cls_cfg->fetch($sql, 'array') ) {
			$meta = unserialize($fila['meta']);
			//if($fila['uri'] == 'thankyou.html' && $master != 1 ) { echo '<!--'; }
			
			$listedit.='
			<li draggable="true">'.$fila['idi'].' | <a href="'.ZW_URL.$fila['uri'].'">ver</a> | ';
			if(WASA==1) {
				$listedit.='<a href="'.ZW_URL.ZW_ADM.'?borcnt='.$fila['id'].'"  class="del">x</a> | ';
			}
			$listedit.= '<a href="'.ZW_URL.ZW_ADM.'?edita='.$fila['id'].'"><strong>'.$meta['label'].'</strong> - '.$fila['uri'].' - '.$fila['tit'].'</a>';
			$listedit.= '</li>';
		}
		$listedit.= '</ul>';
		$ret = '
		<br />
		'.$listedit.'
		<br />
		'.$deldlg;
	
		echo $ret;
	}
	private function borCfg($cfi) {
		global $cls_cfg;
		$sql = sprintf("DELETE FROM %s WHERE id = %u LIMIT 1", TBLPRE.'cnt_cfg', $cfi);
		$cls_cfg->query($sql);
		@header('Location: '.ZW_URL.ZW_ADM.'?a=cfg');
	}
	
	private function addCfg($tit, $tip, $val) {
		global $cls_cfg;
		$query = $cls_cfg->query("INSERT INTO ".TBLPRE."cnt_cfg (nom, idi, val) VALUES ('".$tit."', '".$tip."', ".fixSql($val).")");
		return $query;
	}
	
	private function updCfg($id, $tit, $val) {
		global $cls_cfg;
		$query = $cls_cfg->query(sprintf("UPDATE %s SET nom = %s, val = %s WHERE id = %u", TBLPRE.'cnt_cfg', fixSql($tit), fixSql($val), $id));
		return $query;
	}
	
	public function cfgLst($type = 'gcfg', $idi = '00', $editk = 'editagcfg') {
		global $cls_cfg;
		if (strstr($idi, '%')) {
			$con = 'LIKE';
		} else {
			$con = '=';
		}
		$sql = $cls_cfg->query('SELECT * FROM '.TBLPRE.'cnt_cfg WHERE idi '.$con.' "'.$idi.'" ORDER BY nom');
		 $globalconfigs = '<ul class="'.$type.'">'.N;
		while ( $fila = $cls_cfg->fetch($sql, 'array') ) {
		$globalconfigs.= '<li class="o'.$type.'"><span class="t'.$type.'">
		<a href="'.ZW_URL.ZW_ADM.'?a=cfg&amp;'.$editk.'='.$fila['id'].'">'.$fila['nom'].'</a></span>
		<div style="display:none" class="'.$type.'" id="'.$fila['id'].'">'.htmlentities($fila['val']).'
		</div></li>'.N.T.T;
		//<a href="'.ZW_URL.ZW_ADM.'?a=cfg&amp;borcfg='.$fila['id'].'" style="float:right;" class="ui-state-default ui-corner-all ui-icon ui-icon-trash">borrar</a>
		}
		$globalconfigs.= '</ul>'.N;
		
		return $globalconfigs;
	}
	
	public function cfgPanel($tip = 'cnt') {
		global $cfg,$cls_cfg;
		if ($tip == 'hdx') {
			if (isset($_GET['borcfg']) && esAdmin()) {
				$this->borCfg($_GET['borcfg']);
			}
			if (isset($_POST['+glob-conf']) && esAdmin()) {
				$this->addCfg($_POST['tit'], '00', $_POST['val']);
			}
			if (isset($_POST['edita-conf']) && esAdmin()) {
				$this->updCfg($_POST['confid'], $_POST['tit'], $_POST['val']);
			}
			// redirs
			if (isset($_POST['+redir']) && esAdmin()) {
				$this->addCfg($_POST['tit'], 'xR', $_POST['val']);
			}
			if (isset($_POST['edita-redir']) && esAdmin()) {
				$this->updCfg($_POST['confid'], $_POST['tit'], $_POST['val']);
			}
			/*
			 *
			
			var ids = jQ(this).parents("span."+id).attr("id");
			var lnk = "<a href=\''.ZW_URL.ZW_ADM.'\'?a=cfg&amp;borcfg=\'"+ids+"\' style=\'float:right;\' class=\'ui-state-default ui-corner-all ui-icon ui-icon-trash\'>borrar</a>";
			jQ(this).parents("span").next("."+id).append(ids);
			//jQ(this).parents("span").append();
			*/
			$usus = ZW_URL.ZW_ADM;
			$this->js = <<< EOPAGE
		function tggl(id) {
			jQ(".o"+id+" ."+id).hide();
			jQ("li.o"+id+" span.t"+id).append("<span class=\'v"+id+"\'> <a class=\'v"+id+"\' href=\'\' title=\'Mostrar\'>+</a><a class=\'v"+id+"\' href=\'\' title=\'Ocultar\' style=\'display:none\'>-</a></span>");
			jQ(".v"+id+" a").click(function(event) {
				var cssobj = { "background-color":"#ffe3b9","border-top":"solid 1px #FF000F","border-bottom":"solid 1px #FF000F","margin":"10px","padding":"10px"}
				jQ(this).parents("span.v"+id+" a").toggle();
				jQ(this).parents("span").next("."+id).css(cssobj);
				jQ(this).parents("span").next("."+id).toggle("fast");
				event.preventDefault();
			});
		}
EOPAGE;
			$this->jsjq .= <<< EOD
			
			tggl("gcfg");
			tggl("rdrs");
			tggl("mn");
			jQ('div.gcfg, div.rdrs, div.mn').each(function(){
							var ids = jQ(this).attr('id');
							var lnk = '<a id="del'+ids+'" href="$usus?a=cfg&amp;borcfg='+ids+'" style="float:right;" class="ui-state-default ui-corner-all ui-icon ui-icon-trash del">borrar</a>';
							jQ(this).append(lnk);
						});
					
	// del dlg
		/*jQ("a.del").click(function() {
			var href = jQ(this).attr("href");
			jQ( "#dialog:ui-dialog" ).dialog( "destroy" );
			jQ( "#dialog-confirm" ).dialog({
				resizable: false,
				draggable: false,
				height:200,
				width:360,
				modal: false,
				buttons: {
					"Borrar pagina" : function() {
						jQ.ajax({
							url: href,
							success: function() { jQ(this).dialog("close"), location.reload(); }
							});
					},
					"Cancelar" : function() {
						jQ( this ).dialog( "close" );
					}
				}
			});
			event.preventDefault();
		});*/
	// dell dlg
			
EOD;
		}

		$globalconfigs = $this->cfgLst('gcfg', '00');
		$redirs = $this->cfgLst('rdrs', 'xR', 'edita-redir');
		$ret = '';
		if(WASA == 1) {
			$ret ='
			<fieldset class="izq">
			<legend>+global config</legend>
			'.$globalconfigs.'
			<form action="'.ZW_URL.ZW_ADM.'?a=cfg" method="post" enctype="multipart/form-data">';
			$cont['nom'] = '';
			$cont['val'] = '';
			$mod = '+glob-conf';
			if(isset($_GET['editagcfg'])) {
				$sql = $cls_cfg->query('SELECT * FROM '.TBLPRE.'cnt_cfg WHERE id = "'.$_GET['editagcfg'].'" AND idi = "00" LIMIT 1');
				$cont = $cls_cfg->fetch($sql, 'assoc');
				$mod = 'edita-conf';
			}
			$ret.= '<input name="tit" type="text" size="30" title="config id" maxlength="64" value="'.$cont['nom'].'" /><br />
			<textarea id="val" name="val" cols="50" rows="10" wrap="soft">'.$cont['val'].'</textarea>
			<input name="'.$mod.'" type="hidden" />';
			if(isset($_GET['editagcfg'])) {
			$ret.= '<input name="confid" type="hidden" value="'.$cont['id'].'" />';
			}
			$ret.= '<input name="submit" type="submit" />
			</form>
			
			</fieldset>
			<fieldset class="der">
			<legend>redirs</legend>
			'.$redirs;
		
			$ret .= '
			<form action="'.ZW_URL.ZW_ADM.'?a=cfg" method="post" enctype="multipart/form-data">';
			$cont['nom'] = '';
			$cont['val'] = '';
			$modr = '+redir';
			$rdr = array('nom' => '', 'val' => '');
			if (isset($_GET['edita-redir'])) {
				$sql = $cls_cfg->query('SELECT * FROM '.TBLPRE.'cnt_cfg WHERE id = "'.$_GET['edita-redir'].'" AND idi = "xR" LIMIT 1');
				$rdr = $cls_cfg->fetch($sql, 'assoc');
				$modr = 'edita-redir';
			}
			$ret.= '
			<input name="tit" type="text" size="30" title="request redir" maxlength="200" value="'.$rdr['nom'].'" /><br />
			<input name="val" type="text" size="30" title="target redir" maxlength="200" value="'.$rdr['val'].'" /><br />
			<input name="'.$modr.'" type="hidden" />';
			if(isset($_GET['edita-redir'])) {
			$ret.= '<input name="confid" type="hidden" value="'.$rdr['id'].'" />';
			}
			$ret.= '<input name="submit" type="submit" />
			</form>
			</fieldset>
			<fieldset class="der">
			<legend>tmp</legend>
					'.$this->cfgLst('mn', "%-m").$this->cfgLst('mn', "%-sm").'
							
			</fieldset>';
		}
		
		return $ret;
	}
	
	public function usrPanel() {
		global $cfg, $gcfg, $cls_cfg;
	
		if(isset($_POST['usuario-nuevo']) && esAdmin() && WASA == 1) {
			$date = time();
			$unvopass = generatePassword(9, 8); //str_shuffle(substr(md5($date),-12,8));
			$passbdd = md5($unvopass);
			$ktk = md5($passbdd.md5($date));
			$sql = sprintf("INSERT INTO %s (uid, fid, nombre, email, pass, ktk, fechreg, foto, web, descr, comm, nomcom)
										VALUES (%u, %u, %s, %s, %s, %s, %u,%s, %s, %s, %s, %s)",
							TBLPRE.'cnt_usr', 0, 0, fixSql($_POST['nombre']), fixSql($_POST['email']), fixSql($passbdd), fixSql($ktk), $date, fixSql(''), fixSql(''), fixSql(''), fixSql(''), fixSql(''));
			$cls_cfg->query($sql);
		}
	
		if(isset($_POST['actusr']) && isset($_GET['uid']) && (_UID == $uid || WASA == 1)) {
			$descr = serialize($_POST['descr']);
			$comm = '';
			$passdb = '';
	
			if ($_POST['pass'] != 'password') {
				if($_GET['uid'] == 1 && _UID == $_GET['uid']) {
					$pass = $_POST['pass'];
					$passbdd = md5($pass);
					$ktk = md5($passbdd.md5($date));
					$passdb = ' pass = '.fixSql($passbdd).',';
				} elseif (WASA == 1) {
					$pass = $_POST['pass'];
					$passbdd = md5($pass);
					$ktk = md5($passbdd.md5($date));
					$passdb = ' pass = '.fixSql($passbdd).',';
				}
			}
	
			if (isset($_POST['comm'])) {
				$comm = ' comm = '.fixSql($_POST['comm']).',';
			}
	
			$sql = sprintf("UPDATE ".TBLPRE."cnt_usr SET nombre = %s, email = %s,$passdb web = %s, descr = %s,$comm nomcom = %s WHERE uid = %u LIMIT 1", fixSql($_POST['nombre']), fixSql($_POST['email']), fixSql($_POST['web']), fixSql($descr), fixSql($_POST['nomcom']), $_GET['uid']);
			$cls_cfg->query($sql);
		}
	
		$sql = $cls_cfg->query('SELECT * FROM '.TBLPRE.'cnt_usr ORDER BY fechreg');
		$usrlst = '<ul class="usrlst">
		';
		while ( $fila = @$cls_cfg->fetch($sql, 'array') ) {
			$usrlst.='<li><a href="'.ZW_URL.ZW_ADM.'?a=usr&amp;uid='.$fila['uid'].'"><span>'.$fila['nomcom'].' ('.$fila['nombre'].')</span> <em>'.$fila['email'].' - '.$fila['comm'].'</em> </a></li>
			';
		}
		$usrlst .= '</ul>
		';
		$edituser = '';
		if(isset($_GET['uid'])) {
			$uid = $_GET['uid'];
			$sql = $cls_cfg->query("SELECT * FROM ".TBLPRE."cnt_usr WHERE uid = $uid LIMIT 1");
			$usr = $cls_cfg->fetch($sql, 'assoc');
			$descr = unserialize($usr['descr']);
			$dis = ' disabled';
			if(_UID == $uid || WASA == 1) {
				$dis = '';
			}
			$edituser = '
			<script>
			//<![CDATA[
			var str = "'.$usr['comm'].'";
			jQ("select option:selected").change(function () {
	          jQ("div").text(str);
	        })
	        .trigger("change");
			//]]>
			</script>
			<form action="'.ZW_URL.ZW_ADM.'?a=usr&uid='.$uid.'" method="post" enctype="multipart/form-data">
			<input name="nombre" type="text" size="30" title="Usuario" value="'.$usr['nombre'].'" '.$dis.'/>
			<input name="email" type="text" size="30" title="Email" value="'.$usr['email'].'" '.$dis.'/>
			<input name="nomcom" type="text" size="30" title="Nombre completo" value="'.$usr['nomcom'].'" '.$dis.'/>
			';
			if(WASA == 1) {
				$edituser .= '
			<select name="comm" id="slvl" '.$dis.'>
			<option value="'.$usr['comm'].'">'.strtoupper($usr['comm']).'</option>
			<option value="root">ROOT</option>
			<option value="admin">ADMIN</option>
			<option value="editor">EDITOR</option>
			<option value="agent">AGENT</option>
			<option value="client">CLIENT</option>
			</select> <input name="descr[clid]" type="text" size="10" title="clientid" value="'.$descr['clid'].'" '.$dis.'/>
			<br />
			';
			}
			$edituser .= '<input name="web" type="text" size="30" title="Web url" value="'.$usr['web'].'" '.$dis.'/><br />
			<textarea name="descr[descr]" cols="50" rows="10" wrap="soft" '.$dis.'>'.$descr['descr'].'</textarea>
			';
			
			if(_UID == $uid || WASA == 1) {
				$edituser .= '<br /><input name="pass" type="password" title="password" size="30" maxlength="64" '.$dis.'/><br />';
			}
			
			$edituser.= '
			<input name="actusr" type="hidden" />
			<input name="submit" type="submit" value="Actualizar usuario" '.$dis.'/>
			</form>
			';
		}
		$ret = '
		<h3>Editar usuario</h3>
		'.$edituser.$usrlst.'
		<h3>Crear nuevo usuario</h3>
		<form action="'.ZW_URL.ZW_ADM.'?a=usr" id="nvousr" method="post" enctype="multipart/form-data">
		<input name="nombre" title="Usuario" type="text" size="30" maxlength="64" />
		<input name="email" title="Email" type="text" size="30" maxlength="64" />';
		if(isset($_POST['usuario-nuevo'])) {
			$ret.=$unvopass;
		}
		$ret.= '<input name="usuario-nuevo" type="hidden" />
		<input name="submit" type="submit" value="Crear usuario" />
		</form><br />
		';
		echo $ret;
			unset($sql);
	}
	public function bTest() {
		global $cls_cfg;
		$cont = '';
	}
	
	public function admEdit($idcnt, $tip) {
		global $cfg, $gcfg, $lib, $cls_cfg;
		if (isset($_POST['editcnt']) && esAdmin()) {
			//$idcnt = $_GET['edita'];
			if (isset($_POST['pos']) && isset($_POST['sup'])) {
				$ord = $_POST['pos'].'.'.$_POST['sup'];
			} else {
				$ord = $_POST['pos'].'.0';
			}
			
			if(isset($_POST['nuevo'])) {
				$sql = sprintf("INSERT INTO %s (id, sup, uri, idi, tit, meta, cnt, hdx, cnx) VALUES (%u, %s, %s, %s, %s, %s, %s,%s, %s)",
								TBLPRE.'cnt', $idcnt, fixSql($ord), fixSql($_POST['uri']), fixSql($_POST['idi']), fixSql($_POST['tit']), fixSql(serialize($_POST['meta'])), fixSql($_POST['cnt']), fixSql($_POST['hdx']), fixSql($_POST['cnx']));
				$cls_cfg->query($sql);
			} else {
				if(WASA == 1) {
					$sql = sprintf("UPDATE %s SET sup=%s, uri=%s, idi=%s, tit=%s, meta=%s, cnt=%s, cnx=%s, hdx=%s WHERE id= %u LIMIT 1",
									TBLPRE.'cnt', fixSql($ord), fixSql($_POST['uri']), fixSql($_POST['idi']), fixSql($_POST['tit']), fixSql(serialize($_POST['meta'])), fixSql($_POST['cnt']),fixSql($_POST['cnx']), fixSql($_POST['hdx']), $idcnt);
				} else {
					$sql = sprintf("UPDATE %s SET tit=%s, cnt=%s WHERE id= %u LIMIT 1", TBLPRE.'cnt', fixSql($_POST['tit']), fixSql($_POST['cnt']), $idcnt);
				}
				$cls_cfg->query($sql);
			}
		}
		
		// nuevo id
		if ($idcnt==0) {
			$idcnt = $cls_cfg->query("SELECT id FROM ".TBLPRE."cnt ORDER BY id DESC LIMIT 1");
			$idcnt = $cls_cfg->fetch($idcnt, 'assoc');
			$idcnt = $idcnt['id']+1;
			//$v = '';
			$cont = array(
					'id' => '0',
					'idi' => '00',
					'cnt' => '',
					'cnx' => '',
					'hdx' => '',
					'meta' => array(),
					'lat' => '',
					'long'=> '',
			);
			$pos = $sup = $meta['lat'] = $meta['long'] = $v;
			$cont['uri'] = 'nombre-de-pagina.html';
			$cont['tit'] = 'Titulo de la pagina';
			$meta['label'] = 'Etiqueta del Menu';
			$meta['desc'] = 'Descripcion meta del encabezado html';
			$meta['keywrds'] = 'Keywords, palabras clave, separadas, por, comas';
			$meta['class'] = 'css class';
			$meta['ext'] = 'extension';
		} else {
			$sql = $cls_cfg->query('SELECT * FROM '.TBLPRE.'cnt WHERE id = "'.$idcnt.'" LIMIT 1');
			$cont = $cls_cfg->fetch($sql, 'assoc');
			$meta = unserialize($cont['meta']);
			list($pos, $sup) = explode('.', $cont['sup']);
		}
		
		/**
		 * @var Lista de paginas principales
		 */
		$listsup = '	<select name="sup">';
		$listsup.= '	<option value="0" ';
		if ($sup == 0 || $sup == '') { $listsup.= 'selected'; }
		$listsup.= '>-- no parent document</option>';
		$sql = $cls_cfg->query('SELECT id, idi, sup, tit FROM '.TBLPRE.'cnt WHERE sup LIKE "%.0" AND idi LIKE "'.$cont['idi'].'" AND id NOT LIKE "'.$cont['id'].'"  ORDER BY sup, idi');
		while ( $fila = $cls_cfg->fetch($sql, 'array') ) {
		$listsup.='
			<option value="'.$fila['id'].'"';
		if($fila['id'] == $sup) { $listsup.= ' selected '; }
			$listsup.='>'.$fila['tit'].'</option>';
		}
		$listsup.= '</select>';
	
				/**
				 * @var Saltar a pagina
				 */
				$salpag = '<select name="edita" onchange="this.form.submit();">';
				$salpag.= '	<option value="0" selected>-- jump and edit</option>';
				$sql = $cls_cfg->query('SELECT id, idi, sup, tit FROM '.TBLPRE.'cnt WHERE id NOT LIKE "'.$cont['id'].'"  ORDER BY idi, tit, sup');
				while ( $fila = $cls_cfg->fetch($sql, 'array') ) {
				$salpag.='
					<option value="'.$fila['id'].'">'.$fila['tit'].'</option>';
				}
				$salpag.= '</select>';
	$selext = '';
	if(isset($gcfg['ext'])) {
		$exts = explode(',', $gcfg['ext']);
		$exs = array();
		foreach($exts as $exs) {
			if(file_exists(ZW_DIR.'sis'.Ds.'ext'.Ds.$exs.'.php')) {
				include_once ZW_DIR.'sis'.Ds.'ext'.Ds.$exs.'.php';
			}
			$exs .= $extdat['nombre'];
		}
		
		$selext = '	<label>Extension</label> <select name="meta[ext]">';
		$selext.= '	<option value="0" ';
		if (!isset($meta['ext']) || $meta['ext'] == 0) { $selext.= 'selected'; }
		$selext.= '>--------</option>';
		foreach($exts as $exi) {
			$selext.='
		<option value="'.$exi.'"';
	if($meta['ext'] == $exi) { $selext.= ' selected '; }
		$selext.='>'.$extnom.'</option>';
		}
		$selext.= '</select>';
	}
		$cnt = '
		<form action="'.ZW_URL.ZW_ADM.'?edita='.$idcnt.'" method="post" enctype="multipart/form-data">
			<a href="'.ZW_URL.$cont['uri'].'" class=" ui-state-default ui-corner-all ui-icon ui-icon-extlink" target="prvw">&nbsp;</a>
		';
		if(WASA == 1) {
		$cnt.='
			<input title="page-name.html" name="uri" type="text" size="20" maxlength="64" value="'.$cont['uri'].'" />
			<input title="Menu Label" name="meta[label]" type="text" size="20" value="'.$meta['label'].'" />
			<input title="0" name="pos" type="text" size="2" maxlength="2" value="'.$pos.'"  />
			
		'.selIdiForm($cont['idi']);
		}
		$cnt.='
	<input title="Document Title" name="tit" type="text" size="30" value="'.$cont['tit'].'" />';
		if(WASA == 1) {
			/* Lista Pagina superior*/
			$cnt.= '
			<div style="float:right; display:block; width:150px; position:relative; ">
				<input name="meta[lat]" type="text" size="10" maxlength="15" value="'.$meta['lat'].'" title="lat" /><br />
				<input name="meta[long]" type="text" size="10" maxlength="15" value="'.$meta['long'].'" title="lon" />
			</div>
			'.$listsup;
			$cnt.='
			<input title="Document brief description, content extract" name="meta[desc]" type="text" size="30" value="'.$meta['desc'].'"  />
			<input title="document keywords, comma, separated" name="meta[keywrds]" type="text" size="30" value="'.$meta['keywrds'].'"  />'.$selext.'<br />
			<input name="meta[class]" type="text" size="10" maxlength="15" value="'.$meta['class'].'" title="class" />
			&nbsp;
			<input name="meta[ext]" type="text" size="10" maxlength="15" value="'.$meta['ext'].'" title="extend" />';
		}

		if ($gcfg['banners'] == 1 || $gcfg['ads'] == 1) {
			if (isset($meta['banners'])) { $banners = $meta['banners']; } else { $banners = ''; }
			$cnt.='
			<input title="banners" name="meta[banners]" type="text" size="50" value="'.$banners.'"  /><br />
			';
		}
		$cnt.='
			<textarea id="cnt" class="cnt" name="cnt" cols="50" rows="20" wrap="soft" style="width: 100%;">'.$cont['cnt'].'</textarea>
		';
		if(WASA == 1) {
		$cnt.='
			<fieldset class="izq">
			<legend>Header Extra</legend>
				<textarea id="hdx" name="hdx" cols="50" rows="10" wrap="soft">'.$cont['hdx'].'</textarea>
			</fieldset>
				<fieldset class="der">
			<legend>Contenido Extra</legend>
				<textarea id="cnx" name="cnx" cols="50" rows="10" wrap="soft">'.$cont['cnx'].'</textarea>
			</fieldset>';
		}
		$cnt.='<input name="editcnt" type="hidden" />
		<input name="meta[lastmod]" type="hidden" value="'.gmdate("D, d M Y H:i:s").'">';
		if ($_GET['edita']==0) {
			$cnt.= '<input name="nuevo" type="hidden" />';
		}
		$cnt.= '<input name="submit" type="submit" />
		</form>
		<form action="'.ZW_URL.''.ZW_ADM.'" method="get" enctype="multipart/form-data">'.
		$salpag
		.'</form>';
		return $$tip;
	}
	
	public function emlPanel() {
		global $gcfg, $mailconfig;
		$mc4 = $mailconfig[4];
		$mailfooter = remTxt(remTxt($gcfg['mailfooter']));
		$mailheader = remTxt(remTxt($gcfg['mailheader']));
		$mailbody = $gcfg['mailbody'];
		$ret = <<< EOD
		<h1>Send Mail</h1>
				<form action="sm.zw" method="post" enctype="multipart/form-data">
	            
	              <p>
	                <label for="dest">Destinatario</label>
	                <input name="dest" type="text" id="dest" size="30" maxlength="50">
	                <br />
	                <label for="subj">Titulo</label>
	                <input name="subj" type="text" id="subj" size="50" maxlength="100" value="$mc4">
	                <input name="sendAndminMail" type="hidden" value="true">
	              </p>
	              <div class="zw_bodyMail" style="width:100%;height:130%;background-color:">
	                <div class="body">
	                	<a href="javascript:;" onmousedown="jQ('#msgedit').tinymce().execCommand('mceInsertContent',false,'<b>Hello world!!</b>');">[Insert HTML]</a>
						$mailheader
						<textarea id="msgedit" class="msgedit" name="msgedit" cols="40" rows="20" wrap="soft" style="width: 700px;">$mailbody</textarea>
						$mailfooter
				  </div>
				  <input type="hidden" name="sendAndminMail" />
				</div>
	            <input name="submit" type="submit" />
	  </form>
EOD;
		return $ret;  
	}
}
$cls_adm = new Admin();
/*
if (isset($_GET['a'])) {
	$as = $_GET['a'];
} else {
	$as = '';
}
*/	
$as = $cls_adm->a;

function xmlPanel() {
	global $cls_adm;
	return $cls_adm->srcPanel();
}

function generatePassword($length=9, $strength=0) {
	$vowels = 'ZkWxYrRgSmXKP';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
		$consonants .= '@#$%&?!_([}';
	}

	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}
function selIdiForm($idis) {
	$selen = ''; $seles = ''; $selfr = ''; $selde = ''; $selit = ''; $selca = ''; $selja = ''; $selzh = ''; $sel00 = '';
	$sel = 'selected';
	if ($idis == 'es') { $seles = $sel; }
	if ($idis == 'en') { $selen = $sel; }
	if ($idis == 'fr') { $selfr = $sel; }
	if ($idis == 'de') { $selde = $sel; }
	if ($idis == 'it') { $selit = $sel; }
	if ($idis == 'ca') { $selca = $sel; }
	if ($idis == 'ja') { $selja = $sel; }
	if ($idis == 'zh') { $selzh = $sel; }
	if ($idis == '00') { $sel00 = $sel; }
	$cnt ='
	<select name="idi">
		<option value="es" '.$seles.'>spanish</option>
		<option value="en" '.$selen.'>english</option>
		<option value="fr" '.$selfr.'>french</option>
		<option value="de" '.$selde.'>dutch</option>
		<option value="ca" '.$selca.'>catalan</option>
		<option value="it" '.$selit.'>italian</option>
		<option value="zh" '.$selzh.'>chinesse</option>
		<option value="ja" '.$selja.'>japanesse</option>
		<option value="00" '.$sel00.'>global</option>
	</select>
	';
	return $cnt;
}
function admEdit($idcnt, $tip) {
	global $cls_adm;
	return $cls_adm->admEdit($idcnt, $tip);
}
/*
<a href="javascript:;" onmousedown="$(\'#cnt\').tinymce().hide();">[HTML]</a> <a href="javascript:;" onmousedown="$(\'#cnt\').tinymce().show();">[WYSIWYG]</a>
<a href="javascript:;" onmousedown="$('#content').tinymce().execCommand('Bold');">[Bold]</a>
<a href="javascript:;" onmousedown="alert($('#content').html());">[Get contents]</a>
<a href="javascript:;" onmousedown="alert($('#content').tinymce().selection.getContent());">[Get selected HTML]</a>
<a href="javascript:;" onmousedown="alert($('#content').tinymce().selection.getContent({format : 'text'}));">[Get selected text]</a>
<a href="javascript:;" onmousedown="alert($('#content').tinymce().selection.getNode().nodeName);">[Get selected element]</a>
<a href="javascript:;" onmousedown="$('#content').tinymce().execCommand('mceInsertContent',false,'<b>Hello world!!</b>');">[Insert HTML]</a>
<a href="javascript:;" onmousedown="$('#content').tinymce().execCommand('mceReplaceContent',false,'<b>{$selection}</b>');">[Replace selection]</a>
*/
