<?php
$gcfg = remTxt($gcfg);
if( preg_match('/\.(css|js|xml)$/i', $ruri)  && $pag_id != '' ):

	if ( preg_match( '/\.(xml)$/i', $ruri ) ) {
		sendHeaders($ext='xml', '200');
	}
	if ( preg_match( '/\.(css)$/i', $ruri ) ) {
		sendHeaders($ext='css', '200');
	}
	if ( preg_match( '/\.(js)$/i', $ruri ) ) {
		sendHeaders($ext='js', '200');
	}
	echo  $cls_cnt->getContent('cnt');
else:
	//if (getContent('id') != '') { sendHeaders('html', '200'); } else { sendHeaders('html', '404'); }
	if (isset($_GET['visual']) && $_GET['visual'] != '' && file_exists($cfg['dir'].'visual'.Ds.$_GET['visual'].Ds.$_GET['visual'].'.php') ) {
		$visual = $_GET['visual'];
		$visd = 'visual/'.$visual.'/';
	}
	if (isset($visual) && $visual != '' && file_exists($cfg['dir'].'visual'.Ds.$visual.Ds.$visual.'.php')):
		include($cfg['dir'].'visual'.Ds.$visual.Ds.$visual.'.php');
	else:
?><!DOCTYPE html>
	<!--
	ZihWeb CMS <?php echo ZW_V; ?>
	default visual
	 -->
	<html lang="<?php echo $cfg['idi']; ?>">
	<head>
	<?php echo $cls_cnt->getHead(); ?>
	</head>
	
	<body>
	<div id="cntprn">
	  <header>
	  <a href="<?php echo $url; ?>"><h1><?php echo $gcfg['title']; ?></h1></a>
	  <?php
	  echo $cls_cnt->getMenu();
	  /* Slideshow o encabezado extra */
	  echo $cls_cnt->getContent('cnx');
	  ?>
	  </header>
	  <div class="cnt">
	  <?php
	  /* Mostrar contenido principal */
	  echo $cls_cnt->getContent('cnt');
	  ?>
	  </div>
	  <footer>
	  <?php echo vinCamIdi(''); ?><br />
	<br /><?php if(isset($gcfg['copyright'])) echo $gcfg['copyright']; ?>
	  </footer>
	</div><?php if(isset($gcfg['g-ftr'])) echo $gcfg['g-ftr']; echo admBtn(); ?>
	</body>
</html>
<?php
	endif;
endif;
