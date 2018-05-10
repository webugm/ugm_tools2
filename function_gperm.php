<?php
function get_gperm($module_id,$isAdmin,$gperm_itemid_arr,$gperm_name_arr){
	global $xoopsUser,$xoopsDB;
	$gperm = array();
	//權限設定($gperm_name_arr權限名稱、$gperm_itemid_arr權限項目 )
	if($gperm_name_arr and $gperm_itemid_arr){
	  $gperm_handler =& xoops_gethandler('groupperm');
	  #取得群組
	  $groups =($xoopsUser)? $xoopsUser->getGroups():XOOPS_GROUP_ANONYMOUS;
	  //權限設定
	  foreach($gperm_name_arr as $gperm_name =>$gperm_name_title){
	    foreach($gperm_itemid_arr as $gperm_itemid =>$gperm_itemid_v){
	      if($isAdmin){
	        #管理員
	        $gperm[$gperm_name][$gperm_itemid]=true;
	      }else{
	        #非管理員
	        if($gperm_itemid_v['anonymous']){
	          #訪客有權限
	          #檢查是是否開放全部
	          $sql = "select gperm_id
	                  from `".$xoopsDB->prefix('group_permission')."`
	                  where `gperm_groupid` = '0'  and `gperm_modid`='{$module_id}' and `gperm_itemid`='{$gperm_itemid}' and `gperm_name`='{$gperm_name}'";//die($sql);
	          
						$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
						$row = $xoopsDB->fetchArray($result);
	          if($row['gperm_id']){
	            $gperm[$gperm_name][$gperm_itemid]=true;
	            continue;
	          }
	        }
	        if($gperm_handler->checkRight($gperm_name, $gperm_itemid, $groups, $module_id)){
	          //若有權限要做的動作
	          $gperm[$gperm_name][$gperm_itemid]=true;
	        }else{
	          //若沒有權限要做的動作
	          $gperm[$gperm_name][$gperm_itemid]=false;
	        }
	      }

	    }
	  }
	}
	return $gperm;
}