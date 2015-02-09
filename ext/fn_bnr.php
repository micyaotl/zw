<?php

if (isset($_POST['env'])) {
print_r($_POST);
echo implode(':', $_POST['ser']);
reset($_POST['ser']);
echo '<br />';
while (list($key, $val) = each($_POST['ser'])) {
    $arr = "$key%$val:";
    echo $arr;
}
echo '<br />';
$err = "pri%1:amu%1:ter%1:vma%1:zce%1:";

$arr = explode(":",$err);
print_r($arr);
foreach ($arr as $ser){
	$sar = explode("%",$ser);
	while (list($key, $val) = $sar) {
		//$ser = array();
		$ser[$key] = $val;
	}
}
/*while (list($key, $val) = explode("%",$arr)) {
		//$ser = array();
		$ser[$key] = $val;
	}*/
	//echo $arr;
	print_r($arr);

//print_r(explode(':',$arr));
/*while ( list($key, $val) = explode(':',$arr) ) {
	$arr = array($key => $val);
}*/
echo '<br />';
//print_r($arr);}

}

function preDatos() {
	
}

function creaInmueble($iid, $cid, $rid, $tip, $tit, $dir, $ciu, $est, $des, $ser, $img, $pre, $med, $web, $ema) {
	$sql = sprintf("INSERT INTO cnt_inm (lid, cid, rid, tip, tit, dir, ciu, est, des, ser, img, pre, med, hits, web, ema) VALUES (%u, %u, %u, %u, '%s', '%s', '%s', '%s', '%s', '%s', '%s', %u, '%s', 0, '%s', '%s')", $iid, $cid, $rid, $tip, $tit, $dir, $ciu, $est, $des, $ser, $img, $pre, $med, $web, $ema);
	echo $sql;
	//mysql_query($sql);
}

?>
<script type="text/javascript" src="<?php echo $cb['url']; ?>/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		mode : "exact",
		elements : "dti",
		theme : "simple",
	});
</script>
<form name="ninm" action="" method="post" enctype="multipart/form-data">
<label>Nombre del Desarrollo: <input type="text" name="nom" value="" size="40" maxleght="255"></label><br />
<label>Direccion: <input type="text" name="dir" value="" size="40" maxleght="255"></label><br />
<label>Precio: <input type="text" name="pre" value="" size="10" maxleght="255"></label><br />
<label>Medidas: <input type="text" name="med" value="" size="10" maxleght="255"></label><br />
<label>Descripcion: </label><br /><textarea name="des" cols="40" rows="15" wrap="physical" id="dti"></textarea>
<label>Web: <input type="text" name="web" value="" size="40" maxleght="255"></label><br />
<label>Email: <input type="text" name="ema" value="" size="40" maxleght="255"></label><br />
<label>Privacidad <input name="ser[pri]" type="checkbox" value="1" /></label>
<label>Aire acondicionado <input name="ser[aic]" type="checkbox" value="1" /></label>
<label>Amueblado <input name="ser[amu]" type="checkbox" value="1" /></label>
<label>Terraza <input name="ser[ter]" type="checkbox" value="1" /></label>
<label>Vista al Mar <input name="ser[vma]" type="checkbox" value="1" /></label>
<label>Zona Cenro <input name="ser[zce]" type="checkbox" value="1" /></label>
<label>Jardin <input name="ser[jar]" type="checkbox" value="1" /></label>
<label>Gimnasio <input name="ser[gim]" type="checkbox" value="1" /></label>
<label>Nuevo Desarrollo <input name="ser[nue]" type="checkbox" value="1" /></label>
<label>Vista de la Ciudad <input name="ser[vci]" type="checkbox" value="1" /></label>
<label>Estacionamiento <input name="ser[est]" type="checkbox" value="1" /></label><br />
<label>Internet <input name="ser[int]" type="checkbox" value="1" /></label><br />
<label>Ventiladores <input name="ser[ven]" type="checkbox" value="1" /></label><br />
<input type="hidden" name="env" />
<input name="sub" type="submit" /> <input name="res" type="reset" />
</form>