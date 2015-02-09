<?php
/**
 * Funciones administrativas
 * 
 * @package CntEditor
 * @subpackage idi
 * @version 0.5
 * @author Marco Garcia <micyaotl@gmail.com>
 * @copyright 2009 FeelRiviera.com
 */


class Language {
	public function lngLst() {
		global $cls_cfg;
		$idiomas = $GLOBALS['cfg']['idi'];
		if(isset($cls_cfg->gcfg['idiomas'])) { $idiomas = $GLOBALS['gcfg']['idiomas']; }
		if(strstr($idiomas, ',')) { 
			return explode(',', $idiomas);
		} else {
			return array($idiomas);
		}
	}
	
	public function lngExist() {
		
	}
	
}
function idiDisponible() {
	global $cls_cfg;
	foreach($cls_cfg->idiLst() as $idi) {
		$result[$idi];
	}
	return $result;
}

function cambioIdioma ($i = ''){
	global $cls_cfg;
	$ret = 0;
	if ( ($i != '') && (in_array($i, $cls_cfg->idiLst())) ) {
		$ret = 1;
	}
	return $ret;
}

if ( isset($cls_cfg->idis) && file_exists(ZW_DIR."sis/idi/cfg_".$cls_cfg->idis.".php") ) {
	include ZW_DIR."sis/idi/cfg_".$cls_cfg->idis.".php";
} else {
	include ZW_DIR."sis/idi/cfg_es.php";
}

function textoEnIdioma($texto) {
	global $cls_cfg, $cls_cnt;
	return $cls_cnt->textoEnIdioma($texto);
}

/**
 * Crear vinculo de idioma
 */
function vinCamIdi($sep = ' &middot; ', $dis = 'iso', $tip = 'lnk') {
	global $cls_cfg, $cfg;
	$url = ZW_URL;
	$isor = array('^es^', '^en^', '^fr^');
	$vers = array('Versi&oacute;n en Espa&ntilde;ol', 'English Version', 'Français Version');
	$idin = array('Espa&ntilde;ol', 'English', 'Français');
	$ret = '';
	if (array_count_values($cls_cfg->idiLst()) >= '2') {
		
		if ($tip == 'lnk') {
			$ret = '<span id="idi" class="'.$cls_cfg->idis.'">';
			$ret .= $sep;
			foreach($cls_cfg->idiLst() as $idi) {
				$ret.= '
			<a href="'.$url.$idi.'/" class="i_'.$idi.'" rel="nofollow">';
				if($dis == 'iso') {
					$ret .= $idi;
				} elseif ($dis == 'nombre') {
					$ret .= preg_replace($isor, $idin, $idi);
				} elseif ($dis == 'version') {
					$ret .= preg_replace($isor, $vers, $idi);
				}
				
				$ret .= '</a>'.$sep;
			}
			$ret .= '</span>';
		} elseif ($tip == 'sel') {
		$ret = '<select id="selidi" name="selidi" class="'.$cfg['idi'].'">
		<option name="sel_idi" selected>'.textoEnIdioma('[es]Elige tu Idioma[/es][en]Select your language[/en]').'</option>';
		foreach($cls_cfg->idiLst() as $idi) {
			$ret.= '
		<option name="'.$idi.'" value="'.$idi.'" class="i_'.$idi.'">';
			if($dis == 'iso') {
				$ret .= $idi;
			} elseif ($dis == 'nombre') {
				$ret .= preg_replace($isor, $idin, $idi);
			} elseif ($dis == 'version') {
				$ret .= preg_replace($isor, $vers, $idi);
			}
			
			$ret .= '</option>';
		}
		$ret .= '</select>
<script>
jQ(function(){
      jQ("#selidi").bind("change", function () {
          var url = "'.ZW_URL.'" + jQ(this).val() + "/";
          if (url) {
              window.location = url;
          }
          return false;
      });
});
</script>';
		}
	}
	return $ret;
}

function camCod($txt) {
	//$txt = mb_convert_encoding($txt, "UTF-8");
	//$txt = utf8_encode($txt);
	return $txt;
}