<?php

class extRss {
	
	var $xt_cfg;
	var $rsscl;
	var $query;
	var $i;
	var $cont;
	
	public function __construct() {
		global $cls_cfg,$cls_cnt;
		//$userAgent = 'Opera/9.80 (Windows NT 6.1; U; es-LA) Presto/2.9.168 Version/11.51';
		//$userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';
		//$userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.'.rand(0,2).'; en-US; rv:1.'.rand(2,9).'.'.rand(0,4).'.'.rand(1,9);
		$userAgent = $cls_cfg->agent;	
		ini_set('user_agent', $userAgent);
		mb_internal_encoding("UTF-8");
		mb_regex_encoding("UTF-8");
		if (isset($cls_cfg->gcfg['xt-rss-cl'])) {
			$arr = $cls_cfg->gcfg['xt-rss-cl'];
		} else { $arr = ''; } 
		$this->xt_cfg = unserialize($arr);
		
		$cls_cfg->query( "CREATE TABLE IF NOT EXISTS `".TBLPRE."cnt_rss` (
		  `id` int(5) NOT NULL AUTO_INCREMENT,
		  `iid` varchar(56) NOT NULL,
		  `network` varchar(300) NOT NULL,
		  `client` varchar(300) NOT NULL,
		  `feed_url` varchar(300) NOT NULL,
		  `description` text NOT NULL,
		  `content` text NOT NULL,
		  `title` varchar(300) NOT NULL,
		  `date` varchar(300) NOT NULL,
		  `url` varchar(255) NOT NULL,
		  `fetch_date` varchar(300) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `iid` (`iid`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ");
		
		$cls_cfg->query( "CREATE TABLE IF NOT EXISTS `".TBLPRE."cnt_rss_net` (
		  `id` int(5) NOT NULL AUTO_INCREMENT,
		  `network` varchar(300) NOT NULL,
		  `client` varchar(300) NOT NULL,
		  `url` varchar(255) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `url` (`url`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ");
		
		$sql = 'SELECT network, client, url FROM '.TBLPRE.'cnt_rss_net';
		$sql = $cls_cfg->query($sql);
		$this->query = $sql;
		$this->rsscl = $cls_cfg->fetch($sql, 'array');
	}
	
	public function &exthdx() {
		global $cls_cfg, $cls_cnt;
		$a = '';
		if (isset($_POST['c'])) {
			ob_start();
			$cls_cfg->query("INSERT INTO ".TBLPRE."cnt_rss_net (network, client, url) VALUES (".fixSql($_POST['c']['network']).", ".fixSql($_POST['c']['client']).", ".fixSql($_POST['c']['url']).")");
			ob_end_clean();
		}
		return $a;
	}
	
	public function extcnt($pag='lst') {
		global $cls_cfg, $cls_dyn, $cls_cnt;
		$ruri = ZW_URL.$cls_cfg->fldr[0];
		$r = '';
		if (esAdmin()) {
			$r .= '<a href="'.$ruri.'/">home</a> · <a href="'.$ruri.'/config.zw">setup</a> · <a href="'.$ruri.'/reload.zw">Reload Feeds</a>';
		}
		if (isset($cls_cfg->fldr[1]) && $cls_cfg->fldr[1] == 'config.zw') {
			$r .= $this->setup();
		} elseif (isset($cls_cfg->fldr[1]) && $cls_cfg->fldr[1] == 'reload.zw') {
			/*
			$tumblr_arr['url']='http://micyaotl.tumblr.com/rss';
			$tumblr_arr['type']='tumblr';
			$tumblr_arr['client']='micyaotl';
			$twitter_arr['url']= ZW_URL.'feedproxy/http://twitter.com/statuses/user_timeline/6342552.rss';
			$twitter_arr['type']='twitter';
			$twitter_arr['client']='micyaotl';
			$facebook_arr['url']= ZW_URL.'feedproxy/http://www.facebook.com/feeds/page.php?id=101522998054&format=rss20';
			$facebook_arr['type']='facebook';
			$facebook_arr['client']='IZM.org';
			$flickr_arr['url']='http://api.flickr.com/services/feeds/photos_public.gne?id=18299872@N00&lang=es-us&format=rss_200';
			$flickr_arr['type']='flickr';
			$flickr_arr['client']='micyaotl';
			$youtube_arr['url']='http://gdata.youtube.com/feeds/base/users/micyaotl/uploads?alt=rss&v=2&orderby=published&client=ytapi-youtube-profile';
			$youtube_arr['type']='youtube';
			$youtube_arr['client']='micyaotl';
			$final_array=array();
			array_push($final_array, $tumblr_arr, $twitter_arr, $facebook_arr, $flickr_arr, $youtube_arr); 
						
			for($i=0;$i<count($final_array);$i++){
				$r .= $this->loadRSS($final_array[$i]['url'],$final_array[$i]['type'],$final_array[$i]['client']);
			}*/
			$sql = 'SELECT network, client, url FROM '.TBLPRE.'cnt_rss_net LIMIT 10';
			$sql = $cls_cfg->query($sql);
			while($rsscl = $cls_cfg->fetch($sql, 'array')){
				$r .= $this->loadRSS($rsscl['url'],$rsscl['network'],$rsscl['client']);
			}
		} else {
			$r .= $this->lstrss($pag);
			$this->cont['title'] = $cls_cnt->cont['tit'];
		}
		return $r;
	}
	
	private function loadRSS($url,$type,$client){
		global $cls_cfg;
	
		try{
			$ret = "Working with feed URL" . $url;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_USERAGENT, $cls_cfg->agent);
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_FAILONERROR, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			$html= curl_exec($ch);
			
			$fh = fopen(ZW_DIRC.'tmp'.Ds.'rss-'.$type.'-'.md5($html).'.txt', 'w') or die("N00B!...");
			fwrite($fh, $html);
			fclose($fh);
			
			if ($html):
				$xml = new DomDocument();
				$xml->strictErrorChecking = false;
				$xml->formatOutput = false;
				$xml->loadXML($html);
				if (!$xml) {
					$ret .= "Failed loading XML\n";
					foreach(libxml_get_errors() as $error) {
						$ret .= "\t". $error->message;
					}
				}
	
				$root = $xml->documentElement;
				if ($type=='facebook') {
					$items = $root->getElementsByTagName('item');
				} else {
					$items = $root->getElementsByTagName('item');
				}
			  
			  for($i=0;$i<($items->length);$i++){
					$item_client=$client;
					$item_network=$type;  
					$item_date = date("Y-m-j G:i:s", strtotime($items->item($i)->getElementsByTagName('pubDate')->item(0)->nodeValue));
					$fetch_date = date("Y-m-j G:i:s");
					$item_title = strip_tags($items->item($i)->getElementsByTagName('title')->item(0)->nodeValue);
					
					$item_id = md5($item_title.$item_date);
					$item_url = $items->item($i)->getElementsByTagName('link')->item(0)->nodeValue;
					$item_description = $items->item($i)->getElementsByTagName('description')->item(0)->nodeValue;
					
					if($type=='flickr'){
						$item_content = $items->item($i)->getElementsByTagNameNS('*','content')->item(0)->getAttribute('url');
					} elseif ($type=='youtube'){
						//$item_description = htmlentities($item_title);
						$item_content = $item_description;
					} elseif ($type=='blog'){
						$item_content = $items->item($i)->getElementsByTagName('encoded')->item(0)->nodeValue;
					} elseif ($type=='facebook'){
						$item_content = $item_description;
						//$item_title = preg_replace("/[^a-zA-Z0-9\s]/", " ", $item_title);
						//$item_content = strip_tags($item_description);	
						//$item_content = strip_tags($items->item($i)->getElementsByTagName('description')->item(0)->nodeValue,'<img>');	
						/*$item_content = preg_match_all("/(<img [^>]*>)/",$item_content,$matches,PREG_PATTERN_ORDER);
						$item_content = $matches[1][0];
						$item_content = preg_match_all('/(src)=("[^"]*")/i',$item_content, $img[$item_content],PREG_PATTERN_ORDER);*/
						//preg_match('/src=(\'|")(.*?)(\'|")/',$item_content,$match);
						//$item_content=$match[0];
						//$item_content=preg_replace('/(src="|")/i','',$item_content);
						//$item_description=preg_replace('/[^a-zA-Z0-9.:#@\/\s]/',' ', strip_tags($item_description));
					} else {
					  $item_content = strip_tags($item_description);
					}
					$ret .=  "Processing item '" . $item_id . "' on " .$fetch_date. "<br />";
					$ret .= "<h5>".$item_title. "</h5><br />";
					$ret .= $item_network." - ".$item_date." - <em>".$item_url."</em><br />";
					$ret .= $item_description. "<hr />";
					$ret .= $item_content."<hr />";
					
					$item_exists_sql = "SELECT iid FROM ".TBLPRE."cnt_rss WHERE iid = '" . $item_id . "'";
					$item_exists = $cls_cfg->query($item_exists_sql) or die("Error: ". mysql_error(). " with query ". $item_exists_sql);
					//$tem_count=mysql_num_rows($item_exists);
					if($cls_cfg->numrows($item_exists)<1){
						$ret .=  "<p style='color:green'>Inserting new item..</p>";
						$item_insert_sql = "INSERT INTO ".TBLPRE."cnt_rss(id, iid, network, client, feed_url, description, content, title, date, url, fetch_date) VALUES ( '0','" . $item_id . "','" . $item_network . "','" . $item_client . "', '" . $url . "', '" . mysql_real_escape_string($item_description) . "', '" . mysql_real_escape_string($item_content) . "', '" . mysql_real_escape_string($item_title) . "', '" . $item_date . "', '" . $item_url . "', '" . $fetch_date . "')";
						$insert_item = $cls_cfg->query($item_insert_sql) or die("Error: ". mysql_error(). " with query ". $item_insert_sql);
					}else{
						$ret .= "<p style='color:blue'>Existing item..</p>";
					}
					$ret .= "<br/>";
				}
			endif;
		}
		
		catch (Exception $e){$ret .= 'Caught exception: '.  $e->getMessage(). "\n";}
		return $ret;
	}
	
	private function setup() {
		global $cls_cfg, $cls_dyn;
		
		$cl = '<ul>';
		$sql = 'SELECT network, client, url FROM '.TBLPRE.'cnt_rss_net';
		$sql = $cls_cfg->query($sql);
		while($c = $cls_cfg->fetch($sql, 'array')) {
			$cl .= '<li>'.$c['network'].' · '.$c['client'].' · '.$c['url'].'</li>';
		}
		
		$cl .= '</ul>';
		//ob_start();
		//print_r($this->xt_cfg);
		//print_r($_POST['c']);
		//$cl .= ob_get_contents();
		//ob_end_clean();
		$form = $cl.$lid.<<< EOPAGE
	<form name="c" method="post" action="">
	  <label for="network">Network</label>
	  	<select name="c[network]" id="network">
		    <option value="facebook">facebook</option>
		    <option value="tumblr">tumblr</option>
		    <option value="flickr">flickr</option>
		    <option value="youtube">youtube</option>
		    <option value="linkedin">linkedin</option>
		    <option value="alert">alert</option>
		</select>
	  <label for="url">URL</label>
	  <input type="text" name="c[url]" id="url">
	  <label for="client">Client</label>
	  <input type="text" name="c[client]" id="client">
	  <input type="submit" name="button" id="button" value="Submit">
	</form>
EOPAGE;
return $form.$this->rsscl;
	}
	
	private function lstrss($pag=0) {
		global $cls_cfg, $cls_dyn;
		$lnk_edit = '';
		if (!isset($pag)) { $pag = 1; } 
		
		if (!isset($pag) || $pag <= 1) {
			$sql = 'SELECT * FROM '.TBLPRE.'cnt_rss ORDER BY date DESC LIMIT 0, 10';
		} else {
			$pact = $pag-1;
			$sql = 'SELECT * FROM '.TBLPRE.'cnt_rss ORDER BY date DESC LIMIT '.$pact.'0, 10';
		}
		$sql = $cls_cfg->query($sql);
		$lstrss = '<dl>';
		while( $fila = $cls_cfg->fetch($sql, 'array') ) {
			$fid = $fila['id'];
			$desc = $fila['description'];
			if ($fila['description'] != $fila['content']) {
				$desc = $fila['description'];
				if ($fila['network'] != 'tumblr') $desc .= $fila['content'];
			}
			if ($fila['network'] != 'youtube') $desc = $this->linkIt($desc);
			$lstrss .= '
			<dt class="feed-'.$fid.' lst"><a href="'.$fila['iid'].'">'.$fila['title'].'<span class="fltRgt" style="font-size:100px;color:#a2a2a2;font-weight:bold;">'.$fila['id'].'</span></a></dt>
			<dd class="feed-'.$fid.' lst">'.$lnk_edit.'
			<span class="lst ldes">'.$desc.'</span>
			<span class="lst ldir" style="display:block;clear:both;">'.$this->timeAgo(strtotime($fila['date'])).' on <a href="'.$fila['url'].'">'.$fila['network'].'</a></span>
			<span class="lst lciu"></span>
			</dd>
			';
		}
		$lstrss .= '</dl>'.N.T;
		$lstrss .= navbar(TBLPRE.'cnt_rss', 'id != 0', ZW_URL.$cls_cfg->fldr[0].'/', 10);
		
		return $lstrss;
	}
	
	private function linkIt($text){
		$text= preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" >$3</a>", $text);
	    $text= preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" >$3</a>", $text);
	    $text= preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $text);	
	    $text= preg_replace("/@(\w+)/", '<a href="http://www.twitter.com/$1" target="_blank">@$1</a>', $text);
	    $text= preg_replace("/\#(\w+)/", '<a href="http://search.twitter.com/search?q=$1" target="_blank">#$1</a>',$text);
	    return $text;
    }
	
	private function timeAgo($timestamp,$output = 'less than a minute ago') {
	    $timestamp = time() - $timestamp;
	    $units = array(604800=>'week',86400=>'day',3600=>'hour',60=>'minute');
	    foreach($units as $seconds => $unit) {
	        if($seconds<=$timestamp) {
	            $value = floor($timestamp/$seconds);
	            $output = 'about '.$value.' '.$unit.($value == 1 ? NULL : 's').' ago';
	            break;
	        }
	    }
	    return $output;
    }
}


/*if(isset($_GET['feed_url'])){
	$url = $_GET['feed_url'];
}else{
	die("Need to pass a consistent) 'feed url'");
}
if(isset($_GET['feed_type'])){
	$type = $_GET['feed_type'];
}else{
	die("Need to pass a) 'feed Type'");
}*/

if (!function_exists('getRSS')) {
function getRSS () {
	header("Content-type: text/xml");
	$client_feed = $_GET['client_feed'];

	$query = sprintf("SELECT * FROM clientes_rss WHERE item_client='%s'",addcslashes($cls_cfg->realescape($client_feed),'%_'));

	$data= $cls_cfg->query($query);	
	
	echo "<?xml version=\"1.0\" encoding =\"UTF-8\"?>";
	echo "<rss version=\"2.0\"
		xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"
		xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\"
		xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
		xmlns:atom=\"http://www.w3.org/2005/Atom\"
		xmlns:sy=\"http://purl.org/rss/1.0/modules/syndication/\"
		xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\"
		xmlns:app=\"http://www.w3.org/2007/app\" 
		xmlns:openSearch=\"http://a9.com/-/spec/opensearch/1.1/\">";
	
	echo "
		<channel>
		<title>".ucwords(str_replace('-',' ',$client_feed))." Feed</title>
		<category>".$client_feed."</category>
		<copyright>Copyright (c) ".date('Y')." feelriviera.com. All rights reserved.</copyright>
		<description>".ucwords(str_replace('-',' ',$client_feed))." RSS Feed by feelriviera.com</description>
		<link>http://feelriviera.com/".$client_feed."/rss.xml</link>
		<atom:link href='http://feelriviera.com/".$client_feed."/rss.xml' rel='self' type='application/rss+xml'/>
	<image>
	<title>tulum ".$client_feed." Feed</title>
	<width>240</width>
	<height>50</height>
	<link>http://feelriviera.com</link>
	<url>http://feelriviera.com/img/rss.png</url>
	</image>";
	while ($row = $cls_cfg->fetch($data,'array')) {
			echo "
		<item>
		<link>http://feelriviera.com/update-".$row[item_id].".html</link>
		<guid isPermaLink =\"true\">".$row[item_url]."</guid>
		<title>".$row[item_title]."</title>
		<description><![CDATA[".$row[item_description]."]]></description>
		<content:encoded><![CDATA[".$row[item_content]."]]></content:encoded>
		<author>".$row[item_network]."</author>
		<pubDate>".$row[item_date]."</pubDate>
		</item>";
	}
	echo "
		</channel>
	</rss>";
}
}
