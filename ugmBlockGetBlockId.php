<?php
/* 用「show_func」 取得區塊編號*/
defined('XOOPS_ROOT_PATH') || die("XOOPS root path not defined");
function ugmBlockGetBlockId($show_func = "") {
	global $xoopsDB;

	$sql = "select `bid`
				from " . $xoopsDB->prefix("newblocks") . "
				where `show_func`= '{$show_func}'"; //die($sql);
	//if( $show_func == "ugm_jinghung_b_new_prod")die($sql);

	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
	$row = $xoopsDB->fetchArray($result);
	return $row['bid'];
}