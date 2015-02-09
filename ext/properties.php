<?php
/*
 * ZihWeb CMS Extension
 * 
 * extProperties v. 0.7
 * Copyright © 2012
 * 
 */

$idi = array(
	'es_amenities' => array(
		'amc' => 'Amueblado',
		'ams' => 'Semi-Amueblado',
		'aic' => 'Aire Acondicionado',
		'cab' => 'Cable',
		'int' => 'Internet',
		'agc' => 'Agua Caliente',
		'seg' => 'Seguridad Habitacional',
		'ter' => 'Terraza',
		'ocv' => 'Ocean View',
		'obf' => 'Beach Front',
		'znc' => 'Centrico',
		'jrd' => 'Jardin',
		'gym' => 'Gimnasio',
		'est' => 'Estacionamiento',
		'ven' => 'Ventiladores',
		'dre' => 'Drenaje',
		'fos' => 'Fosa septica',
		'eco' => 'Eco-Amigable',
		'tip' => 'Titulo de propiedad',
	),
	
	'en_amenities' => array(
		'amc' => 'Furnished',
		'ams' => 'Semi-Furnished',
		'aic' => 'Air Conditioning',
		'cab' => 'Cable',
		'int' => 'Internet',
		'agc' => 'Hot Water',
		'seg' => 'Private Safety',
		'ter' => 'Terrce',
		'ocv' => 'Ocean View',
		'obf' => 'Beach Front',
		'znc' => 'Downtown area',
		'jrd' => 'Garden',
		'gym' => 'Gym',
		'est' => 'Parking',
		'ven' => 'Fans',
		'dre' => 'Drainage',
		'fos' => 'Fosa septica',
		'eco' => 'Ecofriendly',
		'tip' => 'Titulo de propiedad',
	),
);

if(file_exists('./properties-idi.php')) {
	include './properties-idi.php';
}
class extProperties {
	var $cont;
	var $tit;
	var $inm_cities;
	var $inm_trato;
	var $sendrel;
	var $uri = array('config','relators');
	
	public function __construct() {
		global $cls_cfg, $cls_cnt;
		
		// Operational cities list
		$this->inm_cities = array(
		'cancun' => 'Cancún',
		'playac' => 'Playa del Carmen',
		'tulumq' => 'Tulum',
		'islamu' => 'Isla Mujeres',
		'cozume' => 'Cozumel',
		'pmorel' => 'Puerto Morelos',
		'pavent' => 'Puerto Aventuras',
		'cobaqu' => 'Cobá',
		'mahahu' => 'Mahahual',
		'holbox' => 'Hol-Box',
		'zihgro' => 'Ixtapa-Zihuatanejo',
		);
		
		// Amenities of the propertie
		$this->inm_amenities = array(
		'amc' => 'Amueblado',
		'ams' => 'Semi-Amueblado',
		'aic' => 'Aire Acondicionado',
		'cab' => 'Cable',
		'int' => 'Internet',
		'agc' => 'Agua Caliente',
		'seg' => 'Seguridad Habitacional',
		'ter' => 'Terraza',
		'ocv' => 'Ocean View',
		'obf' => 'Beach Front',
		'znc' => 'Centrico',
		'jrd' => 'Jardin',
		'gym' => 'Gimnasio',
		'est' => 'Estacionamiento',
		'ven' => 'Ventiladores',
		'dre' => 'Drenaje',
		'fos' => 'Fosa septica',
		'eco' => 'Eco-Amigable',
		'tip' => 'Titulo de propiedad',
		);
		
		$this->inm_trato = array(
		'venta' => 'Venta',
		'renta' => 'Renta',
		'cesio' => 'Cesion de derechos',
		'titul' => 'Titulo de propiedad',
		'finan' => 'Financiamiento',
		);
		
		// Tipo de inmueble en el listado.
		$this->inm_tipo = array(
		'ter' => 'Terreno',
		'cas' => 'Casa',
		'apa' => 'Apartamento',
		'con' => 'Condominio',
		'cua' => 'Cuarto',
		'est' => 'Estudio',
		'lot' => 'Lote',
		'loh' => 'Lote Hotelero',
		'lor' => 'Lote Residencial',
		'loc' => 'Local Comercial',
		'ofi' => 'Oficina',
		'cns' => 'Consultorio',
		'edi' => 'Edificio',
		'hot' => 'Hotel',
		'pla' => 'Plaza Comercial',
		'vil' => 'Villa',
		'ran' => 'Rancho',
		'yat' => 'Yate',
		'cnt' => 'Contenedor',
		'bod' => 'Bodega',
		);
		
		/**
		 * Tabla de Inmuebles
		 * @var unknown_type
		 */
		$sql = "CREATE TABLE IF NOT EXISTS `".TBLPRE."cnt_inm` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `sid` varchar(5) NOT NULL,
  `rid` int(5) NOT NULL DEFAULT '0',
  `uri` varchar(50) NOT NULL,
  `tit` text NOT NULL,
  `dir` text NOT NULL,
  `ciu` varchar(50) NOT NULL,
  `est` varchar(50) NOT NULL,
  `des` text NOT NULL,
  `ser` text NOT NULL,
  `pre` varchar(20) NOT NULL,
  `med` varchar(20) NOT NULL,
  `lat` float(12,8) NOT NULL,
  `lon` float(12,8) NOT NULL,
  `vis` int(11) NOT NULL DEFAULT '0',
  `web` varchar(255) NOT NULL,
  `sts` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0";
	$cls_cfg->query ( $sql );
	
	$sql = "CREATE TABLE IF NOT EXISTS `".TBLPRE."cnt_inm_amn` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `idi` varchar(5) NOT NULL,
  `aid` varchar(5) NOT NULL,
  `tit` varchar(100) NOT NULL,
  `uri` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;";
	$cls_cfg->query($sql);
	
	$sql = "CREATE TABLE IF NOT EXISTS `".TBLPRE."cnt_inm_rel` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `idt` varchar(20) NOT NULL,
  `tit` varchar(100) NOT NULL,
  `des` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`idt`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;";
	$cls_cfg->query($sql);
		
		if (isset($cls_cfg->fldr[1])) {
			$query = 'SELECT * FROM '.TBLPRE.'cnt_inm WHERE uri = '.fixSql($cls_cfg->fldr[1]).' LIMIT 1';
			$query = $cls_cfg->query($query);
			$this->cont = $cls_cfg->fetch($query, 'assoc');
		} elseif (isset($cls_cfg->fldr[1]) && $cls_cfg->fldr[1] == $this->uri[1]) {
			//echo 'sdasd';
		} else {
			$meta = $cls_cnt->meta;
		}
		//$this->sendrel = 0;
		$this->sendrel++;
	}

	/**
	 * Insertar inmueble
	 */
	private function creInmueble($iid=0, $cid, $rid, $uri, $tit, $dir, $ciu, $est, $des, $ser, $pre, $med, $web) {
		global $zihweb, $cls_cfg;
		/*$sql = sprintf("INSERT INTO %s (id, cid, rid, tip, tit, dir, ciu, est, des, ser, pre, med, vis, web, sts)
							    VALUES (%u, %u, %u, '%s', '%s', '%s', '%s', '%s', '%s', '%s', %s, '%s', %u, '%s', '%u')",
						 TBLPRE.'cnt_inm', $iid, $cid, $rid, $tip, $tit, $dir, $ciu, $est, $des, $ser, $pre, $med, 0, $web, 1);
		*/
		$tpr = TBLPRE.'cnt_inm'; //SET NAMES 'utf8';
		$ser = stripslashes($ser);
		$uri = $zihweb->fixUri($uri);
		$sql = "INSERT INTO `$tpr` (`id`, `cid`, `rid`, `uri`, `tit`, `dir`, `ciu`, `est`, `des`, `ser`, `pre`, `med`, `vis`, `web`, `sts`) 
				VALUES ('$iid', '$cid', '$rid', '$uri', '$tit', '$dir', '$ciu', '$est', '$des', '$ser', '$pre', '$med', '0', '$web', '1')";
		$cls_cfg->query($sql);
	}
	
	public function &exthdx() {
		global $zihweb, $cls_cfg, $lib;
		if (isset($_POST['sendrel']) && $cls_cfg->esAdmin() && $this->sendrel == 1) {
				ob_start();
				$cls_cfg->query("INSERT INTO ".TBLPRE."cnt_inm_rel (id, idt, tit, des) VALUES (0, ".fixSql($_POST['uri']).", ".fixSql($_POST['tit']).", ".fixSql(serialize($_POST['des'])).")");
				ob_end_clean();
			}
		if (isset($_POST['edinm']) && $cls_cfg->esAdmin()) {
			$sql = sprintf("UPDATE %s SET
			`cid` = %s, `rid` = %s, `uri` = %s,
			`tit` = %s, `dir` = %s,
			`ciu` = %s, `est` = %s,
			`des` = %s, `ser` = %s,
			`pre` = %s, `med` = %s, `web` = %s
			 WHERE id = %u",
			TBLPRE.'cnt_inm',
			fixSql($_POST['cid']), fixSql($_POST['rid']), fixSql($zihweb->fixUri($_POST['inmuri'])),
			fixSql($_POST['inmtit']),fixSql($_POST['dir']),
			fixSql($_POST['ciu']), fixSql($_POST['est']), 
			fixSql($_POST['des']), fixSql(serialize($_POST['ser'])),
			fixSql($_POST['pre']), fixSql($_POST['med']), fixSql($_POST['web']),
			$_GET['edita']);
			$cls_cfg->query($sql);
		}

		if (isset($_POST['nvinm']) && $cls_cfg->esAdmin()) {
			$this->creInmueble(0, $_POST['cid'], $_POST['rid'] , urlencode($_POST['inmtit']), $_POST['inmtit'], $_POST['dir'], $_POST['ciu'], $_POST['est'], $_POST['des'], serialize($_POST['ser']), $_POST['pre'], $_POST['med'], $_POST['web']);
		}
		if (isset($_GET['borri']) && $cls_cfg->esAdmin()) {
			$cls_cfg->query('DELETE FROM `'.TBLPRE.'cnt_inm` WHERE `id` = '.$_GET['borri']);
		}
		
			$uldfil = ZW_URL.'upload';
			$cancel = $lib.'cancel';
			if (isset($_GET['edita'])) { $editid = $_GET['edita']; } else { $editid = ''; };
		$exthdx = '';
		if ($cls_cfg->esAdmin()){
			//$cls_cfg->gcfg['jqp'] .= ',uploadify';
			/* '
	<script src="'.$lib.'jquery.uploadify.js"></script>'.
			 */
			$libj = $lib.'jquery';
			$exthdx .= '
	'.<<< EOPAGE
	<style>
	#fileQueue { border: 1px solid #E5E5E5; margin-bottom: 10px; }
	.uploadifyQueueItem { background-color: #F5F5F5; border: 2px solid #E5E5E5; margin-top: 5px; padding: 10px; }
	.uploadifyError { background-color: #FDE5DD !important; border: 2px solid #FBCBBC !important; }
	.uploadifyQueueItem .cancel { float: right; }
	.uploadifyQueue .completed { background-color: #E5E5E5; }
	.uploadifyProgress { background-color: #E5E5E5; margin-top: 10px; width: 100%; }
	.uploadifyProgressBar { background-color: #0099FF; height: 3px; width: 1px; }';
	</style>
	<script src="$libj.uploadify.js"></script>
	<script>
	//<![CDATA[
	
	jQ( ".navp" ).button({ icons: { primary: "ui-icon-gear", secondary: "ui-icon-triangle-1-s" } });
	
		jQ(document).ready(function() {
			jQ("#uploads").uploadify({
				'uploader'		: '$uldfil.swf',
				'script'		: '$uldfil.php',
				'cancelImg'		: '$cancel.png',
				'folder'		: '/img/properties/$editid',
				'queueID'		: 'fileQueue',
				'fileExt'		: '*.jpg;*.jpeg;*.gif;*.png;*.txt',
				'auto'			: false,
				'multi'			: true,
				//'queueSizeLimit': 0,
				'buttonText'	: 'Buscar Archivos'
			});
		});
	//]]>
	</script>
EOPAGE;
			$exthdx.= viseditor('.dti', 'default', 'jQ');
		}
		//$this->exthdx = $exthdx;
		return $exthdx;
	}
	
	public function &extcnt($pag) {
		global $cls_cfg,$cls_dyn,$cls_cnt,$urluri;
		// Show full listing
		if (isset($this->cont['tit'])) {
			$sql = $cls_cfg->query('SELECT idt, tit, des FROM FROM `'.TBLPRE.'cnt_inm_rel` WHERE `id` = "'.$this->cont['rid'].'"');
			//list($ridt, $rtit, $rdes) = $cls_cfg->fetch($sql);
			$extcnt = '
			<h2 class="lst">'.$rtit.''.$this->cont['tit'].'</h2>
			';
			$extcnt .= <<< EOPAGE
			
EOPAGE;
			if ($cls_cfg->esAdmin()) $extcnt .= '
			<a href="'.$urluri.'?borri='.$this->cont['id'].'" class="del ui-state-default ui-corner-all ui-icon ui-icon-trash fltRgt">x</a>
			<a href="'.$urluri.'?edita='.$this->cont['id'].'" style="float:right;" class="ui-state-default ui-corner-all ui-icon ui-icon-pencil">editar</a>';
			
			$extcnt .= '<em class="lstaddr">'.$this->cont['dir'].'</em>
			 <div class="lstcont">'.$this->cont['des'].'</div>
			 ';
			if (file_exists(ZW_DIRC.'img/properties/'.$this->cont['id'])) {
				$extcnt .= '<h3>Photos of the property</h3>'; 
				$extcnt .= $cls_dyn->thumbs($gal='img/properties/'.$this->cont['id'], $wxh = '100x100', $phs ='n');
			}
		} elseif(isset($cls_cfg->fldr[1]) && $cls_cfg->fldr[1] == 'relators') {
			$this->cont['title'] = 'relators list';
			$formnwrel = $this->relatform();
			
			$extcnt = $formnwrel;
		} elseif (isset($_GET['edita'])) {
			$this->cont['title'] = 'Edit property';
			$nvlnkf = $this->nvlnkf();
			$extcnt = <<< EOPAGE
		$nvlnkf
EOPAGE;
		} else {
			
			// Show listings list
			$lstinm = $this->lstinm($pag);
			$nvlnkf = $this->nvlnkf();
			$extcnt = <<< EOPAGE
		$lstinm
		$nvlnkf
EOPAGE;
			$this->cont['title'] = $cls_cnt->cont['tit'];
		}
		if($cls_cfg->esAdmin()) {
			$nav = '
			<a href="'.ZW_URL.$cls_cfg->fldr[0].'/" class="navp">Up one level</a>
			<a href="'.ZW_URL.$cls_cfg->fldr[0].'/'.$this->uri[1].'/" class="navp">relators</a>
			';
		} else {
			$nav = '';
		}
		return $nav.$extcnt;
	}
	
	private function relatform() {
		global $cls_cfg;
		if ($cls_cfg->esAdmin()) {
			
		$cl = '<ul>';
		$sql = 'SELECT idt, tit FROM '.TBLPRE.'cnt_inm_rel';
		$sql = $cls_cfg->query($sql);
		while($c = $cls_cfg->fetch($sql, 'array')) {
			$cl .= '<li>'.$c['idt'].' · '.$c['tit'].'</li>';
		}
		
		$cl .= '</ul>';
				$formnwrel = $cl.<<< EOPAGE
	<form name="form1" method="post" action="">
	<label for="uri">id/uri</label>
	    <input type="text" name="uri" id="uri"><br>
	<label for="tit">title</label>
	    <input type="text" name="tit" id="tit">
	    <br>
	<label for="eml">e-mail</label>
	    <input type="text" name="des[eml]" id="eml">
	    <br>
	<label for="phon">phone</label>
	    <input type="text" name="des[phon]" id="phon">
	    <br>
	<label for="web">web</label>
	    <input type="text" name="des[web]" id="web">
	    <br>
	<label for="addr">address</label>
	    <input type="text" name="des[addr]" id="addr">
	    <br>
	<label for="des">description</label>
	    <textarea name="des[des]" id="des"></textarea>
	    <br>
	    <input type="submit" name="sendrel" id="send" value="Submit">
	</form>
EOPAGE;
			}
			return $formnwrel;
	}

	private function lstinm($pag) {
		global $cls_cfg, $cls_dyn, $cls_cnt, $zihweb;
		if(!isset($pag) || $pag <= 1) {
			$sql = 'SELECT * FROM '.TBLPRE.'cnt_inm ORDER BY id DESC LIMIT 0, 10';
		} else {
			$pact = $pag-1;
			$sql = 'SELECT * FROM '.TBLPRE.'cnt_inm ORDER BY id DESC LIMIT '.$pact.'0, 10';
		}
		$sql = $cls_cfg->query($sql);
		//$this->inmtipo().
		$urluri = ZW_URL.$cls_cfg->ruri;
		$lstinm = '<dl>';
		while( $fila = @$cls_cfg->fetch($sql, 'array') ) {
			$fid = $fila['id'];
			$fvd = '';//genUri($flia['tit']);
			$lnk_edit = '';
			if ($cls_cfg->esAdmin()) $lnk_edit = '
			<a href="'.$urluri.'?borri='.$fila['id'].'" class="del ui-state-default ui-corner-all ui-icon ui-icon-trash fltRgt">x</a>
			<a href="'.$urluri.'?edita='.$fila['id'].'" style="float:right;" class="ui-state-default ui-corner-all ui-icon ui-icon-pencil">editar</a>';
			/*if (isset($fila['img'])) {
				$imf = '<img src="'.ZW_URL.'webimg/150x200.inm_'.$fila['img'].'" style="float:left">';
			} else {
				$imf = '';
			}*/
			$imf = '';
			if (file_exists(ZW_DIRC.'img/properties/'.$fid)) {
				$imf = $cls_dyn->thumbs($gal='img/properties/'.$fid, $wxh = '100x100', $phs ='n', $lmt = 6);
			}
			$link = $urluri.$fvd.$fila['uri'];
			$fblike = $cls_cnt->fbLike($link, 'recommend');
			$plusone = $cls_cnt->plusOne($med='medium', $con=true, $url = $link);
			$tweet = $cls_cnt->tweet($url=$link);
			
			$lstinm .= '
			<dt class="inm-'.$fid.' lst"><a href="'.$urluri.$fvd.$fila['uri'].'">'.$fila['tit'].'</a></dt>
			<dd class="inm-'.$fid.' lst">'.$lnk_edit.'
			<span class="lst ldes">'.$imf.$fila['des'].'</span>
			<span class="lst ldir">'.$fila['dir'].'</span>
			<span class="lst lciu">'.$this->inm_cities[$fila['ciu']].'</span>
			<a href="'.$fila['web'].'" class="lst lurl">'.$fila['web'].'</a><br />
					'.$fblike.' '.$plusone.' '.$tweet.'
			</dd>
			';
			$imf = '';
		}
		$lstinm .= '</dl>'.N.T;
		$lstinm .= navbar(TBLPRE.'cnt_inm', 'sts = 1', $urluri, 10);
		
		return $lstinm;
	}
	
	/**
	 * Tipo de inmuebles
	 */
	private function inmtipo($tip = 'ul', $sel = '') {
		$inm_tipo = $this->inm_tipo;
		if ($tip == 'ul') {
			$inmtipo = N.T.'<ul class="cid">';
			while (list($key, $val) = each($inm_tipo)) {
				$inmtipo .= N.T.T.'<li><a href="#'.$key.'">'.$val.'</a></li>';
			}
			$inmtipo .= N.T.'</ul>';	
		} else {
			$inmtipo = N.T.'<select name="cid">';
			while (list($key, $val) = each($inm_tipo)) {
				$sels = '';
				if ($key == $sel) $sels = ' selected';
				$inmtipo .= N.T.T.'<option value="'.$key.'"'.$sels.'>'.$val.'</option>';
			}
			$inmtipo .= N.T.'</select>';
		}
		return $inmtipo;
	}
	
	/**
	 * Amenities selection
	 */
	private function amenities($ameni) {
		$inm_amenities = $this->inm_amenities;
		$amenities = '';
		while (list($key, $val) = each($inm_amenities)) {
			$sel = '';
			if ($ameni[$key] == 1) $sel = ' checked';
				$amenities .= N.T.T.T.'<label class="srvcs" style="display:inline;margin-right:20px;">'.$val.' <input name="ser['.$key.']" type="checkbox" value="1"'.$sel.' /></label>';
		}
		return $amenities;
	}
	
	/**
	 * Lista de ciudades
	 */
	private function cities($city) {
		$inm_cities = $this->inm_cities;
		$sel = '';
		$lstciudad = N.T.T.'<select name="ciu">'.N.T;
		while (list($key, $val) = each($inm_cities)) {
			if ($key == $city) $sel = ' selected';
			$lstciudad .= T.T.'<option value="'.$key.'"'.$sel.'>'.$val.'</option>'.N.T;
			$sel = '';
		}
		$lstciudad .= '</select><br />'.N.T;
		return $lstciudad;
	}
	
	/**
	 * Lista de corredores
	 */
	private function relators($rid='') {
		$inm_relator = array(
		1 => 'feelriviera',
		2 => 'realtytulum',
		3 => 'costarealty',
		4 => 'cozumelcapital',
		5 => 'caribbeanteasures',
		);
		$inm_relfull[0] = array( 'feelRiviera.com', 'info@feelriviera.com', 'http://feelriviera.com/',
		'feelRiviera es una compañia con base en Playa del Carmen dedicada a proporcionar servicios en línea de la Riviera Maya.');
		$inm_relfull[1] = array('Realty Tulum', 'info@realtytulum.com', 'http://www.realtytulum.com/',
		'Realty Tulum es una compañia de Bienes Raices de Tulum desarrolladora del Tulum Jungle y promotor de servicios.');
		$inm_relfull[2] = array('Costa Realty', 'info@costarealty.com', 'http://www.costarealty.com/',
		'Costa Realty compañia de Bienes Raices.');
		$inm_relfull[3] = array('Caribbean Teasures', 'caribeteasures@feelriviera.com', 'http://feelriviera.como/caribbeanteasures',
		'Caribbean Teasures Cozumel, Riviera Maya');
		
		$lstrelator = N.T.'<select name="rid">';
			while(list($id, $tit) = each($inm_relator)) {
				if ($id == $rid) $sel = ' selected';
				$lstrelator .= N.T.T.'<option value="'.$id.'"'.$sel.'>'.$tit.'</option>';
				$sel ='';
			}
		$lstrelator .= N.T.'</select>';
		return $lstrelator;
	}
	
	/**
	 * Lista tratos
	 */
	private function tratos($trat) {
		$inm_trato = $this->inm_trato;
		$tratos = N.T.'<select name="est">';
			$sel = '';
			while(list($id, $tit) = each($inm_trato)) {
				if ($id == $trat) $sel = ' selected';
				$tratos .= N.T.T.'<option value="'.$id.'"'.$sel.'>'.$tit.'</option>';
			}
		$tratos .= N.T.'</select>';
		return $tratos;
	}
	
	private function nvlnkf() {
		global $cls_cfg,$cls_dyn,$urluri;
		if ($cls_cfg->esAdmin()) {
			$formtitle = 'Nuevo inmueble';
			$valdir = $valtit = $valpre = $valmed = $valweb = 'value=""';
			$valdes = '';
			$edinm = '';
			$edinm .= '<input name="nvinm" type="hidden" value="">';
			
			// Set $fila temp empty
			$fila = array();
			//
			if (isset($_GET['edita'])) {
				$sql = 'SELECT * FROM '.TBLPRE.'cnt_inm WHERE id = '.$_GET['edita'].' LIMIT 1';
				$sql = $cls_cfg->query($sql);
				$fila = $cls_cfg->fetch($sql, 'array');
				$formtitle = 'Editar inmueble';
				$valtit = ' value="'.$fila['tit'].'"';
				$valuri = ' value="'.$fila['uri'].'"';
				$valdes = $fila['des'];
				$valdir = ' value="'.$fila['dir'].'"';
				$valmed = ' value="'.$fila['med'].'"';
				$valpre = ' value="'.$fila['pre'].'"';
				$valweb = ' value="'.$fila['web'].'"';
				$edinm = '<input name="edinm" type="hidden" value="'.$fila['id'].'">';
			}
			$relators = $this->relators($fila['rid']);
			$tratos = $this->tratos($fila['est']);
			$cities = $this->cities($fila['ciu']);
			$inmtipo = $this->inmtipo('sel', $fila['cid']);
			$amenities = $this->amenities(unserialize($fila['ser']));
			$editi = '';
			if (isset($_GET['edita'])) $editi = $_GET['edita'];
			if (file_exists(ZW_DIRC.'img/properties/'.$_GET['edita'])) {
				$imgs = '<h3>Photos of the property</h3>'; 
				$imgs .= $cls_dyn->thumbs($gal='img/properties/'.$editi, $wxh = '100x100', $phs ='n');
			}
			
			$nvlnkf .= <<< EOPAGE
			<h4>$formtitle</h4>
			<form name="ninm" action="$urluri" method="post" enctype="multipart/form-data">
				$relators
				$tratos
				$inmtipo
				<br />
				<label>Titulo: <input type="text" name="inmtit" size="40" maxleght="255" $valtit></label><br />
				<label>URI: <em>$urluri</em><input type="text" name="inmuri" size="40" maxleght="255" $valuri></label><br />
				<label>Direccion: <input type="text" name="dir" size="40" maxleght="255" $valdir></label><br />
				<label>Ciudad: $cities </label>
				<label>Medidas: <input type="text" name="med" size="10" maxleght="255" $valmed></label>
				<label>Precio: <input type="text" name="pre" size="10" maxleght="255" $valpre></label><br />
				<label>Web: <input type="text" name="web" size="40" maxleght="255" $valweb></label><br />
				<label>Descripcion: </label><br />
				<textarea name="des" cols="40" rows="15" wrap="physical" class="dti">$valdes</textarea><br />
				$amenities
				$edinm
				<br />
				$imgs
				<input name="sub" type="submit" /><br />
			</form>
			
			<div id="fileQueue"><!-- uploader --></div><br />	
			<input id="uploads" name="uploads" type="file" />
			<a href="javascript:$('#uploads').uploadifyUpload();" style="border-radius:3px;background-color:#00ff00;border:solid 1px #33dd33;padding:5px; font-weight:bold;">Upload Files</a>
			<a href="javascript:$('#uploads').uploadifyClearQueue()">Cancel All Uploads</a>
			<div id="currFiles"></div><br />
				
EOPAGE;
			return $nvlnkf;
		}
	}
}
/*
class dynProperties extends Dynamics {
	public function postUpdate() {
		return 'Hola!';
	}
}*/