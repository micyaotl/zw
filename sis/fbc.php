<?php
/**
 * Facebook Connect
 * 
 * @package ZihWeb
 * @subpackage fbc
 * @version 0.2
 * @author Marco Garcia <micyaotl@gmail.com>
 * @copyright 2009 FeelRiviera.com
 */
/*
$req_uri = explode('fbc/', $req_uri);
$req_uri = $req_uri[1];
$ruri = $req_uri;
$ruri = explode('?', $ruri); $ruri = $ruri[0];
$ruri = str_replace('fbc/', '', $ruri);
if($ruri == '') {
	$ruri = 'index.html';
}
*/

$meta = $cls_cnt->meta;
if(file_exists(ZW_DIR.'sis'.Ds.'ext'.Ds.$meta['ext'].'.php')) {
	require(ZW_DIR.'sis'.Ds.'ext'.Ds.$meta['ext'].'.php');
	$xtret = array(
	'act' => true,
	'hdx' => $exthdx,
	'cnt' => $extcnt,
	);
}

sendHeaders( 'html', '200' );
echo '<!DOCTYPE html>
<html lang="'.$cfg['idi'].'" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta charset="utf-8" />
<title>'.getContent('tit').'</title>
<style>

</style>
<link href="'.$url.'fbc.css" rel="stylesheet" type="text/css">
<meta name="author" content="feelRiviera.com - http://feelriviera.com/"/>';
echo '
<meta name="generator" content="'.ZW_V.'" />
'.getContent('hdx').'
</head>
'.getContent('cnx').'
<div id="cont">';

if ($cls_usr->fbme):
/*
?>
    <a href="<?php echo $cls_usr->facebook->getLogoutUrl(); ?>">
      <img src="//static.ak.fbcdn.net/rsrc.php/z2Y31/hash/cxrz4k7j.gif">
    </a>
    <?php else: ?>
    <div><a href="<?php echo $cls_usr->fbLoginUrl; ?>">
        <img src="//static.ak.fbcdn.net/rsrc.php/zB6N8/hash/4li2k73z.gif">
      </a>
    </div>
<?php
*/
endif;


if ($cls_usr->fbme): ?>
    <img src="//graph.facebook.com/<?php echo $cls_usr->user; ?>/picture">
    <?php
    echo $cls_usr->fbme['name'];
endif;
echo $cls_usr->fblilo;
echo $cls_cnt->remTxt($cls_cnt->getContent('cnt'));

/** JS JDK
 * <div id="fb-root">
 * 
 * <fb:login-button></fb:login-button>
 * </div>
    <script>               
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $cls_usr->facebook->getAppID() ?>', 
          cookie: true, 
          xfbml: true,
          oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
          window.location.reload();
        });
        FB.Event.subscribe('auth.logout', function(response) {
          window.location.reload();
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/<?php echo $cfg['idi']; ?>_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
 */
?>
 
<?php
if($cls_usr->user && $cls_cfg->admin = true) {
	echo '<h1>Me</h1>
	<pre>';
	print_r($cls_usr->fbme);
	echo '
	</pre>
	
	<h1>Session</h1>
	<pre>';
	print_r($_SESSION);
	echo '</pre>';
}
	
echo '</div>
'.admBtn(false).'
</html>';