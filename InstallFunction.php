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
