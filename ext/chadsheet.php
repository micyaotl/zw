<?php

class extChadsheet {
	var $cont;
	
	public function __construct() {
		$this->cont['tit'] = 'Chad\'s Pay Sheet';
		
	}
	

	public function exthdx() {
		global $cls_cfg, $cls_cnt;
		$ret = <<<EOPAGE
		<style>
				#seccsnav { }
				.itemList {  padding:0; }
				.itemLi {  }
		</style>		
EOPAGE;
		$this->cont['title'] = 'Chad Pay Sheet';
		return $ret;
	}
	
	public function &extcnt() {
		global $cls_cfg, $cls_cnt;
		$ret = '
				<br />
				<div id="seccsnav">
				<a href="'.ZW_URL.$cls_cfg->ruri.'">C\'s S</a> &nbsp;
				<a href="'.ZW_URL.$cls_cfg->ruri.'?orderby=mesero&groupby=claveprod">filter sample</a> &nbsp;
				<a href="'.ZW_URL.$cls_cfg->ruri.'?mesero=1">mesero resume</a> &nbsp;
				</div>';
		if(isset($_GET['mesero'])) {
			$ret .= $this->infMesero();
		} else {
			$ret .= $this->lstItems();
		}
		return $ret;
	}
	
	private function &lstItems() {
		global $cls_cfg, $cls_cnt;
		$sql = 'SELECT * FROM `table 40`';
		if (isset($_GET['groupby'])) {
			$sql .= ' GROUP BY '.$_GET['groupby'];
		}
		if (isset($_GET['orderby'])) {
			$sql .= ' ORDER BY '.$_GET['orderby'].' ASC';
		}
		$qry = $sql;
		$sql = $cls_cfg->query($sql);
		$numrows = $cls_cfg->numrows($sql);
		$ret = $numrows . '
				<br />
			<table>
				<tr class="tHead">
				<td>id</td>
					<td>folio</td> <td>seriefolio</td> <td>numcheque</td> <td>fecha</td> <td>cierre</td> <td>mesa</td>
					<td>mesero</td> <td>descuentocuenta</td> <td>nombre</td> <td>cantidad</td> <td>claveprod</td>
					<td>precio</td> <td>descuento</td> <td>idmeseroproducto</td> <td>descripcionproducto</td>
					<td>clavegrupo</td> <td>descripciongrupo</td>
				</tr>
				';
		$id = 0;
		while ($fil = $cls_cfg->fetch($sql, 'array')) {
			$id++;
			/*
			 *  folio 	seriefolio 	numcheque 	fecha 	cierre 	mesa 	mesero 	descuentocuenta
			*  nombre 	cantidad 	claveprod 	precio 	descuento 	idmeseroproducto 	descripcionproducto
			*  clavegrupo 	descripciongrupo
			*/
				
			$ret .= '
					<tr class="itemLi">
					<td>'.$id.'</td>
						<td>'.$fil['folio'].'</td> <td>'. $fil['seriefolio'].'</td>
						<td>'.$fil['numcheque'].'</td> <td>'. $fil['fecha'].'</td><td>'.$fil['cierre'].'</td>
						<td>'.$fil['mesa'].'</td > <td>'.$fil['mesero'].'</td>
						<td>'.$fil['descuentocuenta'].'</td> <td>'.$fil['nombre'] .'</td>
						<td>'.$fil['cantidad'].'</td> <td>'.$fil['claveprod'].'</td> <td>'.$fil['precio'].'</td>
						<td>'.$fil['descuento'].'</td> <td>'.$fil['idmeseroproducto'].'</td>
						<td>'.$fil['descripcionproducto'].'</td> <td>'.$fil['clavegrupo'].'</td>
						<td>'.$fil['descripciongrupo'].'</td>
					</tr>
				';
		}
		$ret .= '</table>';
		if (esAdmin()) {
			$ret .= '<br />'.$qry;
		}
		return $ret;
	}
	
	private function &infMesero($mId='12') { 
		global $cls_cfg, $cls_cnt;
		if (isset($_GET['mesero'])) {
			$mId = $_GET['mesero'];
		}
		$sql = 'SELECT * FROM  `table 40` WHERE mesero ='.$mId.' ORDER by fecha';
		//$sql = 'SELECT * FROM ('.$sql.') GROUP BY claveprod';
		
		$qry = $sql;
		$sql = $cls_cfg->query($sql);
		$numrows = $cls_cfg->numrows($sql);
		$ret = $numrows . '
				<br />
			<table border="1">
				<tr class="tHead">
				<td>id</td>
					<td>folio</td> <td>seriefolio</td> <td>numcheque</td> <td>fecha</td> <td>cierre</td> <td>mesa</td>
					<td>mesero</td> <td>descuentocuenta</td> <td>nombre</td> <td>cantidad</td> <td>claveprod</td>
					<td>precio</td> <td>descuento</td> <td>idmeseroproducto</td> <td>descripcionproducto</td>
					<td>clavegrupo</td> <td>descripciongrupo</td>
				</tr>
				';
		$id = 0;
		while ($fil = $cls_cfg->fetch($sql, 'array')) {
			$id++;
			$ret .= '
					<tr class="itemLi">
					<td>'.$id.'</td>
						<td>'.$fil['folio'].'</td> <td>'. $fil['seriefolio'].'</td>
						<td>'.$fil['numcheque'].'</td> <td>'. $fil['fecha'].'</td><td>'.$fil['cierre'].'</td>
						<td>'.$fil['mesa'].'</td > <td>'.$fil['mesero'].'</td>
						<td>'.$fil['descuentocuenta'].'</td> <td>'.''.$fil['nombre'] .'</td>
						<td>'.$fil['cantidad'].'</td> <td>'.$fil['claveprod'].'</td> <td>'.$fil['precio'].'</td>
						<td>'.$fil['descuento'].'</td> <td>'.$fil['idmeseroproducto'].'</td>
						<td>'.$fil['descripcionproducto'].'</td> <td>'.$fil['clavegrupo'].'</td>
						<td>'.$fil['descripciongrupo'].'</td>
					</tr>
				';
		}
		$ret .= '</table>';
		if (esAdmin()) {
			$ret .= '<br />'.$qry;
		}
		return $ret;
	}
}