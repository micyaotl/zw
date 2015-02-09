<?php
/**
 * Logger Class
 * User tracking
 * @param unknown_type $sid
 * @param unknown_type $lid
 * @param unknown_type $tab
 */
function regCont($sid, $lid, $tab) {
	global $cls_cfg;
	$ayer = time()-46800;
	$lid = intval($lid);
	$sql = "SELECT COUNT(*) FROM log WHERE ip='" . $_SESSION ['uip'] . "' AND fech>$ayer AND sid=$sid AND lid=$lid";
	list($sql) = $cls_cfg->fetch($sql, 'row');
	if ($sql == 0) {
		// Register user
		$sql = sprintf ( "INSERT INTO %s (id, sid, lid, ip, hst, fech) VALUES (%u, %u, %u, '%s', '%s', %u)", "dir_log", 0, $sid, $lid, $_SESSION ['uip'], $_SESSION ['unh'], $_SESSION ['f'] );
		$cls_cfg->query ( $sql );
		// Register clic on items
		$sql = sprintf ( "UPDATE %s SET hits = hits+1 WHERE lid = %u", $tab, $lid );
		$cls_cfg->query ( $sql );
	}
}