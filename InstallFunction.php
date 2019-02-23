<?php
defined('XOOPS_ROOT_PATH') || die("XOOPS root path not defined");

#----檢查類
// 檢查資料表是否存在(資料表)
if (!function_exists("chk_isTable")) {
	function chk_isTable($tbl_name = "") {
		global $xoopsDB;
		if (!$tbl_name) {
			return;
		}
		#注意這裡資料表必須加「''」
		$sql = "SHOW TABLES LIKE '" . $xoopsDB->prefix($tbl_name) . "'"; //die($sql);

		$result = $xoopsDB->queryF($sql); //die($sql);
		if ($xoopsDB->getRowsNum($result)) {
			//資料表存在
			return true;
		}
		//資料表不存在
		return false;
	}
}

//檢查某欄位是否存在(欄名,資料表)
if (!function_exists("chk_isColumn")) {
	function chk_isColumn($col_name = "", $tbl_name = "") {
		global $xoopsDB;
		if (!$col_name and $tbl_name) {
			return;
		}

		//SHOW COLUMNS FROM `show_kind` LIKE 'sn1'
		$sql = "SHOW COLUMNS FROM " . $xoopsDB->prefix($tbl_name) . " LIKE '{$col_name}'";
		$result = $xoopsDB->queryF($sql); //die($sql);
		if ($xoopsDB->getRowsNum($result)) {
			return true;
		}
		//欄位存在
		return false; //欄位不存在
	}
}


//檢查ugm modules 的 system (name and kind)是否有變數
if (!function_exists("check_ugm_module_system_nameKind")) {
	function check_ugm_module_system_nameKind($name, $kind, $tbl) {
		global $xoopsDB;
		$sql = "select sn
        from " . $xoopsDB->prefix($tbl) . "
        where name='{$name}' and kind='{$kind}'"; //die($sql);
		$result = $xoopsDB->query($sql);
		list($sn) = $xoopsDB->fetchRow($result);
		if (empty($sn)) {
			return false;
		}
		return true;
	}
}

//檢查ugm modules 的 kind (kind and ps)是否有變數 1.12
if (!function_exists("check_ugm_module_kind_kindPs")) {
	function check_ugm_module_kind_kindPs($kind, $ps, $tbl) {
		global $xoopsDB;
		$sql = "select sn
        from " . $xoopsDB->prefix($tbl) . "
        where `kind`='{$kind}' and `ps`='{$ps}'"; //die($sql);
		$result = $xoopsDB->query($sql);
		list($sn) = $xoopsDB->fetchRow($result);
		return $sn;
	}
}

//檢查ugm modules 的 block_format (kind_sn and block)是否有變數 1.12
if (!function_exists("check_ugm_module_block_format_kind_snBlock")) {
	function check_ugm_module_block_format_kind_snBlock($kind_sn, $block, $tbl) {
		global $xoopsDB;
		$sql = "select sn
        from " . $xoopsDB->prefix($tbl) . "
        where `kind_sn`='{$kind_sn}' and `block`='{$block}'"; //die($sql);
		$result = $xoopsDB->query($sql);
		list($sn) = $xoopsDB->fetchRow($result);
		return $sn;
	}
}

//處理前將 「ugm_theme_2_kind」=> kind='block' 的 col_sn 設為 1 (1.12)
if (!function_exists("setup_ugm_module_kind")) {
	function setup_ugm_module_kind($kind,$col_sn,$tbl) {
		global $xoopsDB;
    $sql="UPDATE `" . $xoopsDB->prefix($tbl) . "` SET
            `col_sn` = '{$col_sn}'
            WHERE `kind` = '{$kind}'
         ";
    $xoopsDB->queryF($sql) or web_error($sql);
	}
}

//處理後把 「ugm_theme_2_kind」=> kind='block' col_sn=1 刪除，且把「ugm_theme_2_block_format」kind_sn 刪除 (1.12)
if (!function_exists("delete_ugm_module_kind")) {
	function delete_ugm_module_kind($kind,$col_sn,$tbl,$tbl_block_format) {
		global $xoopsDB;

		$sql = "select sn
        from " . $xoopsDB->prefix($tbl) . "
        where `kind`='{$kind}' and `col_sn`='{$col_sn}'"; //die($sql);
		$result = $xoopsDB->query($sql);
		while(list($kind_sn) = $xoopsDB->fetchRow($result)){
			$sql="DELETE FROM `" . $xoopsDB->prefix($tbl_block_format) . "`
 						WHERE `kind_sn` = '{$kind_sn}'
			";
      $xoopsDB->queryF($sql) or web_error($sql);
		}

		$sql="DELETE FROM `" . $xoopsDB->prefix($tbl) . "`
					WHERE `kind`='{$kind}' and `col_sn`='{$col_sn}'
		";
    $xoopsDB->queryF($sql) or web_error($sql);
	}
}
#----建立類

########################################
# 建立資料表
########################################
if (!function_exists("createTable")) {
	function createTable($sql) {
	  global $xoopsDB;
	  $xoopsDB->queryF($sql); //die($sql);
	  return true;
	}
}

//建立目錄
if (!function_exists("mk_dir")) {
	function mk_dir($dir = "") {
		//若無目錄名稱秀出警告訊息
		if (empty($dir)) {
			return;
		}

		//若目錄不存在的話建立目錄
		if (!is_dir($dir)) {
			umask(000);
			//若建立失敗秀出警告訊息
			mkdir($dir, 0777);
		}
	}
}

//拷貝目錄
if (!function_exists("full_copy")) {
	function full_copy($source = "", $target = "") {
		if (is_dir($source)) {
			@mkdir($target);
			$d = dir($source);
			while (FALSE !== ($entry = $d->read())) {
				if ($entry == '.' || $entry == '..') {
					continue;
				}

				$Entry = $source . '/' . $entry;
				if (is_dir($Entry)) {
					full_copy($Entry, $target . '/' . $entry);
					continue;
				}
				copy($Entry, $target . '/' . $entry);
			}
			$d->close();
		} else {
			copy($source, $target);
		}
	}
}

########################################
# 增加欄位
#  $tbl
#  $sql
########################################
if (!function_exists("go_addColumn")) {
	function go_addColumn($tbl, $sql) {
		global $xoopsDB;
		$sql =
		"ALTER TABLE " . $xoopsDB->prefix($tbl) . "
	   ADD
	   {$sql}";
		$xoopsDB->queryF($sql);
	}

}
########################################
# 增加外鍵
# $sql
########################################
if (!function_exists("go_addForeignKey")) {
	function go_addForeignKey($sql) {
		global $xoopsDB;
		$xoopsDB->queryF($sql);
	}	
}

########################################
# 增加資料表
# $sql
########################################
if (!function_exists("go_addTable")) {
	function go_addTable($sql) {
		global $xoopsDB;
		$xoopsDB->queryF($sql);
	}	
}

#----得到類
if(!function_exists("getColumnType")){
	function getColumnType($col,$tbl){
		global $xoopsDB;
		if (!$col or !$tbl) {
			return;
		}

	  $sql = "show columns from `".$xoopsDB->prefix($tbl)."`
	          where `Field` = '{$col}'
	  ";//die($sql);
	  $result = $xoopsDB->queryF($sql);
	  $row = $xoopsDB->fetchArray($result);
	  return $row;
	}
}

if (!function_exists("rename_win")) {
	function rename_win($oldfile, $newfile) {
		if (!rename($oldfile, $newfile)) {
			if (copy($oldfile, $newfile)) {
				unlink($oldfile);
				return TRUE;
			}
			return FALSE;
		}
		return TRUE;
	}
}

if (!function_exists("delete_directory")) {
	function delete_directory($dirname) {
		if (is_dir($dirname)) {
			$dir_handle = opendir($dirname);
		}

		if (!$dir_handle) {
			return false;
		}

		while ($file = readdir($dir_handle)) {
			if ($file != "." && $file != "..") {
				if (!is_dir($dirname . "/" . $file)) {
					unlink($dirname . "/" . $file);
				} else {
					delete_directory($dirname . '/' . $file);
				}

			}
		}
		closedir($dir_handle);
		rmdir($dirname);
		return true;
	}
}

//檢查必要之群組是否已經建立 for XOOPS 2.5
if (!function_exists("check_group")) {
	function check_group($name="")
	{
	  $member_handler = xoops_gethandler('member');
	  $group = $member_handler->getGroupList();
	  if(!in_array($name,$group)){
	    creat_group($name);
	  }
	  return;
	}	
}
//建立群組
if (!function_exists("creat_group")) {
	function creat_group($name="")
	{
	  $member_handler = xoops_gethandler('member');
	  $new_group = $member_handler->createGroup();
	  $new_group->setVar("name", $name);  
	  $new_group->setVar("description", "請勿修改、刪除群組名稱，否則會造成模組運作不正確。");
	  $member_handler->insertGroup($new_group);
	  return ;
	}
}

//從群組名稱取得群組編號
if (!function_exists("getGroupIdFromName")) {
	function getGroupIdFromName($gname="")
	{
	  $member_handler = xoops_gethandler('member');
	  $group = $member_handler->getGroupList();
	  foreach($group as $gid=>$group_name){
	    if($group_name == $gname){
	      return $gid;
	    }
	  }
	  return;
	}
}
