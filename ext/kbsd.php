<?php
$exthdx = $addlnk = $tblxst = $upldimg = '';

if (isset($_POST['nvrpt']) && esAdmin() && $_POST['tit'] != '' && $_POST['lurl'] != '') {
	/*if(!empty($_FILES)) {
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
	}*/
	$sql = sprintf("INSERT INTO %s (`id`, `tit`, `txt`, `url`, `vis`, `val`, `pos`, `img`) VALUES (%u, %s, %s, %s, %u, %u, %u, %s)",
	TBLPRE.'cnt_lnk', 0, fixSql($_POST['tit']), fixSql($_POST['txt']), fixSql($_POST['lurl']), 0, 0, 0, fixSql($upldimg));
	$cls_cfg->query($sql);
}

if(!isset($pag) || $pag <= 1) {
	$sql = 'SELECT * FROM '.TBLPRE.'cnt_lnk LIMIT 0, 10';
} else {
	$pact = $pag-1;
	$sql = 'SELECT * FROM '.TBLPRE.'cnt_lnk LIMIT '.$pact.'0, 10';
}
$sql = $cls_cfg->query($sql);
/*$lstlnk = '<dl>';
while( $fila = @mysql_fetch_array($sql) ) {
	$fila['img'] == '' ? $imf = '' : $imf = '<img src="'.ZW_URL.'webimg/150x200.links_'.$fila['img'].'" style="float:left">';
	$lstlnk .= '
	<dt>'.$imf.'<a href="'.$fila['url'].'">'.$fila['tit'].'</a>
	</dt>
	<dd>'.$fila['txt'].'<br />
	<a href="'.$fila['url'].'">'.$fila['url'].'</a>
	</dd>
	';
}
$lstlnk .= '</dl>';*/

$navbar = navbar(TBLPRE.'cnt_lnk', 'pos = 0', ZW_URL.$ruri, 10);
$exthdx = '<!-- ext hdx -->';
if (isset($gcfg['g-hdx'])) {
	$exthdx .= $gcfg['g-hdx'];
}
$exthdx .= '
	<script>
	//<![CDATA[
	$(document).ready(function(){
		$(\'input[title]\').each(function(){
			if ($(this).val() === \'\') {
				$(this).val($(this).attr(\'title\'));
			}
			$(this).focus(function(){
				if ($(this).val() === $(this).attr(\'title\')) {
					$(this).val(\'\').addClass(\'focused\');
				}
			});
			
			$(this).blur(function(){
				if ($(this).val() === \'\') {
					$(this).val($(this).attr(\'title\')).removeClass(\'focused\');
				}
			});
		});
	});
	//]]>
	</script>';

$extcnt = <<< EOPAGE
<h1>Support desk &amp; Knowledge base<sub style="vertical-align:top; font-size:0.5em;">&beta;</sub></h1>
EOPAGE;
if( !esAdmin() && !esClient() ) {
	$extcnt .= '<form action="" method="post" enctype="multipart/form-data">
	<input name="eml" type="text" title="usuario o mail" size="30" maxlength="64" />
	<input name="pas" type="password" title="password" size="30" maxlength="64" />
	<input name="login" type="hidden" />
	<input name="lvl" value="cli" type="hidden" />
	<input name="sub" type="submit" value="login" />
	</form>';
}
/*
 * VISTA CLIENTES
 */
if ( esClient() ) {
	$extcnt .= ' 
<form action="" method="post" enctype="multipart/form-data">
<h4>Nuevo reporte</h4>
<p>Ingrese el titulo o asunto de su reporte.</p>
<input name="tit" type="text" title="titulo" size="30">
<p>Ingrese una descripción detallada del motivo de su reporte.</p>
<textarea name="txt" cols="30" rows="8"></textarea><br />
<p>Incluya la dirección de la pagina que estaba en la que ocurrio el fallo.</p>
<input name="lurl" type="text" title="http://url" size="30">
<p>Fichero adjunto o captura de pantalla</p>
<input name="viw" type="file">
<input name="nvrpt" type="hidden">
<input name="snd" type="submit">
<p></p>
</form>';

}
/*
 * VISTA ADMINS
 */
if( esAdmin() ) {
	$extcnt .= '
	<h4>Reportes Abiertos</h4>
	<p>Ordenar reportes por: <a href="'.ZW_URL.$ruri.'?ob=a">autor</a>  <a href="'.ZW_URL.$ruri.'?ob=f">fecha</a>  <a href="'.ZW_URL.$ruri.'?ob=e">estado</a></p>
';
	/*
<form action="" method="post" enctype="multipart/form-data">
<h4>Nuevo cliente</h4>
<input name="tit" type="text" title="titulo" size="30">
<textarea name="txt" cols="20" rows="8"></textarea>
<input name="lurl" type="text" title="http://url" size="40">
<input name="viw" type="file">
<input name="snd" type="submit">
<input name="nvrpt" type="hidden" value="">
</form>
	 */
}


$extcnt .= <<< EOPAGE
<h3>Comprar Tickets de Soporte</h3>
<p>Tiene su website pero necesita ayuda, solicite 10 tickets de soporte por 70 USD (oferta de vigencia limitada)</p>
<p>Casi como contratar un webmaster para hacer ediciones y correcciones parciales<br>
analisis estadístico, consultas de TI, enlaces entrantes.</p>
<p>O si desea un plan a medida, <a href="http://feelriviera.com/marco-garcia.html">contactame</a> para cotizar.</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="NF6H6G7PQL2VY">
<table>
<tr><td><input type="hidden" name="on0" value="E-Mail de ingreso">E-Mail de ingreso</td></tr><tr><td><input type="text" name="os0" maxlength="60"></td></tr>
</table>
<input type="image" src="https://www.paypal.com/es_XC/MX/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">
<img alt="" border="0" src="https://www.paypal.com/es_XC/i/scr/pixel.gif" width="1" height="1">
</form>
EOPAGE;

if( esAdmin() || esClient() ) {
$extcnt .= '<form action="'.ZW_URL.'" method="post" enctype="multipart/form-data">
<input name="logout" type="hidden" />
<input name="logout" type="submit" value="logout" />
</form>';
}

?>