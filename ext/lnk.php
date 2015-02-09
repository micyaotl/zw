<?php
/**
 * @package ZihWeb CMS
 * @subpackage Links
 * @version 0.3
 */

class extLnk {
	var $cont;
	var $urluri;
	var $ruri;
	
	public function __construct() {
		global $cls_cfg,$cls_cnt;
		$ruri = $cls_cfg->ruri;
		$nvlnkf = $exthdx = $addlnk = $tblxst = $upldimg = '';
		$this->urluri = ZW_URL.$ruri;
		$cls_cfg->query ( "CREATE TABLE IF NOT EXISTS `".TBLPRE."cnt_lnk` (
		  `id` int(5) NOT NULL AUTO_INCREMENT,
		  `tit` varchar(300) NOT NULL,
		  `txt` text NOT NULL,
		  `url` varchar(255) NOT NULL,
		  `vis` int(11) NOT NULL,
		  `val` varchar(5) NOT NULL,
		  `pos` int(3) NOT NULL,
		  `img` varchar(256) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `url` (`url`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ");
		
		if (isset($_POST['nvlnk']) && WASA && $_POST['tit'] != '' && $_POST['lurl'] != '') {
			if(!empty($_FILES)) {
				$tempFile = $_FILES['viw']['tmp_name'];
				$targetPath = ZW_DIRC.'img'.Ds.'links'.Ds;
				$upldimg = str_replace('_', '-', $_FILES['viw']['name']);
				$targetFile =  str_replace('//','/',$targetPath). $upldimg;
				//$fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
				//$fileTypes  = str_replace(';','|',$fileTypes);
				$typesArray = explode('|','jpg|png|gif');
				$fileParts  = pathinfo($upldimg);
				@mkdir(str_replace('//','/',$targetPath), 0755, true);
				@move_uploaded_file($tempFile,$targetFile);
			}
			$lurl = fixSql($_POST['lurl']);
			$sql = sprintf("INSERT INTO %s (`id`, `tit`, `txt`, `url`, `vis`, `val`, `pos`, `img`) VALUES (%u, %s, %s, %s, %u, %u, %u, %s)",
					TBLPRE.'cnt_lnk', 0, fixSql($_POST['tit']), fixSql($_POST['txt']), $lurl, 0, 0, 0, fixSql($upldimg));
			//if (mysql_num_rows('SELECT * FROM '.TBLPRE.'cnt_lnk WHERE `url`=='.$lurl) == 0) {
			$cls_cfg->query($sql);
			//}
		}
		
		if (isset($_POST['edlnk']) && WASA) {
			$sql = sprintf("UPDATE %s SET `tit` = %s,
			`txt` = %s,
			`url` = %s WHERE id = %u",
			TBLPRE.'cnt_lnk', fixSql($_POST['tit']), fixSql($_POST['txt']), fixSql($_POST['lurl']), $_GET['edita'] );
			  $cls_cfg->query($sql);
		}
		
		if (isset($_GET['borrar']) && WASA) {
			$sql = sprintf("DELETE FROM %s WHERE `id` = %u",
			TBLPRE.'cnt_lnk', $_GET['borrar'] );
			  $cls_cfg->query($sql);
		}
		/*
		 * $query = 'SELECT * FROM '.TBLPRE.'cnt_inm WHERE uri = '.fixSql($cls_cfg->fldr[1]).' LIMIT 1';
		$query = $cls_cfg->query($query);
		$this->cont = $cls_cfg->fetch($query, 'assoc');
		 */
	}
	public function &exthdx() {
		global $cls_cfg;

		$exthdx = '<!-- ext hdx -->';
		if (isset($cls_cfg->gcfg['g-hdx'])) {
			$exthdx .= $cls_cfg->gcfg['g-hdx'];
		}
		$exthdx .='
			<script>
			//<![CDATA[
			jQ(document).ready(function(){
				jQ(\'input[title]\').each(function(){
					if (jQ(this).val() === \'\') {
						jQ(this).val(jQ(this).attr(\'title\'));
					}
					jQ(this).focus(function(){
						if (jQ(this).val() === jQ(this).attr(\'title\')) {
							jQ(this).val(\'\').addClass(\'focused\');
						}
					});
					
					jQ(this).blur(function(){
						if (jQ(this).val() === \'\') {
							jQ(this).val(jQ(this).attr(\'title\')).removeClass(\'focused\');
						}
					});
				});
			});
			//]]>
			</script>
			<style> dt { clear: both; } </style>
			';
		return $exthdx;
	
	}
	
	public function &extcnt($pag) {
		global $cls_cfg,$cls_cnt;
		
		if (!isset($pag)) { $pag = 1; } 
		
		if (!isset($pag) || $pag <= 1) {
			$sql = 'SELECT * FROM '.TBLPRE.'cnt_lnk ORDER BY id DESC LIMIT 0, 10';
		} else {
			$pact = $pag-1;
			$sql = 'SELECT * FROM '.TBLPRE.'cnt_lnk ORDER BY id DESC LIMIT '.$pact.'0, 10';
		}
		
		$sql = $cls_cfg->query($sql);
				
		$lstlnk = '<dl class="lnkshr">';
		while( $fila = $cls_cfg->fetch($sql, 'array') ) {
			//$fila['img'] == '' ? $imf = '' : $imf = '<img src="'.ZW_URL.'webimg/150x200.links_'.$fila['img'].'" style="float:left">';
			$imf = '<img src="http://open.thumbshots.org/image.aspx?url='.$fila['url'].'" style="float:left;border:none;" />';
			$lstlnk .= '
			<dt><a href="'.$fila['url'].'" target="_blank" class="ltit">'.$imf.''.$fila['tit'].'</a>';
			if (esAdmin()) {
				$lstlnk .= '
				<a href="'.$this->urluri.'?borrar='.$fila['id'].'" style="float:right;" class="ui-state-default ui-corner-all ui-icon ui-icon-trash">borrar</a>
				<a href="'.$this->urluri.'?edita='.$fila['id'].'" style="float:right;" class="ui-state-default ui-corner-all ui-icon ui-icon-pencil">editar</a>';
			}
			$lstlnk .= '';
			$lstlnk .= '</dt>
			<dd><span class="ldes">'.$fila['txt'].'</span><br />
			<a href="'.$fila['url'].'" class="lurl">'.$fila['url'].'</a>
			</dd>
			';
		}
		$lstlnk .= '</dl>';
		
		$navbar = navbar(TBLPRE.'cnt_lnk', 'pos = 0', ZW_URL.$cls_cfg->ruri, 10);
		$nvlnkf = $this->nvlnkf();
		
		$extcnt = <<< EOPAGE
		
		$lstlnk
		$navbar
		<a href="http://www.thumbshots.com" target="_blank" title="" class="lnkthu">Thumbnail Screenshots by Thumbshots</a>
		$nvlnkf
		<p style="clear: both;">&nbsp;</p>
EOPAGE;
$this->cont['title'] = $cls_cnt->cont['tit'];
		return $extcnt;
	}
	private function nvlnkf () {
		global $cls_cfg;
		if ($cls_cfg->esAdmin()) {
			$formtitle = 'Nuevo link';
			$valurl = $valtxt = $valtit = '';
			$elnk = '<input name="nvlnk" type="hidden" value="">';
			if (isset($_GET['edita'])) {
				$sql = 'SELECT * FROM '.TBLPRE.'cnt_lnk WHERE id = '.$_GET['edita'].' LIMIT 1';
				$sql = $cls_cfg->query($sql);
				$fila = $cls_cfg->fetch($sql, 'array');
				$formtitle = 'Editar link';
				$valtit = ' value="'.$fila['tit'].'"';
				$valtxt = $fila['txt'];
				$valurl = ' value="'.$fila['url'].'"';
				$elnk = '<input name="edlnk" type="hidden" value="'.$fila['id'].'">';
			}
			$nvlnkf = '<form action="" method="post" enctype="multipart/form-data">
			<h4>'.$formtitle.'</h4>
			<input name="tit" type="text" title="titulo" size="30"'.$valtit.'><br />
			<textarea name="txt" cols="20" rows="8">'.$valtxt.'</textarea><br />
			<input name="lurl" type="text" title="http://url" size="40"'.$valurl.'><br />
			<input name="viw" type="file"><br />
			<input name="snd" type="submit">
			'.$elnk.'
			</form>
			';
			return $nvlnkf;
		}
	}
}
/*
if (!function_exists('extLnk')) {
	function extLnk($tip = 'cnt'){
		$extcnt = <<< EOPAGE
	<h1>Link Sharing</h1>
	$lstlnk
	$navbar
	<a href="http://www.thumbshots.com" target="_blank" title="Thumbnails Screenshots by Thumbshots">Thumbnail Screenshots by Thumbshots</a>
	$nvlnkf
	<p style="clear: both;">&nbsp;</p>
EOPAGE;
	return $extcnt;
	}
}*/