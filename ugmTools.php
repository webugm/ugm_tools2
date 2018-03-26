<?php
/*
version 1.0
date 2018-03-10
use
 */
defined('XOOPS_ROOT_PATH') || die("XOOPS root path not defined");
include_once "ugm_tools_header.php";


#####################################################################################
#  取得系統變數值
#  get_modules_system_var($tbl,$kind,$name,$key);
#  (資料表,類別,欄名,資料庫欄名)#
#  給前台使用，只撈 enable=1
#####################################################################################
if (!function_exists("get_modules_system_var")) {
	function get_modules_system_var($tbl = "", $kind = "", $name = "", $key = "value") {
		global $xoopsDB;
		if (empty($tbl) or empty($kind) or empty($name)) {
			return;
		}

		$sql = "select `{$key}`
      from " . $xoopsDB->prefix($tbl) . "
      where `kind`='{$kind}' and `name`='{$name}' and enable='1'"; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		list($value) = $xoopsDB->fetchRow($result);
		return $value;
	}
}

#####################################################################################
#  取得系統變數值
#  get_modules_system_var($tbl,$kind,$name,$key);
#  (資料表,類別,欄名,資料庫欄名)#
#  給前台使用，只撈 enable=1
#####################################################################################
if (!function_exists("get_modules_system_varBYsn")) {
	function get_modules_system_varBYsn($sn,$tbl) {
		global $xoopsDB;
		if (empty($sn) or empty($tbl)) {
			return;
		}

		$sql = "select `value`
      from " . $xoopsDB->prefix($tbl) . "
      where `sn`='{$sn}'and enable='1'"; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		list($value) = $xoopsDB->fetchRow($result);
		return $value;
	}
}

// function get_config_conf_valueBYconf_name($name){
	
// }
















/*----------------------------------------------------*/
###############################################################################
# 設定 ugm_set_meta page_title
################################################################################
if (!function_exists("ugm_module_set_meta")) {
	function ugm_module_set_meta($meta_keywords = "", $meta_description = "", $pagetitle = "", $og_image = "", $author = "", $url = "") {
		global $xoopsDB, $xoopsTpl, $xoTheme;
		#取得關鍵字
		$sql = "select conf_value
          from      " . $xoopsDB->prefix("config") . "
          where  `conf_name`='meta_keywords'"; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		$DBV = $xoopsDB->fetchArray($result);
		//設定頁面關鍵字（用 , 隔開）
		$meta_keywords = $DBV['conf_value'] ? $meta_keywords . "," . $DBV['conf_value'] : $meta_keywords;

		$sql = "select conf_value
          from      " . $xoopsDB->prefix("config") . "
          where  `conf_name`='meta_description'"; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		$DBV = $xoopsDB->fetchArray($result);
		//設定關鍵字說明
		$meta_description = $DBV['conf_value'] ? $meta_description . "," . $DBV['conf_value'] : $meta_description;

		if (isset($xoTheme) && is_object($xoTheme)) {
			$xoTheme->addMeta('meta', 'keywords', $meta_keywords);
			$xoTheme->addMeta('meta', 'description', $meta_description);
			if ($author) {
				$xoTheme->addMeta('meta', 'author', $author);
			}
		} else {
			// Compatibility for old Xoops versions
			$xoopsTpl->assign('xoops_meta_keywords', $meta_keywords);
			$xoopsTpl->assign('xoops_meta_description', $meta_description);
			if ($author) {
				$xoopsTpl->assign('xoops_meta_author', $author);
			}
		}

		$xoopsTpl->assign('xoops_pagetitle', $pagetitle);

		$og_image = $og_image ? "<meta property='og:image' content='{$og_image}'>\n<link href='{$og_image}' rel='image_src' type='image/jpeg'>\n" : "";
		$url = $url ? "<meta property='og:url' content='{$url}' />\n" : "";

		if ($og_image or $url) {
			$xoopsTpl->assign('xoops_module_header', $url . $og_image);
		}
	}
}

#####################################################################################
#  get_ugm_module
#  自動取得(排序欄位,資料表)的最新排序
#  get_ugm_module_max_sort($col,$tbl,$kind_key,$kind)
#  (排序欄位,資料表,key欄位,key)#
#####################################################################################
if (!function_exists("get_ugm_module_max_sort")) {
	function get_ugm_module_max_sort($col = "sort", $tbl = "", $kind_key = "", $kind = "") {
		global $xoopsDB;
		if (empty($col) or empty($tbl)) {
			return;
		}
		$and_key = $kind_key ? " where `$kind_key`='{$kind}'" : "";

		$sql = "select max({$col}) from " . $xoopsDB->prefix($tbl) . "{$and_key}";
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		list($sort) = $xoopsDB->fetchRow($result);
		return ++$sort;
	}
}

####################################################################################
#  以$sql取得某筆分類資料
#  get_ugm_module_tbs($sql)
####################################################################################
if (!function_exists("get_ugm_module_sql")) {
	function get_ugm_module_sql($sql = "") {
		global $xoopsDB;
		if (empty($sql)) {
			return;
		}

		//$sql = "select * from ".$xoopsDB->prefix($tbl)." where sn='{$sn}'";
		$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		return $xoopsDB->fetchArray($result);
	}
}

####################################################################################
#  以流水號取得某筆分類資料
#  get_ugm_module_tbs(流水號,資料表)
####################################################################################
if (!function_exists("get_ugm_module_tbl")) {
	function get_ugm_module_tbl($sn = "", $tbl = "") {
		global $xoopsDB;
		if (empty($sn) or empty($tbl)) {
			return;
		}

		$sql = "select * from " . $xoopsDB->prefix($tbl) . " where sn='{$sn}'";
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		$data = $xoopsDB->fetchArray($result);
		return $data;
	}
}

//get_ugm_module end
//
###############################################################################
#  立即寄出
###############################################################################
function send_now($email_arr, $subject, $content) {
	global $xoopsConfig, $xoopsDB;
	//sendMail($email, $subject, $body, $headers)
	$xoopsMailer = &getMailer();
	$xoopsMailer->multimailer->ContentType = "text/html";
	$xoopsMailer->addHeaders("MIME-Version: 1.0");
	$msg=array();
	if (!is_array($email_arr)) {
		$email_arr = explode(";", $email_arr);
	}
	foreach ($email_arr as $email) {
		$email = trim($email);
		if (!empty($email)) {
			if($xoopsMailer->sendMail($email, $subject, $content, $headers)){
				$msg[]="寄送成功=>{$email}";
			}
		}
	}
	return $msg;
}
###############################################################################
#  前台工具列
###############################################################################
if (!function_exists('ugm_toolbar_b3')) {
	function ugm_toolbar_b3($interface_menu = array()) {
		global $xoopsUser, $xoopsModule;

		if ($xoopsUser) {
			$module_id = $xoopsModule->getVar('mid'); //取得模組mid
			$isAdmin = $xoopsUser->isAdmin($module_id); //是否有模組管理權
			$mod_name = $xoopsModule->getVar('name'); //取得模組名稱
		} else {
			$isAdmin = false;
			$mod_name = "";
		}

		if (empty($interface_menu)) {
			return;
		}

		$jquery = get_jquery();

		$basename = basename($_SERVER['SCRIPT_NAME']);
		//die($basename);//index.php
		$main .= "
      <div style='margin:20px 0px;'>
        <div class='btn-group'>";
		foreach ($interface_menu as $v) {
			$active = ($v['url'] == $basename) ? " active" : "";
			if ($v['sub']) {
				#有下層
			} else {
				#只有一層
				if ($v['title'] == _TAD_TO_MOD) {
					#首頁
					$main .= "
                <button type='button' class='btn btn-default{$active}' title='{$v['title']}' alt='{$v['title']}' onclick=\"window.location='{$v['url']}';\">
                  <i class='glyphicon glyphicon-home'></i> </span>
                </button>";
				} else {
					#他頁
					$main .= "
                <button type='button' class='btn btn-default{$active}' title='{$v['title']}' alt='{$v['title']}' onclick=\"window.location='{$v['url']}';\">
                  {$v['title']}
                </button>";
				}
			}
		}
		if ($isAdmin and $module_id) {
			$main .= "
            <div class='btn-group'>
              <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
                " . _TAD_TF_MODULE_CONFIG . " <span class='caret'></span>
              </button>
              <ul class='dropdown-menu' role='menu'>
                <li><a href='admin/index.php'>" . sprintf(_TAD_ADMIN, $mod_name) . "</a></li>
                <li><a href='" . XOOPS_URL . "/modules/system/admin.php?fct=preferences&op=showmod&mod={$module_id}'>" . sprintf(_TAD_CONFIG, $mod_name) . "</a></li>
                <li><a href='" . XOOPS_URL . "/modules/system/admin.php?fct=blocksadmin&op=list&filter=1&selgen={$module_id}&selmod=-2&selgrp=-1&selvis=-1'>" . sprintf(_TAD_BLOCKS, $mod_name) . "</a></li>
              </ul>
            </div>
          ";
		}
		$main .= "
            </div>
          </div>";
		return $main;
	}
}

###############################################################################
#  取得目前網址
###############################################################################
if (!function_exists("getCurrentUrl")) {
	function getCurrentUrl() {
		global $_SERVER;
		$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http' : 'https';
		$host = $_SERVER['HTTP_HOST'];
		$script = $_SERVER['SCRIPT_NAME'];
		$params = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : "";

		$currentUrl = $protocol . '://' . $host . $script . $params;
		//die( $currentUrl);
		return $currentUrl;
	}
}

###############################################################################
#  獲得填報者ip
###############################################################################
if (!function_exists("getVisitorsAddr")) {
	function getVisitorsAddr() {
		if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		} elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else {
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		return $ip;
	}
}

################################################################################
#   檢查某資料表的某欄位是否有資料(資料表,key欄位名,key值)
#   1. 有資料 ->  true
#   2. 無資料 ->  false
#
################################################################################
if (!function_exists("check_tbl_col_sn")) {
	function check_tbl_col_sn($tbl = "", $col = "", $sn = "") {
		global $xoopsDB;
		if (empty($tbl) or empty($col) or empty($sn)) {
			return false;
		}

		$sql = "select count(*) from " . $xoopsDB->prefix($tbl) . "
          where `{$col}`='{$sn}'";
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		list($count) = $xoopsDB->fetchRow($result);
		if ($count) {
			return true;
		}

		return false;
	}
}
################################################################################
# 得到網站管理員的信箱
################################################################################
if (!function_exists("get_xoops_admin_email")) {
	function get_xoops_admin_email() {
		global $xoopsDB; //groups_users_link =>groupid=1
		$sql = "select b.email
          from      " . $xoopsDB->prefix("groups_users_link") . " as a
          left join " . $xoopsDB->prefix("users") . "             as b on a.uid = b.uid
          where `groupid`='1'"; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		while ($all = $xoopsDB->fetchArray($result)) {
			$DBV[] = $all['email'];
		}
		return $DBV;
	}
}

################################################################################
#   燈箱打包碼(內容,標題)
################################################################################
if (!function_exists("show_lytebox_html")) {
	function show_lytebox_html($content, $title) {
		global $xoopsModule;
		$DIRNAME = $xoopsModule->getVar('dirname');
		$jquery = get_jquery();
		$main = "
    <!DOCTYPE html>
    <html lang='" . _LANGCODE . "'>
      <head>
        <meta charset='" . _CHARSET . "'>
        <title></title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link rel='stylesheet' type='text/css' media='screen' href='" . XOOPS_URL . "/modules/tadtools/bootstrap/css/bootstrap.css' />
        <link rel='stylesheet' type='text/css' media='screen' href='" . XOOPS_URL . "/modules/tadtools/bootstrap/css/bootstrap-responsive.css' />
        <link rel='stylesheet' type='text/css' media='screen' href='" . XOOPS_URL . "/modules/tadtools/css/xoops_adm.css' />
        <link rel='stylesheet' type='text/css' media='screen' href='" . XOOPS_URL . "/modules/{$DIRNAME}/css/module.css' />
      </head>
      <body>
        <div class='container'>
          <h1>{$title}</h1>
          $content
        </div>
        $jquery
        <script src='" . XOOPS_URL . "/modules/tadtools/bootstrap/js/bootstrap.min.js'></script>
      </body>
    </html>
    ";
		return $main;
	}
} ################################################################################
#   燈箱打包碼(內容,標題)
################################################################################
if (!function_exists("show_lytebox_html_b3")) {
	function show_lytebox_html_b3($content, $title) {
		global $xoopsModule;
		$DIRNAME = $xoopsModule->getVar('dirname');
		$jquery = get_jquery(true);
		$main = "
  <!DOCTYPE html>
  <html  lang= '" . _LANGCODE . "' >
  <head>
  <meta  charset= '" . _CHARSET . "' >
  <meta  http-equiv= 'X-UA-Compatible'  content= 'IE=edge' >
  <meta  name= 'viewport'  content= 'width=device-width, initial-scale=1'>
  <!--上述3個meta標籤*必須*放在最前面，任何其他內容都*必須*跟隨其後！-->
  <title>{$title}</title>

  <!-- Bootstrap -->
  <link  href= '" . XOOPS_URL . "/modules/tadtools/bootstrap3/css/bootstrap.css'  rel= 'stylesheet' >

  <!-- xoops -->
  <link  href= '" . XOOPS_URL . "/modules/ugm_tools/css/xoops_adm3.css'  rel= 'stylesheet' >
  <link  href= '" . XOOPS_URL . "/modules/ugm_tools/css/forms.css'  rel= 'stylesheet' >
  <link  href= '" . XOOPS_URL . "/modules/{$DIRNAME}/css/module_b3.css'  rel= 'stylesheet' >

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src='http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js'></script>
    <script src='http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js'></script>
  <![endif]-->

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  {$jquery}
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src= '" . XOOPS_URL . "/modules/tadtools/bootstrap3/js/bootstrap.min.js' ></script>
  </head>
  <body  id='home'>
    {$content}
  </body>
  </html>
  ";
		return $main;
	}
}
###############################################################################
# 日期選單
################################################################################
if (!function_exists("js_dateTime_code")) {
	function js_dateTime_code() {
		$main = "
  <script type='text/javascript' src='" . XOOPS_URL . "/modules/tadtools/My97DatePicker/WdatePicker.js'></script>
  ";
		return $main;
	}
}
###############################################################################
# 啟用 停用 option
################################################################################
if (!function_exists("get_use_enable_option")) {
#取得enable選項
	function get_use_enable_option($enable = "1") {
		$checked_1 = $enable ? " checked" : "";
		$checked_0 = !$enable ? " checked" : "";
		$main = "
  <input type='radio' name='enable' id='enable_1' value='1'{$checked_1}>
  <label for='enable_1' class='checkbox-inline'>本文</label>
  <input type='radio' name='enable' id='enable_0' value='0'{$checked_0}>
  <label for='enable_0' class='checkbox-inline'>草稿</label>
  ";
		return $main;
	}
}


#####################################################################################
#  檢查資料表是否有系統變數
#  check_modules_system_var($tbl,$kind,$name);
#  (資料表,類別,欄名)#
#  給後台使用，不管 enable=1
#  回傳查到的sn
#####################################################################################
if (!function_exists("check_modules_system_var")) {
	function check_modules_system_var($tbl = "", $kind = "", $name = "") {
		global $xoopsDB;
		if (empty($tbl) or empty($kind) or empty($name)) {
			return;
		}

		$sql = "select `sn`
      from " . $xoopsDB->prefix($tbl) . "
      where `kind`='{$kind}' and `name`='{$name}'"; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		list($sn) = $xoopsDB->fetchRow($result);
		return $sn;
	}
}

#####################################################################################
#  建立系統變數
#  create_modules_system_var($tbl="",$kind="",$name="",$title="",$value="",$description="",$formtype="",$valuetype="");
#  (資料表,類別,欄名,中文，值，欄位說明，表單型態，值型態)#
#  給後台使用，不管 enable=1
#  回傳true
#####################################################################################
if (!function_exists("create_modules_system_var")) {
	function create_modules_system_var($tbl = "", $kind = "", $name = "", $title = "", $value = "", $description = "", $formtype = "", $valuetype = "") {
		global $xoopsDB;
		if (empty($tbl) or empty($kind) or empty($name)) {
			return;
		}

		#排序處理(變數是唯一，故排序為1)

		$sql = "insert into " . $xoopsDB->prefix($tbl) . "
          (`name`,`title`,`value`,`description`,`formtype`,`valuetype`,`sort`,`enable`,`kind`) values
          ('{$name}','{$title}','{$value}','{$description}','{$formtype}','{$valuetype}','1','1','{$kind}')"; //die($sql);
		$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		return true;
	}
}

#############################################
#  系統變數列表
#  只撈 enable=1
#############################################
if (!function_exists("modules_system_list")) {
	function modules_system_list($kind = "system", $name = "") {
		global $xoopsDB, $xoopsTpl, $tbl, $kind_arr, $DIRNAME;
		#預設Foreign key=> system
		#---- 防呆
		if (!in_array($kind, array_keys($kind_arr))) {
			$kind = "system";
		}

		# ----得到Foreign key選單 ----------------------------
		$kind_option = "";
		foreach ($kind_arr as $key => $value) {
			$selected = "";
			if ($kind == $key) {
				$selected = " selected";
			}
			$kind_option .= "<option value='{$key}'{$selected}>{$value['title']}</option>";
		}
		$kind_form = "
		    <select name='kind' id='kind'class='form-control' onchange=\"location.href='?kind='+this.value\">
		      $kind_option
		    </select>
		  ";
		$xoopsTpl->assign('kind_form', $kind_form);
		$xoopsTpl->assign('kind', $kind);
		#-------------------------------------------

		$sql = "select *
          from " . $xoopsDB->prefix($tbl) . " as a
          where kind='{$kind}' and enable='1'
          order by a.sort"; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());

		$myts = MyTextSanitizer::getInstance();
		$i = 1;
		while ($DBV = $xoopsDB->fetchArray($result)) {
			#---- 過濾讀出的變數值 ----
			#sn name  title value description formtype  valuetype sort  enable  kind
			$DBV['name'] = $myts->htmlSpecialChars($DBV['name']);
			$DBV['kind'] = $myts->htmlSpecialChars($DBV['kind']);
			/*
				    「yesno」是否的單選框
				    「select」下拉選單
				    「select_multi」可複選的下拉選單
				    「group」群組下拉選單
				    「group_multi」可複選的群組下拉選單
				    「textbox」文字框
				    「textarea」大量文字框
				    「user」已註冊使用者下拉選單
				    「user_multi」可複選的已註冊使用者下拉選單
				    「timezone」時區下拉選單
				    「language」語系下拉選單

			*/

			if ($DBV['formtype'] == "textbox" or $DBV['formtype'] == "textarea") {
				#---- 文字框
				$html = 0;
				$br = 1;
				$DBV['value'] = $myts->displayTarea($DBV['value'], $html, 1, 0, 1, $br);
			} elseif ($DBV['formtype'] == "fck") {
				#---- fck編輯器
				$html = 1;
				$br = 0;
				$DBV['value'] = $myts->displayTarea($DBV['value'], $html, 1, 0, 1, $br);
			} elseif ($DBV['formtype'] == "file") {
				if ($DBV['valuetype'] == "single_img") {
					#---- 單圖
					$multiple = false;
				} elseif ($DBV['valuetype'] == "multiple_img") {
					#---- 多圖
					$multiple = true;
				}
				$dir_name = "/system/" . $DBV['kind'];
				$col_name = $DBV['name'];
				$show_del = false;
				$ugmUpFiles = new ugmUpFiles($DIRNAME, $dir_name, NULL, "", "/thumbs", $multiple);
				$ugmUpFiles->set_col($col_name, $DBV['sn']);
				$DBV['value'] = $ugmUpFiles->list_show_file_b3($show_del);
			} elseif ($DBV['formtype'] == "yesno") {
				$DBV['value'] = ($DBV['value']) ? _YES : "<span class='text-danger'>" . _NO . "</span>";
			}

			$DBV['title'] = $myts->htmlSpecialChars(constant($DBV['title']));
			$DBV['description'] = $myts->htmlSpecialChars(constant($DBV['description']));
			$DBV['sort'] = $i;
			$i++;
			$list[] = $DBV;
		}
		# ------------------------------------------------------------
		$xoopsTpl->assign("list", $list);
	}
}

#####################################################################################
# 檢查是否有這筆記錄
#####################################################################################
if (!function_exists("checkIsRecord")) {
	function checkIsRecord($sn = "", $tbl = "", $key = "sn") {
		global $xoopsDB;
		if (empty($sn) or empty($tbl)) {
			return;
		}

		$sql = "select count(*) from " . $xoopsDB->prefix($tbl) . "
            where `{$key}`='{$sn}'";
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		list($count) = $xoopsDB->fetchRow($result);
		if ($count) {
			return true;
		}

		return false;
	}
}

###############################################################################
#  自動更新排序
#  $tbl => 資料表
#  $sort=1 排序值
#  $formColum="tr" => 表單欄位
#  $dbKey => 資料表主鍵
#  $dbSort => 資料表排序欄位
###############################################################################
if (!function_exists("updateSort")) {
	function updateSort($tbl = "", $sort = 1, $formColum = "tr", $dbKey = "sn", $dbSort = "sort") {
		global $xoopsDB;

		$_POST['max_sort'] = intval($_POST['max_sort']);
		$sort = $_POST['max_sort'] ? $_POST['max_sort'] : $sort;
		$ascend = ($sort == 1) ? true : false; //升冪 、降冪

		foreach ($_POST[$formColum] as $sn) {
			$sn = intval($sn);
			if (!$sn) {
				continue;
			}

			$sql = "update " . $xoopsDB->prefix($tbl) . " set `{$dbSort}`='{$sort}' where `{$dbKey}`='{$sn}'";
			$xoopsDB->queryF($sql) or die("Save Sort Fail! (" . date("Y-m-d H:i:s") . ")"); //die($sql);
			if ($ascend) {
				$sort++;
			} else {
				$sort--;
			}
		}
		return "Save Sort OK! (" . date("Y-m-d H:i:s") . ")" . _BP_F5;
	}
}

###############################################################################
#  得到自訂陣列的層數
#  $kind 外部變數
#  $kind_arr 自訂陣列
###############################################################################
if (!function_exists("get_kind_arr_stop_level")) {
	function get_kind_arr_stop_level($kind = "", $kind_arr = "") {
		foreach ($kind_arr as $key => $value) {
			if ($kind == $key) {
				return $value['stop_level'];
			}
		}
	}
}

###############################################################################
#  修改除錯模式
###############################################################################
function ugm_module_debug_mode($v = 0) {
	global $xoopsDB;
	$sql = "update " . $xoopsDB->prefix("config") . " set conf_value='$v' where conf_name='debug_mode'";
	$xoopsDB->queryF($sql) or die($sql . "<br>" . web_error());
}
###########################################################
#  新增點閱數
#  傳入 $sn,$tbl
###########################################################
function InsertCounteAddOne($sn, $tbl) {
	global $xoopsDB;
	#新聞計數+1
	$sql = "update  " . $xoopsDB->prefix($tbl) . "
          set counter =  counter+1
          where  sn ='{$sn}'
  "; //die($sql);
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
	return;
}
###########################################################
#  取得下拉選單的選項(單層)
#  $admin=1 => 不管enable
###########################################################
function ugm_module_get_tbl_option($default="",$tbl="",$admin="1") {
	global $xoopsDB;
	if(!$tbl)return;
	$and_key=$admin? "":" where enable='1'";

	$sql = "select * from 
				 ". $xoopsDB->prefix($tbl) ."
				 ". $and_key ."
         order by sort"; //die($sql) ;
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
	$rows="";
	while ($row = $xoopsDB->fetchArray($result)) {
		$selected = ($default == $row['sn']) ? " selected" : "";
		#這裡要處理enable=0 顏色不一樣
		$rows.="<option value='{$row['sn']}' {$selected}>{$row['title']}</option>";
	}
	return $rows;
}

###############################################################################
#  用模組名稱 取得 模組偏好設定
###############################################################################
if (!function_exists("get_ModuleConfigByModuleName")) {
	function get_ModuleConfigByModuleName($ModuleName="") {
		if(!$ModuleName) return;		
    #得到指定模組 mid
    $module_handler =& xoops_gethandler('module');
    $xoopsModule =& $module_handler->getByDirname($ModuleName);
    $mid = $xoopsModule->getVar('mid');
    #取得該mid的模組參數
    $config_handler = & xoops_gethandler('config');
    $xoopsModuleConfig = & $config_handler->getConfigsByCat(0, $mid);
    return $xoopsModuleConfig;
	}
}

################################################################
#  取得外鍵下拉選單
#  傳入：(選取值, 前/後台)
#  回傳：ForeignKeyForm
################################################################
if (!function_exists("get_ForeignKeyMainOption")) {	
function get_ForeignKeyMainOption($tbl="",$kind_select = "", $admin = true) {
	global $xoopsDB;
	#---- 過濾讀出的變數值 ----
	$myts = MyTextSanitizer::getInstance();

	if (!$admin) {
		#前台使用
		$and_key = " where enable='1'";
	}

	$sql = "select sn,title
					from " . $xoopsDB->prefix($tbl) . "
          {$and_key}
          order by sort"; //die($sql);

	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
	$kind_option = "";
	while ($row = $xoopsDB->fetchArray($result)) {
		//以下會產生這些變數： $sn , $ofsn , $title , $enable  ,$sort url target
		$row['sn'] = intval($row['sn']);
		$row['title'] = $myts->htmlSpecialChars($row['title']);

		$selected = "";
		if ($row['sn'] == $kind_select) {
			$selected = " selected";
		}
		$kind_option .= "<option value='{$row['sn']}'{$selected}>{$row['title']}</option>";
	}
	return $kind_option;
}
}


//檢查並傳回欲拿到資料使用的變數
if (!function_exists("filterVar")) {
function filterVar($var, $title = '', $required = true , $type = 'string',$filter=""){  
  #---- 過濾資料 --------------------------
  $myts = &MyTextSanitizer::getInstance();
 
  #先判斷是否有填值
  if($required == true and $var === ""){
    redirect_header(XOOPS_URL, 3,"{$title}為必填！");
  } 

  if($type == "string"){
    $var = $myts->addSlashes($var);//文字
  }elseif($type == "int"){
    $var = intval($var);//整數
  }elseif($type == "float"){
    $var = floatval($var);//小數
  }else{
    $var = $myts->addSlashes($var);//文字
  } 

  if ($filter) {
    $var = filter_var($var, $filter);
    if (!$var) {
    	redirect_header(XOOPS_URL, 3,"不合法的{$title}");
    }
  } 

  return $var;
}
	
}

#################################################
#  取得token form
#################################################
if(!function_exists("getTokenHTML")){
function getTokenHTML(){
  $_SESSION['token'] = substr(md5(uniqid(mt_rand(), 1)), 0, 8);//取得一個亂數

  if (version_compare(PHP_VERSION, '7.0.0') >= 0) {        
    $pass  = password_hash($_SESSION['token'], PASSWORD_DEFAULT);//加密
    return "<input type='hidden' name='token' id='token' value='{$pass}' />";//傳回隱藏token
  } else {
    return "<input type='hidden' name='token' id='token' value='{$_SESSION['token']}' />";//傳回隱藏token
  }
}
}
#################################################
#  verify token 
#################################################
if(!function_exists("verifyToken")){
function verifyToken($hash){

  if (version_compare(PHP_VERSION, '7.0.0') >= 0) {      
    if (password_verify($_SESSION['token'], $hash)) { //判斷token
      return ;
    }
  } else {
    if ($_SESSION['token'] == $hash){
      return ;
    }
  }
  $return = isset($_SESSION['return_url'])?$_SESSION['return_url']:XOOPS_URL;
  redirect_header($return, 3000, 'token 驗證失敗');
}
}

#################################################
#  ajaxDebug 
#################################################
if(!function_exists("ajaxDebug")){
function ajaxDebug($content,$name=""){

  #---- 檢查資料夾
  $fileName=$name."_".time().".txt";
  mk_dir(XOOPS_ROOT_PATH . "/uploads/debug");
  mk_dir(XOOPS_ROOT_PATH . "/uploads/debug/captcha");
  $file = XOOPS_ROOT_PATH . "/uploads/debug/captcha/$fileName";
  $f = fopen($file, 'w'); //以寫入方式開啟文件
  fwrite($f, $content); //將新的資料寫入到原始的文件中
  fclose($f);
}
}