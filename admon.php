<!DOCTYPE html>
<html lang="<?php echo $cfg['idi']; ?>"<?php //if ( $gcfg['tmp']== 1 ) { echo ' manifest="'.ZW_URL.'manifest.cache"'; } ?> >
<!--
	<?php echo ZW_V; ?>
	(c) 2009-2011 feelRiviera.com
	(c) 2012-2014 ZihWeb Development (http://www.zihweb.com/)
-->
<head>
<meta charset="utf-8" />
<title><?php echo $cls_cfg->gcfg['title'].' · ZihWeb CMS Admin'; ?></title>
<?php echo  $cls_adm->admHead() ?>
</head>

<body>
<div id="cntprn">
<header>
  <?php echo $cls_adm->admMenu(); ?>
  <span class="fltRgt"><?php echo $gcfg['title']; ?>&nbsp;&nbsp;</span>
  <a href="<?php echo $url; ?>"><h1>ZihWeb CMS</h1></a>
</header>

<div class="cnt">
<?php
	if (esAdmin() && WASA == 1) {
		if (isset($_GET['edita'])) {
			echo admEdit($_GET['edita'], 'cnt');
		} elseif($as == 'eml') {
			$mailconfig = explode('|', $gcfg['mailconfig']);
			echo $cls_adm->emlPanel();
		} elseif($as == 'uld') {
			echo $cls_adm->uldPanel('cnt');
		} elseif($as == 'map') {
			echo $cls_adm->mapPanel();
			echo $cls_adm->mapList();
		} elseif($as == 'cfg'){
			echo $cls_adm->cfgPanel();
		} elseif($as == 'src' || $as == 'xml'){
			echo $cls_adm->srcPanel();
		} elseif($as == 'usr'){
			echo $cls_adm->usrPanel();
		} elseif($as == 'btt') {
			echo $cls_adm->bTest();
		} else {
			echo $cls_adm->admPanel();
		}
		?>
  <br style="clear: both;" />
	<!-- login/logout -->
	<form action="<?php echo ZW_URL.ZW_ADM; ?>" method="post" enctype="multipart/form-data">
	<input name="logout" type="hidden" />
	<input name="logout" type="submit" value="logout" />
	</form>
		<?php
	} else {
		echo admLogin();
	}
  ?>
</div>
  <div id="cpyrgt">
  <?php 
  $txt = <<<EOD
  Copyright &copy; [yyyy] [webdev] &middot; [feelriviera]
EOD;
  echo $cls_cnt->remTxt($txt)
  ?>  <a href="http://www.zihweb.com/f1/" title="Soporte de feelRiviera" target="zw_kbsd">soporte</a><?php echo vinCamIdi(); ?>
  <br /></div>
</div><?php echo admBtn(); ?>
</body>
</html>
