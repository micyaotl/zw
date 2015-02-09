<?php
class Listings {
	var $uri = array('city', 'category');
	var $pag = 1;
	var $cat = 0;
	var $tpl;
	var $tables = array();
	var $querys = array();
	var $where_lst;
	
	public function __construct($table, $limit, $uri) {
		global $cls_cfg, $cls_cnt, $cls_dyn;
		$this->querys = array(
				'categories'	=> "SELECT title FROM dir_cat WHERE cid=$cat",
				'getlistings'	=> "SELECT l.lid, l.cid, l.cid2, l.cid3, l.cid5, l.title, l.address, l.prop, l.city, l.state, l.zip, l.country, l.phone, l.fax, l.email, l.url, l.ip, l.submitter, l.status, l.date, l.hits, l.rating, l.votes, l.comments, l.premium, t.description FROM dir_neg l, dir_txt t WHERE l.lid=$lid AND l.lid=t.lid AND l.status>0",
		);
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
		
		public function mosCat($cat) {
			global $cls_cfg, $cls_cnt;
			$result = $cls_cfg->query("SELECT title FROM dir_cat WHERE cid=$cat" );
			list($title) = @$cls_cfg->fetch($result, 'row');
			$title = $cls_cnt->textoEnIdioma($title);
			return $title;
		}
	
}