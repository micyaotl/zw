<?php
define('PEARDIR', dirname(realpath(__FILE__)) . '/PEAR/');
$xml = $req_uri;
$xml = explode('xml/', $req_uri);
$xmlf = str_replace('.xml', '', $xml[1]);
if (strstr($xmlf, '/')) {
	$xmlp = explode('/', $xmlf);
	$xmlf = $xmlp[0];
}
if ($xmlf == 'georss') {
	sendHeaders($ext='atom+xml',$h='200');
} else {
	sendHeaders($ext='xml',$h='200');
}
if (function_exists($xmlf) && count($xmlp) > 0) {
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo $xmlf($xmlp[1]);
} else {
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo $xmlf();
}

function georss() {
	global $gcfg, $cls_cfg;
	$sql = "SELECT * FROM `".TBLPRE."cnt_map` WHERE 1";
	$sql = $cls_cfg->query($sql);
	$ret = '
<feed xmlns="http://www.w3.org/2005/Atom"
 xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
 xmlns:georss="http://www.georss.org/georss"
 xmlns:dc="http://purl.org/dc/elements/1.1/"
 xmlns:media="http://search.yahoo.com/mrss/">
	<title>'.$gcfg['title'].'</title>
	<subtitle></subtitle>
	<link  rel="self" href="'.ZW_URL.'xml/georss"/>
	<link href="'.ZW_URL.'"/>
	<generator uri="http://feelRiviera.com/">'.ZW_V.'</generator>
	<updated></updated>
	<author>
		<name>'.ZW_V.'</name>
		<email>info@feelriviera.com</email>
		<uri>http://feelriviera.com/</uri>
	</author>
	<id>urn:uuid:c84ec740-59b8-4911-2ef5-a5f2198257bb</id>
';
	while ($row = @$cls_cfg->fetch($sql, 'assoc')){
		$desc = unserialize($row['desc']);
		$id = $row['id'];
		$lat = floatval($row['lat']);
		$long = floatval($row['long']);
		$ret.= '
	<entry>
		<title>'.$row['nom'].'</title>
		<link href="'.parseToXML($row['link']).'"/>
		<id>urn:uuid:'.$id.'</id>
		<summary>'.parseToXML($desc['dir']).'</summary>
		<georss:point>'.$lat.' '.$long.'</georss:point>
		<geo:lat>'.$lat.'</geo:lat>
		<geo:long>'.$long.'</geo:long>
		<!-- iconstyle></iconstyle -->
        <icon>'.parseToXML($desc['icon']).'</icon>
		<content type="html">';
		if ($desc['img'] != 'Url de imagen') {
			$imgurl = ZW_URL.'webimg/150x100.'.$desc['img'];
		}
		$ret .= parseToXML('<img src="'.$imgurl.'" width="150" height="100" />
		<br/>'
		.$desc['desc']).'
		</content>
	</entry>';
	}
	$ret.= '
</feed>';
	return $ret;
}

/**
 * XmlSerializer uses a PEAR xml parser to generate an xml response. 
 * this takes a php array and generates an xml according to the following rules:
 * - the root tag name is called "response"
 * - if the current value is a hash, generate a tagname with the key value, recurse inside
 * - if the current value is an array, generated tags with the default value "row"
 * 
 */
class XmlSerializer {

	function XmlSerializer() {
		$this->loadPearClasses();
	}

	function loadPearClasses() {
		if (class_exists('XML_Serializer')) {
			return;
		}
		$deps = array(
			'PEAR' => 'PEAR.php', 
			'XML_Parser' => 'XML/Parser.php', 
			'XML_Parser_Simple' => 'XML/Parser/Simple.php', 
			'XML_RPC' => 'XML/RPC.php', 
			'XML_Util' => 'XML/Util.php', 
			'XML_RPC_Dump' => 'XML/RPC/Dump.php', 
			'XML_RPC_Server' => 'XML/RPC/Server.php', 
			'XML_Serializer' => 'XML/Serializer.php', 
			'XML_Unserializer' => 'XML/Unserializer.php' 
		);
		
		foreach ($deps as $k => $v) {
			if (!class_exists($k)) {
				require_once(PEARDIR . $v);
			}
		}
	}

	function serialize(& $obj) {
		$serializer_options = array (
		   'addDecl' => TRUE,
		   'encoding' => 'UTF-8',
		   'indent' => '	',
		   'defaultTagName' => 'cliente', 
		   'rootName' => 'clientes'
		); 
		$serializer = &new XML_Serializer($serializer_options); 

		// Serialize the data structure
		$status = $serializer->serialize($obj);

		// Check whether serialization worked
		if (PEAR::isError($status)) {
		   die($status->getMessage());
		}
		// Display the XML document
		header('Content-type: text/xml');
		echo $serializer->getSerializedData();
	}
	
	function cliente(& $obj) {
		$serializer_options = array (
		   'addDecl' => FALSE,
		   'encoding' => 'UTF-8',
		   'indent' => '	',
		   'defaultTagName' => 'cliente', 
		   'rootName' => 'clientes'
		); 
		$serializer = &new XML_Serializer($serializer_options); 

		$status = $serializer->serialize($obj);

		// Check whether serialization worked
		if (PEAR::isError($status)) {
		   die($status->getMessage());
		}
		echo $serializer->getSerializedData();
	}
}

function parseToXML($htmlStr) {
	/*$xmlStr=str_replace('<','&lt;',$htmlStr);
	$xmlStr=str_replace('>','&gt;',$xmlStr);
	$xmlStr=str_replace('"','&quot;',$xmlStr);
	$xmlStr=str_replace("'",'&#39;',$xmlStr);*/
	return htmlspecialchars($htmlStr);
}

function menu() {
	global $gcfg, $cfg, $cls_cfg;
	$sql = "SELECT id, sup, uri, idi, meta FROM ".TBLPRE."cnt WHERE sup LIKE '%.0' AND idi = '".$cfg['idi']."' ORDER BY sup";
	$sql = $cls_cfg->query($sql);
	//$sql = $cls_cfg->fetch($sql, 'array');
	$ret.= '
<menu>';
	while($fil = @$cls_cfg->fetch($sql, 'array')) {
		list($pos, $pad) = explode('.', $fil['sup']);
		$filsupid = $fil['id'];
		$meta = unserialize($fil['meta']);

		$sqla = "SELECT id, sup, uri, idi, meta FROM ".TBLPRE."cnt WHERE sup LIKE '%.".$filsupid."' AND idi = '".$cfg['idi']."' ORDER BY sup";
		$sqla = $cls_cfg->query($sqla);
		if ($cls_cfg->numrows($sqla) == 0) {
			$link = ZW_URL.$fil['uri'];
		}  else {
			$link = 'link';
		}
		if($pos != 0) { $ret.= '
	<item label="'.$meta['label'].'" url="'.$link.'">';

		while($filu = $cls_cfg->fetch($sqla, 'array')) {
			$metau = unserialize($filu['meta']);
			$ret.= '
		<subItem url="'.ZW_URL.$filu['uri'].'">'.$metau['label'].'</subItem>';
		}
		$ret.='
	</item>';
		}
	}
	$ret.= '
</menu>';
	return  $ret;
}

function mapa() {
	global $cls_cfg;
	$sql = "SELECT * FROM `".TBLPRE."cnt_map` WHERE 1";
	$sql = $cls_cfg->query($sql);
	$ret = '
<mapa>';
	while ($row = @$cls_cfg->fetch($sql, 'assoc')){
		$desc = unserialize($row['desc']);
		$ret.= '
	<mark id="'.$row['id'].'" ';
		$ret.= 'nom="'.$row['nom'].'" ';
		$ret.= 'tip="'.$row['tip'].'" ';
		$ret.= 'lat="'.$row['lat'].'" ';
		$ret.= 'long="'.$row['long'].'" ';
		$ret.= 'cor="'.$row['cor'].'" ';
		$ret.= 'dir="'.parseToXML($desc['dir']).'" ';
		$ret.= 'desc="'.parseToXML($desc['desc']).'" ';
		$ret.= 'img="'.parseToXML($desc['img']).'" ';
		$ret.= 'link="'.parseToXML($row['link']).'"';
		$ret.= '/>';
	}
	$ret.= '
</mapa>';
	return $ret;
}

function clientes() {
	global $cls_cfg;
	$sql = "SELECT * FROM cnt_nct";
	$sql = $cls_cfg->query($sql);
	$res = $cls_cfg->fetch($sql, 'assoc');
	
	$serializer = new XmlSerializer();
	$res = unserialize();
	echo $serializer->cliente($res);
	
}

function sitemap($idi = '') {
	global $gcfg, $cfg, $cls_cfg;
	if ($idi  == '') $idi = $gcfg['idi'];
	//$sql = "SELECT id, sup, uri, idi, meta FROM ".TBLPRE."cnt WHERE sup LIKE '%.0' AND idi = '".$cfg['idi']."' ORDER BY sup";
	$sql = "SELECT id, sup, uri, idi, meta FROM ".TBLPRE."cnt
			WHERE sup LIKE '%.0' AND idi LIKE '".$idi."' OR idi LIKE '00' ORDER BY sup";
	$sql = $cls_cfg->query($sql);
	$mod = gmdate("Y-m-d\TH:i:s").'+00:00';
	$frq = 'daily';
	$ret.= '
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
	while($fil = @$cls_cfg->fetch($sql, 'array')) {
		list($pos, $pad) = explode('.', $fil['sup']);
		$filsupid = $fil['id'];
		$link = ZW_URL.$fil['uri'];
		if($pos != 0) { $ret.= N.'<url>
	<loc>'.$link.'</loc>
	<lastmod>'.$mod.'</lastmod>
	<changefreq>'.$frq.'</changefreq>
	<priority>0.8</priority>
</url>';
		$sqla = "SELECT id, sup, uri, idi, meta FROM ".TBLPRE."cnt WHERE
				sup LIKE '%.".$filsupid."' AND
				idi = '".$idi."' AND uri NOT LIKE '%.xml' ORDER BY sup";
		$sqla = $cls_cfg->query($sqla);
		while($filu = $cls_cfg->fetch($sqla, 'array')) {
			list($posi, $padi) = explode('.', $filu['sup']);
			if ($posi != 0) {
			//$metau = unserialize($filu['meta']);
			$ret.= N.T.'<url>
		<loc>'.ZW_URL.$filu['uri'].'</loc>
		<lastmod>'.$mod.'</lastmod>
		<changefreq>'.$frq.'</changefreq>
		<priority>0.5</priority>
	</url>';
			}
		}
		$ret.='
';
		}
	}
	$ret.= '
</urlset>';
	return  $ret;
}

function stkimg($gal='tulum_hotels', $dr='img') {
	global $gcfg, $cfg;
	$gal = str_replace('_', Ds, $gal);
	
	// Name of the folder in each album's folder that contains the full size imagery
	$large_folder = '';
	
	// Name of the folder in each album's folder that contains the thumbnails
	$thumb_folder = 'tn';
	
	// Name of your album preview image. Should be placed at the root of each album's folder. (Optional)
	$album_preview_name = 'albumPreview.jpg';
	
	// Set up paths
	$dir = ZW_DIRC.$dr.'/'.$gal.'/';
	$server = ZW_URL;
	$rel_path = $dr.'/'.$gal.'/'; //str_replace('images.php', '', $_SERVER['PHP_SELF']);
	$path = $server. $rel_path;
	//$iptc = is_callable('iptcparse');
	
	// Find all folders in this directory
	$albums = array();

	$d = dir($dir);
	while (false !== ($folder = $d->read())) {
		if ($folder != '.' && $folder != '..' && is_dir($dir . Ds . $folder . Ds . $large_folder)) {
			$albums[] = $folder;
		}
	}
	$d->close();
	
	// Start writing XML
	$o = "\n<gallery>\n";
	
	// Cycle through albums
	foreach($albums as $album) {
		// Path variables
		$loc_path = $path . $album . '/';
		$full_path = $dir . Ds . $album;
		
		// Find images in the large folder
		$images = array();
		$d2 = dir($full_path . Ds . $large_folder);
		while (false !== ($image = $d2->read())) {
			if (eregi('.jpg|.gif|.png|.swf|.flv', $image)) {
				$images[] = $image;
			} 
		}
		$d2->close();
		
		// Only write the album to XML if there are images
		if (!empty($images)) {
			natcasesort($images);
			// Pretty up the title
			$title = ucwords(preg_replace('/_|-/', ' ', $album));

			// See if there is an album thumb present, if so add it in
			if (file_exists($full_path . Ds . $album_preview_name)) {
				$atn = ' tn="' . $loc_path . $album_preview_name . '"';
			} else {
				$atn = '';
			}
			
			// Only write tnPath if that folder exists
			//if (is_dir($full_path . DS . $thumb_folder)) {
				$tn = ' tnPath="' . ZW_URL . 'stock/200x200."';
			//}
			
			// Album tag
			$o .= "\t" . '<album title="' . $title . '" description="'.$large_folder.'" lgPath="' . $loc_path . $large_folder . '"' . $tn . $atn . '>' . "\n";
			
			// Cycle through images, adding tag for each to XML
			foreach($images as $i) {
				$link = $caption = $title = '';
				
				/*if ($iptc) {
					$file = $full_path . DS . $large_folder . DS . $i;
					$path_info = pathinfo($file);
					$extensions = array('jpg', 'jpeg', 'gif', 'png');
					if (in_array(strtolower($path_info['extension']), $extensions)) {
						getimagesize($file, $info);
						if (!empty($info['APP13'])) {
							$iptc = iptcparse($info['APP13']);
							if (isset($iptc['2#005'])) {
								$title = $iptc['2#005'];
								if (is_array($title)) {
									$title = htmlentities($title[0], ENT_COMPAT);
								}
							}
							if (isset($iptc['2#120'])) {
								$caption = $iptc['2#120'];
								if (is_array($caption)) {
									$caption = htmlentities($caption[0], ENT_COMPAT);
								}
							}
						}
					}
				}*/
				
				if (isset($_GET['link'])) { $link = $loc_path . $large_folder . '/' . $i; }
				$o .= "\t\t" . '<img src="' . $i . '" title="' . $i . '" caption="' . $i . '" link="' . $link . '" />' . "\n"; 
			}
			
			// Close the album tag
			$o .= "\t</album>\n";
		}
	}
	
	// Close gallery tag, set header and output XML
	$o .= "</gallery>";
	//header('Content-type: text/xml'); 
	die($o);
}
