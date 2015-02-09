<?php

//$publik['tor_enc'] = $banner.'ooo';
/*$publik = array(
		'bar_enc' => "",

		'tor_enc' => $publik['normal'],

		'blo_peq' => "",

		'blo_cua' => "",
);*/

class extDirectory {
	var $cfg;
	var $cont;
	var $uri = array('add', 'edit', 'pag', 'categories', 'search', 'admin');
	var $pag = 1;
	var $cat = 0;
	var $tit;
	var $extcnt;
	var $exthdx;
	var $cattitle;
	var $where;
	var $dir = 'directory';
	var $urluri;
	var $pagestring;
	//var $req_uri;
	//var $ruri;
	var $banner = <<< EOPAGE
		<div style="text-align:center;">
<!-- Adaptable Content -->
<ins class="adsbygoogle"
     style="display:block;height:250px;"
     data-ad-client="ca-pub-7509299074890695"
     data-ad-slot="8428226392"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
		</div>
EOPAGE;
	
	public function __construct() {
		global $cls_cfg, $cls_cnt, $cls_dyn, $urluri, $idi;
		$this->pagestring = '[es]P&aacute;gina[/es][en]Page[/en]';//$cls_cfg->textoEnIdioma('[es]P&aacute;gina[/es][en]Page[/en] ');
		//$this->ruri = $this->req_uri = $cls_cfg->ruri;
		$dir = $cls_cfg->fldr[0];//'directory';
		if (strstr(ZW_URL, 'publik.in')) {
			$dir = 'business-directory';
		} else {
			if( $cls_cfg->idis == 'es' ) {
				$dir = 'directorio';
			} elseif( $cls_cfg->idis == 'en' ) {
				$dir = 'directory';
			}
		}
		$this->dir = $dir;
		$this->urluri = ZW_URL.$this->dir;
		$fldrspns = (isset($cls_cfg->fldr[1])) ?  $cls_cfg->fldr[1] : ''; //$this->dir;
		$cat = $this->cat;
		$pag = $this->pag;
		
		if (isset($cls_cfg->fldr[1]) && strstr ($cls_cfg->fldr[1], '~')) {
			// SHOW FULL LISTING
			$lid = explode('~', $cls_cfg->fldr[1]);
			$lid  = (int)$lid[0];
			$res = $cls_cfg->query("SELECT title FROM dir_neg WHERE lid='$lid' LIMIT 1");
			list($title) = @$cls_cfg->fetch($res, 'row');
			$this->tit = $title;
		} elseif($cls_cfg->fldr[1] == $this->uri[3]){
			$this->tit = 'Categories';
		} elseif($cls_cfg->fldr[1] == $this->uri[5]){
			$this->tit = 'Admin Directory';
		} elseif(strstr ($cls_cfg->fldr[1], 'cat')) {
			$cat = $cls_cfg->fldr[1];
			if (strstr($cat, '-')) {
				$cat = explode('-', $cat);
				//$cat = explode('cat', $cat[1]);
				$cat = $cat[0];
			}
			if (strstr($cat, 'pag')) {
				$pag = explode('pag', $cat);
				//$cat = explode('cat', $pag[0]);
				$cat = $pag[0];
				$pag = $pag[1];
				/*if (strstr($pag, '-')) {
					$pag = explode('-', $pag);
					$pag = $pag[0];
				}*/
				//$pag = (int)$pag[1];
				$this->pag = $pag;
			}
			$cat = explode('cat', $cat);
			$cat = $cat[1];
			$this->cat = $cat;
				//$catit = $this->getCat($this->cat);
				//$this->tit = ''.$catit.' '.$idi['str']['pag'].' '.$this->pag;
				$catit = $this->getCat($this->cat);
				$page = '';
				if ($this->pag !== 1) {
					$page = $this->pagestring.' '.$this->pag;
				}
				$this->tit = ''.$catit.' '.$idi['str']['pag'].' '.$page;
				
			
		} else {
			$this->tit = $cls_cnt->cont['tit']; //Content::getContent('tit',$cls_cnt->pag_id);
		}
		

		if (esAdmin()) { $sts = '=0'; } else {
			$sts = '>0';
		}
		if ($this->cat > 0) {
			$scat = $this->cat;
			//$where = "cid=$cat AND status $sts OR cid2=$cat AND status $sts  OR cid3=$cat AND status $sts";
			$this->where = "cid=$scat OR cid2=$scat OR cid3=$scat AND status $sts";
			//$this->cattitle = $cattit = $catit = $this->getCat($cat, 1);
			//$cattit = '<h2>'.$cls_cnt->remTxt($cattit).'</h2>';
			//$cattit .= $this->lstGiro($this->cat);
		} else {
			$this->where = "status $sts";
		//$cattit = $catit = '';
		//$cattit .= $this->lstGiro($cat=0);
		}
			/**elseif (strstr ($cls_cfg->fldr[1], 'pag')) {
		
				$pag = explode('pag', $cls_cfg->fldr[1]);
				$this->pag = $pag = $pag[1];
				$catit = '';
			}
		//} //*/
		$this->cont['tit'] = $this->tit;
		//$this->cont['tit'] = $this->tit;
	}

	public function &exthdx() {
		global $zihweb, $cls_cfg, $cls_cnt, $gcfg, $idi;
		$exthdx = '';
		$gcfg['ads'] = 0;
		$gcfg['banners'] = 0;
		$fldrspns = $cls_cfg->fldr[1];
		if(isset($cls_cfg->fldr[1])) {
			// ADD || EDIT
			if ($cls_cfg->fldr[1] == $this->uri[0] || $cls_cfg->fldr[1] == $this->uri[1]) {
				if (esAdmin()) {
					$exthdx .= $zihweb->viseditor('.dtn', 'default', 'jQ').$this->mapHead('all');
				} else {
					$exthdx .= $zihweb->viseditor('.dtn', 'min', 'jQ');
				}
			} elseif($cls_cfg->fldr[1] == $this->uri[5]){
				// ADMIN
				if (esAdmin()) {
					if (isset($_POST['catnom'])) {
						$pidcat = (isset($_POST['pidcat'])) ? $_POST['pidcat'] : 0;
						$catnom = $_POST['catnom'];
						$cat = $_POST['catid'];
						$sql = "UPDATE `dir_cat` SET `title` = '$catnom', `pid` = '$pidcat' WHERE `dir_cat`.`cid` = '$cat';";
						$cls_cfg->query($sql);
					}
					//$adc = 0;
					
				}
				
			}/* elseif (strstr ($fldrspns, 'cat')) {
				$cat = explode('cat', $fldrspns);
				if (strstr($fldrspns, 'pag')) {
					$pag = explode('pag', $fldrspns);
					$cat = explode('cat', $pag[0]);
					$this->pag = $pag = $pag[1];
				}
				$cat = $cat[1];
				$this->cat = $cat;
				//$catit = $this->getCat($this->cat);
				//$cls_cnt->cont['tit'] = $catit.' '.$tit.' '.$idi['str']['pag'].' '.$this->pag;
					
			}*/ elseif (strstr ($fldrspns, 'pag')) {
				$pag = explode('pag', $fldrspns);
				$this->pag = $pag = $pag[1];
				//$this->tit = $tit.' '.$idi['str']['pag'].' '.$pag;
			}/* elseif (strstr ($cls_cfg->fldr[1], '~')) {
				// SHOW FULL LISTING
				$lid = explode('~', $cls_cfg->fldr[1]);
				//$extcnt = $this->getListing($lid[0]);
			}*/
		}

		$siteurl = ZW_URL;
		$exthdx .= <<< EOD
		<script src="{$siteurl}lib/jquery.mockjax.js"></script>
		<script src="{$siteurl}lib/jquery.autocomplete.js"></script>
		<script>
			jQ(document).ready(function(){
				var mylist = jQ('#catlst');
				var listitems = mylist.children('li').get();
				listitems.sort(function(a, b) {
				   return jQ(a).text().toUpperCase().localeCompare(jQ(b).text().toUpperCase());
				})
				jQ.each(listitems, function(idx, itm) { mylist.append(itm); });

				'use strict';

				jQ('#autocomplete').devbridgeAutocomplete({
					serviceUrl: '{$siteurl}dyn.zw?suggest',
					minChars: 2,
					onSelect: function(suggestion) {
						window.location.href = "{$this->urluri}/" + suggestion.data.url;
					},
					onHint: function (hint) {
						jQ('#autocomplete-x').val(hint);
					},
					onInvalidateSelection: function() {
						jQ('#selction').html('You selected: none');
					},
					showNoSuggestionNotice: true,
					noSuggestionNotice: 'Sorry, no matching results'
					//groupBy: 'cat'
				});
			});
		</script>

EOD;

		return $exthdx;
	}
	
	public function &extcnt($pag) {
		global $zihweb, $cls_cfg, $cls_cnt, $idi;
		//$tit = 'Directory';
		if(isset($cls_cfg->fldr[1])){
			if ($cls_cfg->fldr[1] == $this->uri[0]) {
				// ADD
				$extcnt = $this->addform();
			} elseif ($cls_cfg->fldr[1] == $this->uri[1]) {
				// EDIT
				if($cls_cfg->esAdmin()) {
					$extcnt = $this->addform($cls_cfg->fldr[2]);
				} else {
					$extcnt = '';
				}
			} elseif($cls_cfg->fldr[1] == $this->uri[4]){
				// SEARCH
				
			} elseif($cls_cfg->fldr[1] == $this->uri[5]){
				// ADMIN
				$extcnt = $this->admin();
				
			} elseif (strstr ($cls_cfg->fldr[1], '~')) {
				// SHOW FULL LISTING
				$lid = explode('~', $cls_cfg->fldr[1]);
				$extcnt = $this->getListing($lid[0]);
			} elseif ($cls_cfg->fldr[1] == $this->uri[3]) {
				// CATEGORIES
				$extcnt = $this->lstGiro(0,'catlst');
			} else {
				//$this->tit = $tit.' '.$idi['str']['pag'].' '.$pag;
				//$extcnt = $this->lstGiro($this->cat);
				$extcnt = $this->lstlis($this->pag, $this->cat, false);//.'pag '.$this->pag.' cat '.$this->cat;
			}
		}
		$urluril = ZW_URL.''.$cls_cfg->fldr[0].'/';
		$headr = $this->headMenu();
		
		$extcnt = $headr.$extcnt;
		//$this->cont['title'] = $this->tit;
		return $extcnt;
	}
	
	private function &headMenu() {
		global $cls_cfg;
		$urluril = $this->urluri.'/';//ZW_URL.''.$cls_cfg->fldr[0].'/';
		//if (esAdmin()) {
		$suggestinput = '
				<div id="autocomp-cont" style="">
		<input type="text" name="suggest" class="autocomplete" id="autocomplete" style=" z-index: 2; background: transparent;"/>
		<input type="text" name="suggest" class="autocomplete" id="autocomplete-x" value="Seach listing" disabled="disabled" style="color: #CCC; background: transparent; z-index: 1;"/>
				</div>
';
		/*} else {
		$suggestinput = '';
		}*/
		$headr = <<< EOD
		<div id="navExt">
		$suggestinput
					<a href="{$urluril}">Home</a> 
					<a href="{$urluril}categories/">Categories</a> 
					<a href="{$urluril}add/">Add Listing</a>

EOD;
		if ($cls_cfg->esAdmin()) {
			
			$headr .= '<a href="'.$urluril.'admin/">Admin</a>';
		}
		$headr .= '<br style="clear:both;" />
				</div>';
		return $headr;
	}
	
	public function &getTopList($cat=0, $limit = 10, $criteria = null, $showcat = false) {
		global $zihweb, $cls_cfg, $cls_cnt, $idi;
		$sql = $cls_cfg->query ( "SELECT * FROM dir_neg WHERE $criteria ORDER BY rand() LIMIT $limit" );

		$urluri = $this->urluri.'/'; //ZW_URL.$this->dir.'/';
		$ret = '<ul class="dirlst">';
		$u = 0;
		while($fila = @$cls_cfg->fetch($sql, 'array')) {
			$nom_neg = textoEnIdioma($fila['title']);
			$u++;
			$nneg = $zihweb->fixUri($nom_neg);
			$dir_neg = textoEnIdioma($fila['address'] );
		
			//$cp++;
			$extra = explode( ':', $fila['submitter']);
			if ( in_array($fila['city'], $idi['uri5'])) {
				$ciuuri = $fila['city'];
			} else {
				$ciuuri = 'no';
			}
			$ret .= '
			<li>';
			$link = $urluri.$fila['lid'].'~'.$nneg;
			$categories = $ad = '';
			if ($showcat == true) {
				$categories = $cls_cnt->textoEnIdioma($this->getCat($fila['cid'], 1));
			}
			if ($u == 3 && $showcat == true) {
				$ad = <<< EOD
<ins class="adsbygoogle" style="display:inline-block;width:300px;height:250px" data-ad-client="ca-pub-7509299074890695" data-ad-slot="1957084790"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
EOD;
			}
			$ret .= <<< EOD
					<div class="dat">
					$categories<br />
						<a href="$link" class="ndn n{$fila['lid']}">$nom_neg</a>
						<br /><span class="ddn">$dir_neg, {$fila['city']}</span>
					</div>$ad
			</li>
EOD;
			
		}
		$ret .= '</ul>';
		return $ret;
	}
	
	public function lstlis ($pag, $cat = 0, $adm = false) {
		global $zihweb, $cls_cfg, $cls_cnt, $cls_dyn, $idi;
		$urluri = $this->urluri.'/';//ZW_URL.$cls_cfg->fldr[0].'/';
		$banner = '';
		if ($adm == false) {
			$banner = $this->banner;
		}
		//$cls_cnt->cont['title'] = $this->tit;
		// paginas
		if (!isset ( $pag ) || $pag <= 0) {
			$sqlr = '0,';
			$pag = 1;
		} else {
			//if($pag == 1) { $sqlr = '0,'; } elseif($pag == 2) { $sqlr = '10,'; } else { $sqlr = $pag.'0,'; }
			//$pag = (int)$pag-1
			$sqlr = (int)$pag.'0' - 10;
			$sqlr .= ',';
		}
		if ($adm == true && esAdmin()) {
			$sts = '=0';
		} else {
			$sts = '>0';
		}

		// category
		$this->cattitle = $cattit = '';
		if (isset($cat) && $cat > 0) {
			$where = "cid=$cat AND status $sts OR cid2=$cat AND status $sts  OR cid3=$cat AND status $sts";
			//$where = "cid=$cat OR cid2=$cat OR cid3=$cat AND status $sts";
			$this->cattitle = $cattit = $catit = $this->getCat($cat, 1);
			$cattit = '<h2>'.$cls_cnt->remTxt($cattit).'</h2>';
			//$cattit .= $this->lstGiro($this->cat);
		} else {
			$where = "status $sts";
			$cattit = $catit = '';
			//$cattit .= $this->lstGiro($cat=0);
		}
		$sql = $cls_cfg->query ( "SELECT * FROM dir_neg WHERE $where ORDER BY date DESC LIMIT $sqlr 10" );
		$urluril = $this->urluri;//ZW_URL.''.$cls_cfg->fldr[0];
		$navbar = $this->navbar('dir_neg', $where, $urluril );

		//$conttit = $catit.' '.$this->tit.' '.$idi['str']['pag'].' '.$this->pag;
		//$cls_cnt->cont['title'] = $this->tit = $conttit;
		/** 
		 * 
		 *  Lista de negocios
		 *  
		 */
		$ret = $cattit;
		$cats = '';
		if ($cls_cfg->fldr[1] == $this->uri[5]) {
			$cats = '';
		} else if ($this->cat !== 0) {
			$cats = $this->lstGiro($this->cat, 'catlst');
		}
		$ret .= $cats.$navbar.'<ul class="dirlst">';
		$NumNeg = ( int ) $pag . '0' - 9;
		$cp = 0;
		while($fila = @$cls_cfg->fetch($sql, 'array')) {
			$nom_neg = textoEnIdioma($fila['title']);

			$nneg = $zihweb->fixUri($nom_neg);
			$dir_neg = textoEnIdioma($fila['address'] );
		
			$cp++;
			$extra = explode( ':', $fila['submitter']);
			if ( in_array($fila['city'], $idi['uri5'])) {
				$ciuuri = $fila['city'];
			} else {
				$ciuuri = 'no';
			}
			$editbtn = '';
			if (esAdmin()) {
				$editbtn = ' <a href="'.$urluri.$this->uri[1].'/'.$fila['lid'].'">[e]</a>';
			}
			$ret .= '
			<li>';
			$link = $urluri.$fila['lid'].'~'.$nneg;
			// Mostrar logotipo
			if (isset($extra[1])) {
				if ($extra[1] == '' || $extra[1] == ' ') {
					$logo = '<div class="neg nl">
					<a href="'.$link.'" class="ndn n'.$fila['lid'].' logo"></a>';
				} else { 
					$logo = '<div class="neg wl">
					<a href="'.$link.'" class="ndn n'.$fila['lid'].' logo">
							<img src="'.ZW_URL.'webimg/t.'.$extra[1].'" alt="'.$nom_neg.'" />
					</a>';
				}
				$ret .= ''.$logo;
			} else {
				$ret .= '<div class="neg nl">
					<a href="'.$link.'" class="ndn n'.$fila['lid'].' logo"></a>';//'<div class="neg sl">';
			}
			$ret .= '
					<div class="dat fltLft">
						<a href="'.$link.'" class="ndn n'.$fila['lid'].'">'.$nom_neg.'</a>
						'.$editbtn;
			
			$ret .= '<br /><span class="ddn">'.$dir_neg.'</span> <span class="fltRgt licount">'.$NumNeg ++ .'</span>
						<br /><span class="ddn">'.$fila['city'].', '.$fila['state'];
			if (($fila['phone'] != '')) {
				$ret .= '
						<br />'.$fila['phone']; //<strong>'.$idi['str_dir']['tel'].':</strong> 
				if ($fila['fax'] != '') {
					$ret .= ', '.$fila['fax']; //<strong>'.$idi['str_dir']['fax'].':</strong> 
				}
			}
			/*
			$ret .= '<br /><strong>'.$idi['str_dir']['vis']. ':</strong> '.$fila['hits'];
			if ($fila ['votes'] > 0) {
				$ret .= ', <strong>e:</strong> '.(int)$fila['rating']. ' ('.$fila['votes']. ' votos)';
			}
			*/
			//$linkcat = '<a href="'.$urluri.'cat'.$fila['cid'].'" >';
			/*$lts = '-';
			$ctitle = $this->mosCat($fila['cid']);
			$lctitle = $zihweb->fixUri($ctitle);
			$ret .= '<a href="'.$urluri.'cat'.$fila['cid'].$lts.$lctitle.'" >'.$ctitle.'</a>';
			*/
			$ret .= '<div rel="categories">'.
				$this->lnkCat($fila['cid']);
			if (isset($fila['cid2']) && $fila['cid2'] > 0) {
				$ret .= ', '.$this->lnkCat($fila['cid2']);
			}
			if (isset($fila['cid3']) && $fila['cid3'] > 0) {
				$ret .= ', '.$this->lnkCat($fila['cid3']);
			}
			$ret .= '</div>';

			/*
			if ($adm==false){
				$fblike = $cls_cnt->fbLike($link, 'recommend');
				$plusone = $cls_cnt->plusOne($med='medium', $con=true, $url = $link);
				$tweet = $cls_cnt->tweet($url=$link);
				$social = $fblike.' '.$plusone.' '.$tweet;
			} else {
				$social = '';
			}
			$ret .= '.
					<br />
					<div style="text-align:center">'.$social.'</div>
					</div>
				</div>';
			*/
			
			$ret .= '<div  style="float:right; clear:both;">';
			if ($extra[0] == '1') {
				$ret .= '<img src="'.ZW_URL.'lib/img/wifilogo.png" alt="'.textoEnIdioma('[es]Servicio [/es]Wireless[en] Service[/en]').'" />';
			}
			if (esAdmin() && $fila['lat'] !== '0.00000000000000000000') {
				$ret .= ''.textoEnIdioma('Map[es]a[/es][en][/en]').'';
			}
			$ret .= '</div>';
			$ret .= '
					
			</li>
			';
			/**
			 *  Publucidad banner 760x90 pixeles
			 */
			if ($cp == 5) {
				$ret .= '
			</ul><br />'.$banner.'<br /><ul class="dirlst">';
			}
		}
		$ret .= '</ul>'.$navbar;
		if ($cp < 5) {
			$ret .= '<br />'.$banner.'<br />';
		}
		return $ret;
	}
	

	private function &navbar($tabla, $where, $link, $porpag = 10) {
		global $zihweb, $pag, $idi, $cls_cfg, $cls_cnt;
		if (isset($this->cat)) {
			$cat = 'cat'.$this->cat;
			if ($this->cat !== 0) { $catsep = '-'; } else { $catsep = ''; }
			$catttl = $catsep.$zihweb->fixUri($this->mosCat($this->cat));
		} else {
			$cat = '';
			$catttl = '';
		}
		
		if (isset($this->pag)) { $pag = $this->pag; } else { $pag = 1; }
		/*if (strstr($cls_cfg->ruri, 'cat')) {
			$this->cont['title'] = $cls_cnt->cont['tit'];
		}*/
		$sql = "SELECT * FROM ".$tabla;
		if (isset($where)) {
			$sql .= " WHERE ".$where;
		}
		$sql = $cls_cfg->query($sql);
		$num_rows = (int) $cls_cfg->numrows($sql);
		//$porpag = 10;
		$offset = 3;
		$ret = '';
		if ($num_rows <= $porpag) {
			return $ret;
		}
		$total_pages = ceil($num_rows / $porpag);
		if ($total_pages > 1) {
			$prev = $pag - 1;
			if ($prev >= 2) {
				$ret .= " <a href=\"$link/$cat"."pag$prev$catttl\"><strong>&nbsp;&laquo;&nbsp;</strong></a> ";
			} else {
				$ret .= " <a href=\"$link/\" class=\"pnavs\"><strong>&nbsp;&laquo;&nbsp;</strong></a> ";
			}
			$cont = 1;
			$pag = explode('-', $pag);
			$pag = $pag[0];
			$current_page = $pag;
			while ( $cont <= $total_pages ) {
				if ($cont == $current_page) {
					$ret .= ' <a><strong>&nbsp;'.$cont.'&nbsp;</strong></a> ';
				} elseif (($cont > $current_page - $offset && $cont < $current_page + $offset) || $cont == 1 || $cont == $total_pages) {
					if ($cont == $total_pages && $current_page < $total_pages - $offset) {
						$ret .= ' <a><strong>&nbsp;...&nbsp;</strong></a> ';
					}
					//$ret .= '<a href="'.$link.($cont - 1).'">'.$cont.'</a> ';
					//if (strstr($link, "pag" )) {
							if ($this->cat == 0) {
								$cat = '';
							}
						if ($cont == 1) {
							$ret .= " <a href=\"$link/$cat$catttl"."\">&nbsp;$cont&nbsp;</a> ";
						} else {
							$ret .= " <a href=\"$link/$cat"."pag$cont$catttl\">&nbsp;$cont&nbsp;</a> ";
						}
					/*} else {
						$ret .= " <a href=\"$link/pag$cont\">&nbsp;$cont&nbsp;</a> ";
					}*/
					if ($cont == 1 && $current_page > 1 + $offset) {
						$ret .= ' <a><strong>&nbsp;...&nbsp;</strong></a> ';
					}
				}
				$cont ++;
			}
			$next = $pag + 1;
			if ($next <= $total_pages) {
				$ret .= " <a href=\"$link/$cat"."pag$next$catttl\"><strong>&nbsp;&raquo;&nbsp;</strong></a> ";
			}
			$ret .= ' <a><strong>&nbsp;'.$idi['str']['pag'].' '.$pag.' '.$idi['str']['de'].' '.$total_pages.'&nbsp;~&nbsp;'.$num_rows.' items</strong></a>';
		}
		$ret = '<div class="pnav">'.$ret.'</div>';
		return $ret;
	}
	
	private function count($cid) {
		global $cls_cfg;
		$where = "status>0 AND cid=$cid AND status>0 OR cid2=$cid AND status>0  OR cid3=$cid";
		$sql = "SELECT * FROM dir_neg";
		if (isset($where)) {
			$sql .= " WHERE ".$where;
		}
		$sql = $cls_cfg->query($sql);
		$sql = (int) $cls_cfg->numrows($sql);
		//$sql = $cls_cfg->fetch($sql, 'assoc');
		//list($sql) = $sql;
		return $sql;
	}
	
	public function lstGiro($cid=0, $id='lst') {
		global $zihweb, $cls_cfg;
		$result = $cls_cfg->query("SELECT cid, pid, title FROM dir_cat WHERE pid=$cid ORDER BY title");
		$urluril = $this->urluri.'/';//ZW_URL.''.$cls_cfg->fldr[0];
		$giro = '<ul id="'.$id.'" class="lstcat">';
		while( list($icid, $pid, $title) = $cls_cfg->fetch($result, 'row') ) {
			$txtsz = $numrws = '';
			if ($cid != 0) {
				$nmrw = $numrws = $this->count($icid);
				$numrws = ' ('.$numrws.')';
				if($id == 'rsts' || $id == 'ldgn') {
					$nmrw = $nmrw*2;
					if ($nmrw <= 9) {
						$nmrw = '0'.$nmrw;
					}
					$txtsz = 'style="font-size:1.'.$nmrw.'em" ';
					$numrws = '';
				}
			}
			$title = textoEnIdioma($title);
			$catlnk = $zihweb->fixUri($title);
			$giro .= '<li class="lcat"><a '.$txtsz.'class="lcat '.$icid.'" href="'.$urluril.'cat'.$icid.'-'.$catlnk.'">'.$title.'</a>'.$numrws;
			unset($catlnk);
			unset($numrws);
			$resulta = $cls_cfg->query("SELECT cid, pid, title FROM dir_cat WHERE pid=".$icid." ORDER BY title");
			$cont = $cls_cfg->numrows($resulta);
			if ($cont > 0) $giro .= '			<ul>';
			while(list($scid, $spid, $stitle) = $cls_cfg->fetch($resulta, 'row')) {
				$numrws = '';
				if ($cid != 0 || $cls_cfg->fldr[1] == $this->uri[3]) {
					$numrws = $this->count($scid);
					$numrws = ' ('.$numrws.')';
				}
				$stitle = textoEnIdioma($stitle);
				$catlnk = $zihweb->fixUri($stitle);
				$giro .= '<li class="lscat '.$scid.'"><a href="'.$urluril.'cat'.$scid.'-'.$catlnk.'">'.$stitle.'</a> '.$numrws.'</li>
				';
				unset($catlnk);
				unset($numrws);
			}
			if ($cont > 0) $giro .= '</ul>';
			$giro .= '</li>';
		}
		$giro .= '</ul>';
		return $giro;
	}
	/**
	 * Muestra lista de seleccion de categorias
	 * @param number $cat id de categoria
	 * @param string $nam name de select
	 * @return string select list
	 */
	public function selGiro($cat = 0, $nam = 'cid') {
		global $zihweb, $cls_cfg;
		$result = $cls_cfg->query("SELECT cid, pid, title FROM dir_cat WHERE pid=0 ORDER BY title");
		$sel = ' selected="selected"';
		$seli = $sel0 = $sels = '';
		if ($cat == 0) {
			$sel0 = $sel;
		}
		$giro = '<select name="'.$nam.'" id="'.$nam.'">
		<option value="0"'.$sel0.'>&mdash;&mdash;&mdash;</option>';
		while( list($cid, $pid, $title) = $cls_cfg->fetch($result, 'row') ) {
			if ($cid == $cat) {
				$seli = $sel;
			}
			//$catlnk = $zihweb->fixUri($title); '-'.$catlnk.
			$giro .= '<option value="'.$cid.'"'.$seli.'>&nbsp;'.textoEnIdioma($title).'</option>
			';
			$seli = '';
			if ($cls_cfg->esAdmin()) {
				$resulta = $cls_cfg->query("SELECT cid, pid, title FROM dir_cat WHERE pid=".$cid." ORDER BY title");
				while(list($scid, $spid, $stitle) = $cls_cfg->fetch($resulta, 'row')) {
					if ($scid == $cat) {
						$sels = $sel;
						//$catlnk = $zihweb->fixUri($stitle); '-'.$catlnk.
					}
					$giro .= '<option value="'.$scid.'"'.$sels.'>&mdash;&nbsp;'.textoEnIdioma($stitle).'</option>
					';
					$sels = '';
				}
			}
		}
		$giro .= '</select>';
		return $giro;
	}
	
	public function getCat($cat, $lnk=0) {
		global $zihweb, $cls_cfg, $cls_cnt;
		$result = $cls_cfg->query("SELECT cid, pid, title FROM dir_cat WHERE cid=$cat" );
		list($cid, $pid, $title) = @$cls_cfg->fetch($result, 'row');
		$ret = $ai = $af = '';
		$urluril = $this->urluri.'/';//ZW_URL.$cls_cfg->fldr['0'];
		if ($lnk != 0) { $af = '</a>'; }
		if ($pid != 0) {
			list($cids, $pidk, $stitle) = @$cls_cfg->fetch($cls_cfg->query("SELECT  cid, pid, title FROM dir_cat WHERE cid=$pid" ), 'row');
			//$clncat = $titlel;  //&Content::textoEnIdioma($titlel);
			//$title = $cls_cfg->textoEnIdioma($title);
			if ($lnk != 0) {
				$ai = '<a href="'.$urluril.'cat'.$cids.'-'.$zihweb->fixUri($cls_cnt->textoEnIdioma($stitle)).'">';
			}
			//$cids .= '-'.$zihweb->fixUri($stitle);
			$ai = $ai;
			//$ret .= $clncat.' &raquo; ';
			$ret .= $ai.$stitle.$af.' &raquo; ';
		}
		if ($lnk != 0) { $ai = '<a href="'.$urluril.'cat'.$cid.'-'.$zihweb->fixUri($cls_cnt->textoEnIdioma($title)).'">'; }
		$clncat = $ai.$title.$af; //&Content::textoEnIdioma($title);
		$ret .= $clncat;
		return $ret;
	}
	
	public function mosCat($cat) {
		global $cls_cfg, $cls_cnt;
		$result = $cls_cfg->query("SELECT title FROM dir_cat WHERE cid=$cat" );
		list($title) = @$cls_cfg->fetch($result, 'row');
		$title = $cls_cnt->textoEnIdioma($title);
		return $title;
	}
	
	private function lnkCat ($cat) {
		global  $zihweb;
		$lts = '-';
		$ctitle = $this->mosCat($cat);
		$lctitle = $zihweb->fixUri($ctitle);
		$return = '<a href="'.$this->urluri.'/cat'.$cat.$lts.$lctitle.'" >'.$ctitle.'</a>';
		return $return;
	}
	public function getListing($lid, $item='all') {
		global $cls_cfg, $cls_cnt, $idi, $urluri,$zihweb;
		$banner = $this->banner;
		$siteurl = ZW_URL;
		$lid = intval($lid);
		//regCont( 1, $lid, 'dir_neg');
		$result = $cls_cfg->query ( "SELECT l.lid, l.cid, l.cid2, l.cid3, l.cid5, l.title, l.address, l.prop, l.city, l.state, l.zip, l.country, l.phone, l.fax, l.email, l.url, l.ip, l.submitter, l.status, l.date, l.hits, l.rating, l.votes, l.comments, l.premium, t.description FROM dir_neg l, dir_txt t WHERE l.lid=$lid AND l.lid=t.lid AND l.status>0" );
		list($lid, $cid, $cid2, $cid3, $cid5, $ltitle, $address, $address2, $city, $state, $zip, $country, $phone, $fax, $email, $url, $ip, $extra, $status, $time, $hits, $rating, $votes, $comments, $premium, $description ) = @$cls_cfg->fetch( $result, 'row' );
		if ($cid5 != 0) {
			$result = $cls_cfg->query ("SELECT description FROM dir_txt WHERE lid=$cid5 LIMIT 1");
			list($description) = @$cls_cfg->fetch( $result, 'row' );
		}
		$ltitle = $cls_cnt->remTxt($ltitle);
		//$this->tit = $ltitle;
		//$this->cont['title'] = $this->tit;
		$description = textoEnIdioma($description);
		$address = textoEnIdioma($address);
		$extra = explode(':', $extra);
		/*$suc_id = preg_replace("/\[[([0-9]*)]]/sU", "\\2", $desc);
		 $suc_id = floatval($suc_id);
		if (is_integer($suc_id )) {
		$sql = mysql_query("SELECT description FROM dir_txt WHERE lid = $suc_id");
		}*/
		$vin_neg = '<br />';
		if (strstr($url, 'http')){ // $url !== '' || $url !== ' ') { // && 
			$vin_neg = '
					<br />
			<a href="'.ZW_URL.'web/directory~'.$cls_cfg->uuid().'~'.$lid.'" rel="nofollow" target="zwLnkLst">'.$idi['str_dir']['viw'].'</a>
			';
		}
		
		$urluri = ZW_URL.$cls_cfg->fldr[0].'/';
		$nneg = $zihweb->fixUri($ltitle);
		$link = $urluri.$lid.'~'.$nneg;
		$fblike = $cls_cnt->fbLike($link, 'recommend');
		$plusone = $cls_cnt->plusOne($med='medium', $con=true, $url = $link);
		$tweet = $tweet = $cls_cnt->tweet($url=$link);
		
		$ret = '
			<div class="inf">
			<h2>'.$ltitle.'</h2>
			<br />
			'.$description.'
					<br />
				<div style="text-align:center">'.$fblike.' '.$plusone.' '.$tweet.'</div>';
		
		
		if (!isset($extra[1]) || ($extra[1] == '' || $extra[1] == ' ')) {
			$ret .= '<div class="neg sl">';
		} else {
			$ret .= '
			<div class="neg">
				<a class="logo"><img src="'.ZW_URL.'webimg/t.'.$extra[1].'" alt="'.$ltitle.'" /></a>';
		}
		//$qrlnk = $urluri.$lid.'~';
		$qrlnk = $zihweb->base_encode($lid, $zihweb->chars);
		$ret .= '
				<div class="dat"><div class="fltRgt"><img src="'.ZW_URL.'webimg/qrd/'.$qrlnk.'" alt="scan" /></div>
					<br />'.$address.'
					<br />'.$city.' '.$zip.', '.$state.' '.$country;
		if(($phone != '')) {
			$ret .= '
					<br />'.$phone;//<strong>' . $idi['str_dir']['tel'].'</strong> 
			if($fax != '') {
				$ret .= ', '.$fax; // <strong>'.$idi['str_dir']['fax'].'</strong>
			}
		}
		$ret .= $vin_neg;//.'
				//	<br /><strong>'.$idi['str_dir']['vis'].':</strong> '.$hits;
		if (esAdmin () == true) {
			$ret .= '<a href="'.$urluri.$this->uri[1].'/'.$lid.'">[e]</a>';
		}
		if ($extra[0] == '1') {
			$ret .= '<img src="'.ZW_URL.'lib/img/wifilogo.png" alt="'.textoEnIdioma('[es]Servicio [/es]Wireless[en] Service[/en]').'" style="float:right;" />';
		}
		if (isset ( $cid ) && $cid > 0) {
			$cat = $this->lnkCat($cid);
			if(isset( $cid2 ) && $cid2 > 0) {
				$cat = $cat.', '.$this->lnkCat($cid2);
			}
			if(isset( $cid3 ) && $cid3 > 0) {
				$cat = $cat.', '.$this->lnkCat($cid3);
			}
		}
		/*
		if ($votes > 0) {
			$ret .= ', <strong>e:</strong> '.(int)$rating.' ('.$votes.' votos)';
		}
		*/
		
		// Formulario de correo
		//if (!empty($email) || $email != '') {
		//	include_once ZW_DIR.'sis/eml.php';
		//}
		
		// Mapa
		if (isset($extra[2]) && $extra[2] != ' ') {
			$lat_long = explode('|', $extra[2]);
			$reqmap = '';
			if (isset($lat_long[1])) {
				$reqmap = 'http://maps.googleapis.com/maps/api/staticmap?center='.$lat_long[0].','.$lat_long[1].'&zoom=16&size=300x300&maptype=roadmap&markers=color:red%7Ccolor:red%7C'.$lat_long[0].','.$lat_long[1].'&sensor=false';
			}
			$alt_map = textoEnIdioma('[es]Localizaci&oacute;n de[/es][en]Location of[/en] ').$ltitle;
			$mapa = '<img src="'.$reqmap.'" alt="'.$alt_map.'" />';
		} else  {
			$mapa = 'mapa';
		}
		/**
		DISPLAY
		 */
		//$zihweb->base_encode($lid, $zihweb->chars)
		$ret .= <<< EOD
		<br />$cat.
		</div>
				<a href="{$siteurl}d/{$qrlnk}" title="shorturl">Short link</a>
			</div>
		<div class="map fltLft w300" style="display:inline;">$mapa</div>
		<div class="fltRgt w300" style="display:inline;">
			{$this->getTopList($cid, 3, "cid=$cid OR cid2=$cid OR cid3=$cid AND status > 0")}
		</div>
		<br style="clear:both"/>$banner<br />
		<div class="fb-comments" data-href="$link" data-numposts="10" data-width="660" data-colorscheme="light"></div>
EOD;
		return $ret;
	}
	
	
	
	private function &patform($pat) {
		$o = "/\[";
		$c = "\]/sU";
		$ret = $o.$pat.$c;
		return $ret;
	}
	
	private function &admin() {
		global $zihweb, $cls_cfg, $cls_cnt, $gcfg;
		$this->extcnt;
		$ret = $pidcat = $catnom = $extcnt = '';
		if (esAdmin()== true) {
			if (isset($_GET['ecat'])) {
				$ecat = $_GET['ecat'];
				$sql = "SELECT cid, pid, title FROM dir_cat WHERE cid = $ecat LIMIT 1";
				$sql = $cls_cfg->fetch($cls_cfg->query($sql), 'row');
				list($cid, $pid, $title) = $sql;
				$catnom = $title;
				$pidcat = ($pid > 0) ? $pid : 0;
			}

			if (isset($_POST['addcat'])) {
				$this->addcat($_POST['pidcatn'], $_POST['addcat']);
			}
			$ret = '<br />'.$this->lstlis(0, 0, true).'<br />';
			/*$catsubcat  = '<form id="orcat">
				<fieldset> <legend>Categories Order</legend>'.$this->selGiro(0,'catpid').'&nbsp;'.$this->selGiro(0,'catsid').'
						</fieldset>
						</form>';*/
			$ecat = (isset($_GET['ecat'])) ? $_GET['ecat'] : 0;
			/*if($ecat != 0) {
				
			}*/
			$seledit  = $this->selGiro($ecat,'ecat');
			$ret .= <<< EOD
			<script>
				jQ(document).ready(function(){
					jQ('#ecat').change(function(){
						jQ('#edcat').submit();
					});
					/*jQ('#submit').click(function(){
						jQ('#catnom').submit();
					});*/ 
				});
			</script>
			
			<form id="edcat">
				<fieldset> <legend>Edit Category</legend>
				$seledit 
				</fiedset>
			</form>
						<form id="modcat" method="post" action="/{$cls_cfg->ruri}?ecat=$ecat">
						<input name="catnom" id="catnom" type="text" value="$catnom" size="40" maxlength="250">
						<input name="catid" id="catid" type="hidden" value="$ecat">
						
						{$this->selGiro($pidcat, 'pidcat')}
						<input id="submit" type="submit" />
						</form>
								
			<form id="addnvcat" method="post" action="/{$cls_cfg->ruri}">
				<fieldset><legend>Add Category</legend>
				<input name="addcat" id="addcat" type="text" value="" size="40" maxlength="250">
				{$this->selGiro(0,'pidcatn')}
				<input id="submit" type="submit" />
				</fiedset>
			</form>
EOD;
			/*

			$cls_cnt->jsjq .= <<< EOD
			jQ('select #catid').change(function(){
						jQ(this).closest("#ecat").submit();
					});'
EOD;
			 */
		}
		return $ret;
	}
	
	private function &addcat($pid, $catnom) {
		global $cls_cfg;
		$pid = (isset($pid)) ? $pid : 0;
		$addcat = $_POST['addcat'];
		$sql = "INSERT INTO `dir_cat` (cid, pid, title) VALUES (0, $pid, '$catnom')";
		$cls_cfg->query($sql);
		unset($_POST);
		return true;
	}
	
	private function &addform($lid=0) {
		global $cls_cfg, $idi;
				require_once(ZW_DIR. 'sis'.Ds.'api'.Ds.'recaptchalib.php');
			$publickey = "6LeylPISAAAAAPO1AUTrvXnWUuSCEOQKlDQlgmau"; // you got this from the signup page
			$privatekey = "	6LeylPISAAAAAF350D1GJ2gdhb_l0yYLxJuN6CwX";
		$valwdn = $valemc = $valfdn = $valedn = $valpai = $valtdn = $valcpn = $valadn = $valpdn = $valddn = $valndn = 'value=""';
		$logo = $lat = $lon = $hidlid = $chkpub = $valdes = '';
		$check = ' checked';
		$cls_cnt->cont['title'] = 'Add listing';
		$wfck = $cid = $cid2 = $cid3 = $cid4 = $cid5 = 0;
		$time = time();
		if ($lid >= 1) {
			$result = $cls_cfg->query ( "SELECT l.lid, l.cid, l.cid2, l.cid3, l.cid4, l.cid5, l.title, l.address, l.prop, l.city, l.state, l.zip, l.country, l.phone, l.fax, l.email, l.url, l.ip, l.submitter, l.status, l.date, l.hits, l.rating, l.votes, l.comments, l.premium, t.description FROM dir_neg l, dir_txt t WHERE l.lid=$lid AND t.lid=l.lid" );
			list($lid, $cid, $cid2, $cid3, $cid4, $cid5, $ltitle, $address, $address2, $city, $state, $zip, $country, $phone, $fax, $email, $url, $ip, $extra, $status, $time, $hits, $rating, $votes, $comments, $premium, $description ) = @$cls_cfg->fetch( $result, 'row' );
			/*if ($cid5 != 0) {
				$result = $cls_cfg->query ("SELECT description FROM dir_txt WHERE lid=$cid5 LIMIT 1");
				list($description) = @$cls_cfg->fetch( $result, 'row' );
			}*/
			$in = 'value="';
			$fi = '"';
			$valndn = $in.$ltitle.$fi;
			$valddn = $in.$address.$fi;
			$valpdn = $in.$address2.$fi;
			$valadn = $in.$city.$fi;
			$valcpn = $in.$zip.$fi;
			$valedn = $in.$state.$fi;
			$valtdn = $in.$phone.$fi;
			$valfdn = $in.$fax.$fi;
			$valpai = $in.$country.$fi;
			$valemc = $in.$email.$fi;
			$valwdn = $in.$url.$fi;
			$valdes = $description;
			if (isset($extra)) {
				list($wf, $logo, $geo) = explode(':', $extra);
				if ($wf == 1) {
					$wfck = $check;
				}
				if (strstr($geo, '|')) {
					list($lat, $lon) = explode('|', $geo);
				}
			}
			$hidlid = '<input type="hidden" name="lid" '.$in.$lid.$fi.'/>';
			if ($status > 0) {
				$chkpub = $check;
			}
			
			$this->cont['title'] = 'Edit '.$ltitle;
		}

		/**
		 * Agregar nuevo negocio
		 */
		if (isset($_POST['env'])) {
			if (esAdmin()) {
				$this->post($_POST);;
			} else {
			$resp = recaptcha_check_answer ($privatekey,
					$_SERVER["REMOTE_ADDR"],
					$_POST["recaptcha_challenge_field"],
					$_POST["recaptcha_response_field"]);
				if ($resp->is_valid) {
					// Your code here to handle a successful verification
					$this->post($_POST);
				} else {
					// What happens when the CAPTCHA was entered incorrectly
					die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
							"(reCAPTCHA said: " . $resp->error . ")");
				}
			}
		}
		/*<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
		 <label>Logotipo <input name="log" type="file" disabled="disabled" size="35" value="Solo listados premium" /></label><br />
		<label>Cup&oacute;n<br /><textarea name="cup" cols="50" rows="10" wrap="physical" id="cup" disabled="disabled">Solo listados premium</textarea></label>*/
		
		$pat = array(
				"/\[ann\]/sU", //"/\[ann\]/sU",
				"/\[ndn\]/sU", "/\[ger\]/sU", "/\[dir\]/sU", "/\[cop\]/sU",
				"/\[ciu\]/sU", "/\[est\]/sU", "/\[pai\]/sU", "/\[tel\]/sU",
				"/\[fax\]/sU", "/\[wif\]/sU", "/\[gir\]/sU", "/\[des\]/sU",
				"/\[env\]/sU", "/\[vac\]/sU"
		);
// 		$pat = array(
// 		"ann", //"/\[ann\]/sU",
// 		"ndn", "ger", "dir", "cop",
// 		"ciu", "est", "pai", "tel",
// 		"fax", "wif", "gir", "des",
// 		"env", "vac"
		
// 		);
// 		$pat = array_map($this->patform($pat), $pat);
		$rem = array(
		$idi['str_dir']['ann'],
		$idi['str_dir']['ndn'],
		$idi['str_dir']['ger'],
		$idi['str_dir']['dir'],
		$idi['str_dir']['cop'],
		$idi['str_dir']['ciu'],
		$idi['str_dir']['est'],
		$idi['str_dir']['pai'],
		$idi['str_dir']['tel'],
		$idi['str_dir']['fax'],
		$idi['str_dir']['wif'],
		$idi['str_dir']['gir'],
		$idi['str_dir']['des'],
		$idi['str']['env'],
		$idi['str']['vac'],
		);
		$updtlst = $mapgodir = $maplatlon = '';
		if (esAdmin()) {
			/*$mapgodir = '<form method="post" action="javascript:void(0)" onsubmit="irDireccion()">
	<input type="text" id="direccion" size="50" title="Buscar direccion, ciudad, estado +enter" />
</form>';*/
			$maplatlon = <<< EOPAGE
			<br />
<div id="map"></div><br />
<input type="text" name="lat" id="lat" title="lat" size="12" maxlength="15" value="$lat" />
<input type="text" name="lon" id="lon" title="lon" size="12" maxlength="15" value="$lon" /><br />
EOPAGE;
			$updtlst = <<< EOD
			<label>Logo <input type="text" name="log" id="log" title="logo" size="12" maxlength="15" value="$logo" /></label>
			<label>Superior <input type="text" name="cid5" id="cid5" title="Superior" size="12" maxlength="15" value="$cid5" /><label>
			<label>Published <input name="pub" type="checkbox" value="1" $chkpub/></label>
			<label>Minor Update <input name="updt" type="checkbox" value="$time"/></label>
EOD;
		}
		
		$recaptcha = (esAdmin()) ? '' : recaptcha_get_html($publickey);
		
		$form = <<< EOPAGE
		<h2 class="ndn">[ann]</h2>
<div class="frm">
<form name="nn" method="post" action="" enctype="multipart/form-data"><br />
<div class="ins"><label>{$idi['str_dir']['ndn']}<input name="ndn" type="text" $valndn size="40" maxlength="250"></label><br />
<span><em>Mi Negocio Suc. Playa del Carmen</em></span><br />
<label>{$idi['str_dir']['ger']} <input name="pdn" type="text" $valpdn size="40" maxlength="250"></label> <br />
<label>{$idi['str_dir']['dir']} <input name="ddn" type="text" $valddn size="50" maxlength="250"> </label><br />
<span><em>Nombre de la Calle, No. 1 Col. Centro</em></span></div>
<label>[cop]<input name="cpn" type="text" $valcpn size="10" maxlength="10"></label>&nbsp;&nbsp;
<label>[ciu] <input name="adn" type="text" $valadn size="20" maxlength="30"></label><br />
<div class="ins"><label>[est] <input name="edn" type="text" $valedn size="15" maxlength="20"></label>&nbsp;&nbsp;
<label>[pai] <input name="pai" type="text" $valpai size="15" maxlength="20"></label><br />
<span><em>Quintana Roo</em></span></div>
<div class="ins"><label>[tel] <input name="tdn" type="text" $valtdn size="15" maxlength="20"></label>&nbsp;&nbsp;
<label>[fax] <input name="fdn" type="text" $valfdn size="15" maxlength="20"></label><br />
<span><em>+52 (988) 123 4567</em></span></div>
<label>[wif] <input name="fwf" type="checkbox" value="1" $wfck/></label><br />
<label>E-Mail <input name="emc" type="text" $valemc size="50" maxlength="250"></label><br />
<div class="ins"><label>Web <input name="wdn" type="text" $valwdn size="50" maxlength="250"></label><br />
<div><em>http://www.feelriviera.com/</em></div>
<label>[gir] {$this->selGiro($cid, 'cid')}</label><br />
<label>[gir] 2 {$this->selGiro($cid2, 'cid2')}</label><br />
<label>[gir] 3 {$this->selGiro($cid3, 'cid3')}</label><br />
<label>[des]</label></div>
<textarea id="dtn" name="dtn" cols="50" rows="30" wrap="physical" class="dtn" style="width: 450px;">$valdes</textarea>
$maplatlon
		<br style="clear:both;"/>
		$updtlst
		<input type="hidden" name="time" value="$time" />
		<input type="hidden" name="env" />$hidlid
		<br style="clear:both;"/>
		$recaptcha	
		<input name="sub" type="submit" value="[env]" /> <input name="vac" type="reset" value="[vac]" />
</form>

</div>
EOPAGE;
		$form = preg_replace($pat, $rem, $form);
		return $form;
	}
	
	/**
	 * Pharses $_POST request into a new listing
	 * @param $_POST $post
	 */
	public function &post($post) {
		global $cls_cfg, $idi;
		$css = $logo = $geod = '';
		if (isset($_POST['env'])) {
			$cid = $_POST['cid'];
			$cid2 = $_POST['cid2'];
			$cid3 = $_POST['cid3'];
			$cid5 = $_POST['cid5'];
			$ndn = fixSql($_POST['ndn']);
			$ddn = fixSql($_POST['ddn']);
			$pdn = fixSql($_POST['pdn']);
			$adn = fixSql($_POST['adn']);
			$edn = fixSql($_POST['edn']);
			$cpn = fixSql($_POST['cpn']);
			$pai = fixSql($_POST['pai']);
			$tdn = fixSql($_POST['tdn']);
			$fdn = fixSql($_POST['fdn']);
			$emc = fixSql($_POST['emc']);
			$wdn = fixSql($_POST['wdn']);
			$dtn = fixSql($_POST['dtn']);
			$lat = fixSql($_POST['lat']);
			$lon = fixSql($_POST['lon']);
			$logo = $_POST['log'];
			if (!isset($_POST['fwf'])) {
				$fwf = 0;
			} else {
				$fwf = $_POST['fwf'];
			}

			$geod = $_POST['lat'].'|'.$_POST['lon'];
			if (isset($_POST['pub'])) {
				$pub = $_POST['pub'];
			}
			if (isset($_POST['updt'])) {
				$updt = $_POST['updt'];
			} else {
				$updt = time();
			}
			$subm = fixSql($fwf.':'.$logo.':'.$geod);
			if (isset($_POST['lid']) && esAdmin()) {
				$dlid = $lid = $_POST['lid'];
				$query = sprintf("UPDATE %s SET cid = %u, cid2 = %u, cid3 = %u, cid4 = %u, cid5 = %u,
						title = %s, address = %s, prop = %s, city = %s, state = %s, zip = %s, country = %s,
						phone = %s, fax = %s, email = %s, url = %s, submitter = %s, status = %u, date = %u, lat = %s, lon = %s WHERE lid = %u",
						"dir_neg", $cid, $cid2, $cid3, 0, $cid5,
						$ndn, $ddn, $pdn, $adn, $edn, $cpn, $pai,
						$tdn, $fdn, $emc, $wdn, $subm, $pub, $updt, $lat, $lon, $lid);
				$cls_cfg->query($query);
				/*if ($cid5 != 0) {
					$dlid = $cid5;
				}*/
				$query = sprintf("UPDATE %s SET description = %s, css = %s WHERE lid = %u", "dir_txt", $dtn, fixSql($css), $dlid);
				$cls_cfg->query($query) or die(mysql_error());
			}
			if (!isset($_POST['lid'])) {
				$query = sprintf("INSERT INTO %s (lid, cid, cid2, cid3, cid4, cid5, title, address, prop, city, state, zip, country, phone, fax, email, url, ip, submitter, status, date, hits, rating, votes, comments, premium, lat, lon)
						VALUES (%u, %u, %u, %u, %u, %u, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '%s', %s, %u, %u, %u, %u, %u, %u, %u, %s, %s)",
						 "dir_neg", 0, $cid, $cid2, $cid3, 0, $cid5, $ndn, $ddn, $pdn, $adn, $edn, $cpn, $pai, $tdn, $fdn, $emc, $wdn, $_SERVER ['REMOTE_ADDR'], $subm, $pub, time (), 0, 0, 0, 0, 0, $lat, $lon );
				$cls_cfg->query($query);
				$query = sprintf("INSERT INTO %s (lid, description,css) VALUES (%u, %s, %s)", "dir_txt", 0, $dtn, fixSql($css));
				$cls_cfg->query($query);
			}
			
			$msg = '<div class="frm"><h2>Gracias por agregar su negocio</h2>
		<p>Revisaremos los datos que nos ha enviado para publicar su negocio* en el directorio</p> <p>Una vez que su negocio es publicado le enviaremos un correo electr&oacute;nico de confirmaci&oacute;n con los datos para hacer ediciones</p> <p><em>*Nos reservamos el derecho de publicar o eliminar los listados añadidos al directorio</em></p>
		<p>&nbsp;Haga clic aqui para volver al directorio de negocios&nbsp;</p></div>';
			unset($_POST);
			return $msg;
		}
	}
	
	private function mapHead($tip='hdx') {
		global $cfg;
		$siteurl = ZW_URL;
		$hdx = N.T.'<script src="http://maps.google.com/maps/api/js?sensor=true&amp;language='.$cfg['idi'].'"></script>';
		$jsjq = <<< EOD

			/* Map jQ */
				var loc = new google.maps.LatLng(20.215009,-87.451469);
				var optns = {
					zoom: 8,
					center: loc,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				var map = new google.maps.Map(document.getElementById("map"), optns);
		
				google.maps.event.addListener(map, 'click', function(e) {
					//placeMarker(e.latLng, map);
					var position = e.latLng;
					var marker = new google.maps.Marker({
						position: position,
						map: map,
						draggable: true,
						riseOnDrag: true,
					});
					map.panTo(position);
					jQ('#lat').attr('value', position.lat());
					jQ('#lon').attr('value', position.lng());

					google.maps.event.addListener(marker, 'dragend', function() {
						map.panTo(marker.latLng);
						jQ('#lat').attr('value', marker.latLng.lat());
						jQ('#lon').attr('value', marker.latLng.lng());
					});
				//
				});
								
				google.maps.event.addListener(map, 'center_changed', function() {
					window.setTimeout(function() {
					map.panTo(marker.getPosition());
					}, 3000);
				});
				
				google.maps.event.addListener(marker, 'click', function() {
					map.setZoom(5);
					map.setCenter(marker.getPosition());
				});
				
				function placeMarker(position, map) {
					var marker = new google.maps.Marker({
						position: position,
						map: map,
						draggable: true,
						riseOnDrag: true,
					});
					map.panTo(position);
					jQ('#lat').attr('value', position.lat());
					jQ('#lon').attr('value', position.lng());
				
					google.maps.event.addListener(marker, 'dragend', function() {
						map.panTo(position);
						jQ('#lat').attr('value', position.lat());
						jQ('#lon').attr('value', position.lng());
					});
				}

EOD;
		$js = <<< EOD
EOD;
		$css = <<< EOD
		#map { float:left; width:450px; height:450px; position:relative; margin:0px; }
		#message { position:absolute; padding:10px; background:#555; color:#fff; width:75px; }
EOD;
		$all = $hdx.'
<script>
				'.$js.'
		jQ(document).ready(function(){
				'.$jsjq.'
				});
	//]]>
</script>
						<style>'.$css.'</style>';
		return $$tip;
	}
}