<?php

/**
 * Crear lista de seleccion
 */
function selGiro($cat = '') {
	$result = mysql_query("SELECT cid, pid, title FROM dir_cat WHERE pid=0 ORDER BY title");
	$giro = '		<select name="'.$cat.'">
			<option value="0" selected="selected">&mdash;&mdash;&mdash;</option>';
	while(list($cid, $pid, $title) = @mysql_fetch_row($result)) {
		$giro .= '<option value="'.$cid.'">&nbsp;'.textoEnIdioma(camCod($title)).'</option>
		';
		$resulta = @mysql_query("SELECT cid, pid, title FROM dir_cat WHERE pid=".$cid." ORDER BY title");
		while(list($scid, $spid, $stitle) = @mysql_fetch_row($resulta)) {
			$giro .= '<option value="'.$scid.'">&mdash;&mdash;&nbsp;'.textoEnIdioma(camCod($stitle)).'</option>
			';
		}

	}
	$giro .= '</select>';
	return $giro;
}

$pub = time()-3600*24*30; // Hoy - 30 dias

function selTipo($tip) {
	global $idi;
	$sel = '
		<select name="'.$tip.'">
			<option value="0" selected="selected">&mdash;&mdash;&mdash;</option>
			';
	while ( list($k, $v) = each($idi['cla_tip']) ) {
		$k++;
    	$sel .= "		<option value=\"$k\">$v</option>\n";
    }
	$sel .= '	</select>';
	return $sel;
}

if (isset($sz) && $sz == $idi['uri2'][1]) {
	// Agregar nuevo clasificado
    if(isset($_POST['env']) && ($cid > 0) && !empty($tcl)) {
    	$sql = sprintf("INSERT INTO %s (lid, cid, cid2, title, prop, city, state, phone, email, ip, des, prec, mon, status, date, hits) VALUES (%u, %u, %u, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',%u, '%s', %u, %u, %u)", "cla_avi", 0, $cid, 0, $tcl, $pdc, $adn, $edn, $tdn, $emc, $_SERVER['REMOTE_ADDR'], $dcl, $prec, $mon, 0, time(), 0);
		mysql_query($sql);
		
		echo '<div class="frm"><h2>Gracias por agregar su aviso</h2>
		<p>Ahora su aviso clasificado estar&aacute; disponible durante los proximos 30 dias*</p> <p><em>*Nos reservamos el derecho de publicar o eliminar los listados a&ntilde;adidos a los clasificados</em></p>';
		echo '<p>'.cUrl($idi['uri'][2], '', 'no', '&nbsp;Haga clic aqui para volver al listado&nbsp;', '', '').'</p></div>';
	} else {
	// Formulario Agregar nuevo clasificado
    ?>
<script type="text/javascript" src="<?php
		echo $cb ['url'];
		?>js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		mode : "exact",
		elements : "dcl",
		theme : "simple",
	});
</script>
    <h2 class="ndn"><?php echo $idi['str_cla']['anc']; ?></h2>
    <div class="frm"><form name="nn" method="post" action="" enctype="multipart/form-data">
      <br />
    <?php echo selTipo('cid'); ?><label> <input name="tcl" type="text" value="" size="50" maxlength="250"></label><br />
    <label><?php echo $idi['str_cla']['prec']; ?> <input name="prec" type="text" value="" size="10" maxlength="15">&nbsp;&nbsp;<select name="mon">
			<option value="MXN" selected="selected">MXN - Peso</option><option value="USD">USD - Dolar</option></select></label><br />
    <label><?php echo $idi['str_cla']['conc']; ?> <input name="pdc" type="text" value="" size="40" maxlength="250"></label>
      <br />
    <label><?php echo $idi['str_dir']['ciu']; ?> <input name="adn" type="text" value="" size="20" maxlength="30"></label>&nbsp;&nbsp;<label><?php echo $idi['str_dir']['est']; ?> <input name="edn" type="text" value="" size="15" maxlength="20"></label><br />
    <label><?php echo $idi['str_dir']['tel']; ?> <input name="tdn" type="text" value="" size="15" maxlength="20"></label><br />
    <label>E-Mail <input name="emc" type="text" value="" size="30" maxlength="250"></label><br />
    <label><?php echo $idi['str_dir']['des']; ?> <br /><textarea name="dcl" cols="50" rows="10" wrap="physical" id="dcl"></textarea></label>
    <br /><br />
	<input type="hidden" name="env" />
	<input name="sub" type="submit" value="<?php echo $idi['str']['env']; ?>" /> 
    <input name="vac" type="reset" value="<?php echo $idi['str']['vac']; ?>" /><br /><br />
    </div>
    </form>
    <?php
	}

} elseif (isset($sz) && strstr($sz, '~')) {
	// Listado completo de negocio, con descripcion
	$lid = str_replace($idi['uri1'][0], '', $sz);
	$lid = (int) $lid;

	// Contar visitas
	regCont(2, $lid, 'cla_avi');
	
	//  lid,  cid,  cid2,   title,  prop,  city,  state,  phone,  email,  ip,  des,  status,  date,  hits
	// $lid, $cid, $cid2, $ltitle, $prop, $city, $state, $phone, $email, $ip, $des, $status, $time, $hits
	$result = mysql_query("SELECT l.lid, l.cid, l.cid2, l.title, l.prop, l.city, l.state, l.phone, l.email, l.ip, l.des, l.status, l.date, l.hits FROM cla_avi l WHERE l.lid=$lid AND date>".$pub);
	list($lid, $cid, $cid2, $ltitle, $prop, $city, $state, $phone, $email, $ip, $des, $status, $time, $hits) = @mysql_fetch_row($result);
	$ltitle = textoEnIdioma($ltitle);
	$desc = textoEnIdioma($des);
	
	$tip = (int)$cid;
	
	
	echo '
	<div class="inf">
	<h2><em>'.$idi['cla_tip'][$cid-1].'</em> '.$ltitle.'</h2>
	<br />
	'.
	$desc.'
	';
	
	echo '
		<div class="dat">
			<br /><strong>'.$idi['str_cla']['conc'].':</strong> '.$prop.'
			<br />'.$city.', '.$state.' '; if($phone != '') { echo '
			<br /><strong>'.$idi['str_dir']['tel'].'</strong> '.$phone; } echo '
			<br /><strong>'.$idi['str_dir']['vis'].':</strong> '.$hits;
			echo '
		</div>
	<br />'.$publik['tor_enc'].'<br />';
	if (!empty($email) || $email != '') {
		include_once ZW_DIR.'sis/eml.php';
	}
	echo '
	</div>
	';
} else {
/**
 * Listado de clasificados, por pagina, categoria y ciudad
 */
	// paginas
	if (isset($pag)) {
		$sqlr = (int)$pag.'0' - 10;
		$sqlr .= ',';
	    } else { $sqlr = ''; $pag = 1; }
	// ciudad
	if(isset($_GET['a'])) {
		$c_rem = array(' ');
		$c_ori = array('_');
		$ciu = $_GET['a'];
		$set_ciu = "city LIKE '$ciu' AND ";
	} else { $set_ciu = ''; }
	$sql = mysql_query("SELECT * FROM cla_avi WHERE $set_ciu date>$pub ORDER BY date DESC LIMIT $sqlr 10");

	// Barra de navegacion
	$url = $cb['url'].'?i='.$idi['idi'].'&amp;z='.mb_strtolower($idi['uri'][2]);
	$navbar = navbar( 'cla_avi', $set_ciu.'date>'.$pub, $url);

	echo $publik['bar_enc'];
	echo '<div class="btn">';
	echo cUrl($idi['uri'][2], $idi['uri2'][1], 'no', $idi['str_cla']['anc']);
	echo '</div>';
	echo '<div class="bms">'.$idi['str_cla']['acd'].'</div>
	<br />
	';

	// Lista de clasificados
	echo '<ul>';
	$NumNeg = (int)$pag.'0' - 9;
	$cp=0;
	while($fila = @mysql_fetch_array($sql)) {
		$nom_neg = textoEnIdioma($fila['title']);
		//$nom_neg = utf8_decode($nom_neg);
		$cp++;
		$tip = (int)$fila['cid'];
	echo '
	<li>
			<span>'.$NumNeg++.'</span>
			<div class="dat">
				 <div class="tip">'.$idi['cla_tip'][$tip-1].'</div> '.cUrl($idi['uri'][2], $fila['lid'].'~'.$nom_neg, 'no', $nom_neg, 'ndn');
				 if (esAdmin()) {
						echo ' '.cUrl('admin', 'boc'.$fila['lid'], 'no', '[b]', '');
					}
				 echo ' 
				<br /><strong>'.$idi['str_cla']['con'].':</strong> '.$fila['prop'].' <strong>'.$idi['str_dir']['tel'].':</strong> '.$fila['phone'].'
				<br />'.$fila['city'].', '.$fila['state'].' <strong>'.$idi['str_dir']['vis'].':</strong> '.$fila['hits'].'
			</div>
	</li>
	';
		/*
		 *  Publucidad banner 760x90 pixeles
		 */
		if ($cp == 5) { echo '
	</ul><br />'.$publik['tor_enc'].'<br /><ul>'; }
	}
	echo '</ul>';
if ($cp <= 5) { echo '<br />'.$publik['tor_enc'].'<br />'; }
}
?>
