<?php
if (!class_exists('rss_php')) {
	class rss_php {
		
		public $document;
		public $channel;
		public $items;
	
	/****************************
		public load methods
	***/
		# load RSS by URL
			public function load($url=false, $unblock=true) {
				if($url) {
					if($unblock) {
						$this->loadParser(file_get_contents($url, false, $this->randomContext()));
					} else {
						$this->loadParser(file_get_contents($url));
					}
				}
			}
		# load raw RSS data
			public function loadRSS($rawxml=false) {
				if($rawxml) {
					$this->loadParser($rawxml);
				}
			}
			
	/****************************
		public load methods
			@param $includeAttributes BOOLEAN
			return array;
	***/
		# return full rss array
			public function getRSS($includeAttributes=false) {
				if($includeAttributes) {
					return $this->document;
				}
				return $this->valueReturner();
			}
		# return channel data
			public function getChannel($includeAttributes=false) {
				if($includeAttributes) {
					return $this->channel;
				}
				return $this->valueReturner($this->channel);
			}
		# return rss items
			public function getItems($includeAttributes=false) {
				if($includeAttributes) {
					return $this->items;
				}
				return $this->valueReturner($this->items);
			}
	
	/****************************
		internal methods
	***/
		private function loadParser($rss=false) {
			if($rss) {
				$this->document = array();
				$this->channel = array();
				$this->items = array();
				$DOMDocument = new DOMDocument;
				$DOMDocument->strictErrorChecking = false;
				$DOMDocument->formatOutput = true;
				$DOMDocument->loadXML($rss);
				$this->document = $this->extractDOM($DOMDocument->childNodes);
			}
		}
		
		private function valueReturner($valueBlock=false) {
			if(!$valueBlock) {
				$valueBlock = $this->document;
			}
			foreach($valueBlock as $valueName => $values) {
					if(isset($values['value'])) {
						$values = $values['value'];
					}
					if(is_array($values)) {
						$valueBlock[$valueName] = $this->valueReturner($values);
					} else {
						$valueBlock[$valueName] = $values;
					}
			}
			return $valueBlock;
		}
		
		private function extractDOM($nodeList,$parentNodeName=false) {
			$itemCounter = 0;
			foreach($nodeList as $values) {
				if(substr($values->nodeName,0,1) != '#') {
					if($values->nodeName == 'item') {
						$nodeName = $values->nodeName.':'.$itemCounter;
						$itemCounter++;
					} else {
						$nodeName = $values->nodeName;
					}
					$tempNode[$nodeName] = array();				
					if($values->attributes) {
						for($i=0;$values->attributes->item($i);$i++) {
							$tempNode[$nodeName]['properties'][$values->attributes->item($i)->nodeName] = $values->attributes->item($i)->nodeValue;
						}
					}
					if(!$values->firstChild) {
						$tempNode[$nodeName]['value'] = $values->textContent;
					} else {
						$tempNode[$nodeName]['value']  = $this->extractDOM($values->childNodes, $values->nodeName);
					}
					if(in_array($parentNodeName, array('channel','rdf:RDF'))) {
						if($values->nodeName == 'item') {
							$this->items[] = $tempNode[$nodeName]['value'];
						} elseif(!in_array($values->nodeName, array('rss','channel'))) {
							$this->channel[$values->nodeName] = $tempNode[$nodeName];
						}
					}
				} elseif(substr($values->nodeName,1) == 'text') {
					$tempValue = trim(preg_replace('/\s\s+/',' ',str_replace("\n",' ', $values->textContent)));
					if($tempValue) {
						$tempNode = $tempValue;
					}
				} elseif(substr($values->nodeName,1) == 'cdata-section'){
					$tempNode = $values->textContent;
				}
			}
			return $tempNode;
		}
		
		private function randomContext() {
			$headerstrings = array();
			$headerstrings['User-Agent'] = 'Mozilla/5.0 (Windows; U; Windows NT 5.'.rand(0,2).'; en-US; rv:1.'.rand(2,9).'.'.rand(0,4).'.'.rand(1,9).') Gecko/2007'.rand(10,12).rand(10,30).' Firefox/2.0.'.rand(0,1).'.'.rand(1,9);
			$headerstrings['Accept-Charset'] = rand(0,1) ? 'en-gb,en;q=0.'.rand(3,8) : 'en-us,en;q=0.'.rand(3,8);
			$headerstrings['Accept-Language'] = 'en-us,en;q=0.'.rand(4,6);
			$setHeaders = 	'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5'."\r\n".
							'Accept-Charset: '.$headerstrings['Accept-Charset']."\r\n".
							'Accept-Language: '.$headerstrings['Accept-Language']."\r\n".
							'User-Agent: '.$headerstrings['User-Agent']."\r\n";
			$contextOptions = array(
				'http'=>array(
					'method'=>"GET",
					'header'=>$setHeaders
				)
			);
			return stream_context_create($contextOptions);
		}
		
	}
}

if (!class_exists('MyAggregator')) {
	class MyAggregator {
		static function cmp_pubdate( $a, $b ) {
			$a_t = strtotime( $a['pubdate'] ) ;
			$b_t = strtotime( $b['pubdate'] ) ;
			if( $a_t == $b_t ) return 0 ;
			return ($a_t > $b_t ) ? -1 : 1;
		}
	
		// assemble arrays for display in date order
		function all_documents(){
			$all_data  = array_merge (
			$this->upcoming(), $this->past() ) ;
			// Use within its own class using the $this syntax so:
			usort( $all_data, array( $this , "cmp_pubdate" ) ) ;
			return $all_data ;
		}
	}
}


if (!function_exists('linkIt')) {
	function linkIt($text){
	$text= preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" >$3</a>", $text);
    $text= preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" >$3</a>", $text);
    $text= preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $text);	
    $text= preg_replace("/@(\w+)/", '<a href="http://www.twitter.com/$1" target="_blank">@$1</a>', $text);
    $text= preg_replace("/\#(\w+)/", '<a href="http://search.twitter.com/search?q=$1" target="_blank">#$1</a>',$text);
    return $text;
    }
	function timeAgo($timestamp,$output = 'less than a minute ago') {
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

if (!function_exists('setIcon')) {
	function setIcon($icon){
		$img=str_replace('http://','',$icon);
 		$img_a=explode('/',$img);
		$img_b="";
		$img_c="";
		if($img_a[0]=='www.facebook.com'){
			$img_b='img/social/facebook.png';
			$img_c="facebook";
		}elseif($img_a[0]=='twitter.com'){
			$img_b='img/social/twitter.png';
			$img_c="twitter";
		}elseif($img_a[0]=='www.flickr.com'){
			$img_b='img/social/flickr.png';
			$img_c="flickr";
		}elseif($img_a[0]=='www.youtube.com'){
			$img_b='img/social/youtube.png';
			$img_c="youtube";
		}elseif($img_a[0]=='www.linkedin.com'){
			$img_b='img/social/linkedin.png';
			$img_c="linkedin";
		}else{
			$img_b='img/social/wordpress.png';
			$img_c="blog";
		}
		return array ($img_b,$img_c);
	}
}
	
    $facebook = new rss_php;
    $facebook->load('http://www.facebook.com/feeds/page.php?id=101522998054&format=rss20');
	$facebookitems = $facebook->getItems();
	$blog = new rss_php;
	$blog->load('http://blog.todotulum.com/feed');
	$blogitems = $blog->getItems();
	$flickr = new rss_php;
	$flickr->load('http://api.flickr.com/services/feeds/photos_public.gne?id=18299872@N00&lang=es-us&format=rss_200');
	$flickritems = $flickr->getItems();
	$youtube = new rss_php;
	$youtube->load('http://gdata.youtube.com/feeds/base/users/micyaotl/uploads?alt=rss&v=2&orderby=published&client=ytapi-youtube-profile');
	$youtubeitems = $youtube->getItems();
	$linkedin = new rss_php;
	$linkedin->load('http://www.linkedin.com/rss/nus?key=di4mCA9wUale6-t6tFzXB6reTnBcjg-rAhPULNQcA68DzkQJ88F1n_a7CwgC2asNdB');
	$linkedinitems = $linkedin->getItems();
	$twitter = new rss_php;
	$twitter->load('http://twitter.com/statuses/user_timeline/micyaotl.rss');
	$twitteritems = $twitter->getItems();
	$finalArray = array();
    $finalArray = array_merge($facebookitems, $blogitems, $flickritems, $youtubeitems, $linkedinitems, $twitteritems); 
	usort ( $finalArray, array( "MyAggregator", "cmp_pubdate" ) ) ;
    $html = '<ul>
    ';
    foreach($finalArray as $index => $item) {		
		//$media = $index->children('http://search.yahoo.com/mrss/');
		$icon=setIcon($item['link']);
		$txt="";
		$file="";
		$arr="";
		if($icon[1]=='facebook'){
			$txt="Open Link in Facebook";
			$file=$item['link'];
			$arr=$item['description'];
		}elseif($icon[1]=='twitter'){
			$txt="View Update in Twitter";
			$file=$item['link'];
			$arr=$item['description'];
		}elseif($icon[1]=='flickr'){
			$txt="Open Flickr Picture";
			$file=$item['link'];
			$arr=$item['description'];
		}elseif($icon[1]=='youtube'){
			$txt="Open Youtube Video";
			$file=$item['link'];
			$arr=$item['description'];
		}elseif($icon[1]=='linkedin'){
			$txt="See Linked In Connection";
			$file=$item['link'];
			$arr=$item['description'];
		}else{
			$txt="Read More on the Blog";
			$file=$item['link'];
			$arr=$item['description'];
		};
		
		$time_a = timeAgo(strtotime($item['pubDate']));
        $html .= '<li><p style="padding-left:0; padding-top:0;"><span class="soc icon '.$icon[1].'"></span>
        '.linkIt(strtolower($item['title'])).'<br />
        <small><a href="'.$file.'" id="'.$item['title'].'" class="social_link"  target="_blank">'.$txt.'</a> - '.$time_a.'</small>
        </p><div class="invi"><h2>'.$item['title'].'</h2><br><p>'.$arr.'<br><a href="'.$file.'  target="_blank">'.$txt.'</a></p></div>
        </li>
        ';
        $item_id = md5($item['title'].$time_a);
    	$item_exists_sql = "SELECT iid FROM ".TBLPRE."cnt_rss WHERE iid = '" . $item_id . "'";
		$item_exists = mysql_query($item_exists_sql)or die("Error: ".mysql_error()." with query ".$item_exists_sql);
		//$tem_count=mysql_num_rows($item_exists);
		if(mysql_num_rows($item_exists)<1){
			$ret .=  "<p style='color:green'>Inserting new item..</p>";
			$item_insert_sql = "INSERT INTO ".TBLPRE."cnt_rss(id, iid, network, client, feed_url, description, content, title, date, url, fetch_date) VALUES 
			( '0','" . $item_id . "','" . $icon[1] . "','" . 'micyaotl' . "', '".$item['link']."', '" . mysql_real_escape_string($txt) . "', '" . mysql_real_escape_string($item_content) . "', '" .mysql_real_escape_string($item['title']). "', '" . $time_a . "', '" . $item_url . "', '" . $fetch_date . "')";
			$insert_item = mysql_query($item_insert_sql)or die("Error: ". mysql_error(). " with query ". $item_insert_sql);
		}else{
			$ret .= "<p style='color:blue'>Existing item...</p>";
		}
    }
        $html .= '</ul>';
        
$extcnt .= $html;