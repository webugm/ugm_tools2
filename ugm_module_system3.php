<?php
/*
1.返回：$_SESSION['return_url']
2.
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
「mCo lorPicker」顏色選擇器

# modules/ugm_tools/ugmTools2.php
$tbl = "ugm_mantor_system";
$kind = "youtube";
#---- youtube 標題
$name = "system_youtube_title";
$$name = $myts->htmlSpecialChars(get_modules_system_var($tbl, $kind, $name));
#---- youtube 內容
$html = 0;
$br = 1;
$name = "system_youtube_content";
$$name = $myts->displayTarea(get_modules_system_var($tbl, $kind, $name), $html, 1, 0, 1, $br);
 */
defined('XOOPS_ROOT_PATH') || die("XOOPS root path not defined");

//引入權限設定語系
include_once XOOPS_ROOT_PATH."/modules/ugm_tools2/language/{$xoopsConfig['language']}/groupperm.php";
###############################################################################
#  顯示單筆
###############################################################################
if (!function_exists("ugm_module_system_showOne")) {
	function ugm_module_system_showOne($sn = "", $kind, $button = false) {
		global $xoopsDB, $tbl;

		$sql = "select a.*
	          from      " . $xoopsDB->prefix($tbl) . "  as a
	          where a.sn='{$sn}'
	          "; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
		$DBV = $xoopsDB->fetchArray($result);
		$DBV['enable'] = $DBV['enable'] ? "<img src='../images/on.png' />" : "<img src='../images/off.png' />";
		$DBV['date'] = date("Y-m-d", xoops_getUserTimestamp($DBV['date']));
		if ($kind == "system") {
			$DBV['form_title'] = _MD_UGM_SHOW10_NEWS_PRE_SYS;
		} elseif ($kind == "page") {
			$DBV['form_title'] = _MD_UGM_SHOW10_NEWS_PRE_PAGE;
		} else {
			$DBV['form_title'] = _MD_UGM_SHOW10_NEWS_PRE_NEWS;
		}
		$button = $button ? "
	    <button onclick=\"window.location.href='?op=op_form&kind={$DBV['kind']}&sn={$DBV['sn']}'\" class='btn  btn-success '>" . _EDIT . "</button>
	    <button onclick=\"window.location.href='?op=op_form&kind={$DBV['kind']}'\" class='btn  btn-primary '>" . _ADD . "</button>
	    <button type='button' class='btn btn-warning' onclick=\"location.href='{$_SESSION['return_url']}'\">" . _BACK . "</button>
	  " : "";
		#--------------
		$main = "
	    <div class='container-fluid'>
	      <div class='row'>
	        <div class='panel panel-default'>
	          <div class='panel-heading'>
	            <h3 class='panel-title'>{$DBV['form_title']}</h3>
	          </div>
	          <div class='panel-body'>
	            <div style='margin-bottom:10px;'>{$button}</div>
	            <table id='form_table' class='table table-bordered table-condensed table-hover' >
	              <tr>
	                <th class='col-sm-2 text-center'>" . _MD_UGM_SHOW10_NEWS_TITLE . "</th>
	                <td class='col-sm-10'>
	                  {$DBV['title']}
	                </td>
	              </tr>
	              <tr>
	                <th class='col-sm-2 text-center'>" . _MD_UGM_SHOW10_NEWS_ENABLE . "</th>
	                <td class='col-sm-10'>
	                  {$DBV['enable']}
	                </td>
	              </tr>
	              <tr>
	                <th class='col-sm-2 text-center'>" . _MD_UGM_SHOW10_NEWS_DATE . "</th>
	                <td class='col-sm-10'>
	                  {$DBV['date']}
	                </td>
	              </tr>
	              <!--內容-->
	              <tr>
	                <th class='text-center'>" . _MD_UGM_SHOW10_NEWS_CONTENT . "</th>
	                <td>
	                  {$DBV['content']}
	                </td>
	              </tr>
	            </table>
	          </div>
	        </div>

	  ";
		return $main;
	}
}
###############################################################################
#  更新
###############################################################################
if (!function_exists("ugm_module_system_insert")) {
	function ugm_module_system_insert() {
		global $xoopsDB, $xoopsUser,$tbl,$moduleName, $xoopsConfig;

		//---- 過濾資料 ------------------------*/
		//替特殊符號加入脫逸符號，再存入資料庫
		$myts = &MyTextSanitizer::getInstance();

		$_POST['kind'] = $myts->addSlashes($_POST['kind']); //類別
		$_POST['name'] = $myts->addSlashes($_POST['name']); //系統變數名稱
		$_POST['retrun'] = $myts->addSlashes($_POST['retrun']); //系統變數名稱
		$_POST['sn'] = intval($_POST['sn']); //數字
		$_POST['value'] = $myts->addSlashes($_POST['value']); //


		$DBV = get_ugm_module_tbl($_POST['sn'], $tbl);
		$DBV['title'] = constant($DBV['title']);

		//print_r($DBV);die();
		#防呆
		if (empty($DBV)) {
			redirect_header($_SESSION['return_url'], 3, "錯誤！！");
		}

		if ($DBV['formtype'] == "file") {
			#上傳單張圖片
			if ($DBV['valuetype'] == "single_img") {
				$value = json_decode($DBV['value'], true);

		    #----單圖上傳  
		    $moduleName = $moduleName;             //專案名稱
		    $subdir = "system"; //子目錄
		    $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);
		    
		    $col_name = $_POST['name'];                           //資料表關鍵字
		    $col_sn = $_POST['sn'];                               //商品流水號
		    $name = $_POST['name'];                               //欄位名稱
		    $multiple = false;                                    //單檔 or 多檔上傳
		    $main_width =$value['main_width'];                    //大圖壓縮尺吋，-1則不壓縮
		    $thumb_width =$value['thumb_width'];                  //小圖壓縮尺吋
		    $ugmUpFiles->upload_file($name,$col_name,$col_sn,$multiple,$main_width,$thumb_width);
		    #------------------------------------------------
			
			} elseif ($DBV['valuetype'] == "multiple_img") {

				$safe_name = true;
				$multiple = true;
				$dir_name = "/system/" . $DBV['kind'];
				$col_name = $DBV['name'];
				$ugmUpFiles = new ugmUpFiles($moduleName, $dir_name, NULL, "", "/thumbs", $multiple);

				$ugmUpFiles->set_col($col_name, $DBV['sn']);
				#上傳多檔圖片($col_name,主圖寬,縮圖寬,$file_sn,$desc,$safe_name,false)
				$ugmUpFiles->upload_file($col_name, $DBV['value'], 120, NULL, "", $safe_name, false);
			}
			return $_POST['sn'];
		}
		//-------------------------------------------------------*/
		#更新系統變數值
		$sql = "update " . $xoopsDB->prefix($tbl) . "
	        set
	        `value`='{$_POST['value']}'
	        where `sn`='{$_POST['sn']}'"; //die($sql);
		$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, $sql);

		return $_POST['sn'];
	}

}

#############################################
#  系統變數列表
#  只撈 enable=1
#############################################
if (!function_exists("ugm_module_system_list")) {
	function ugm_module_system_list($kind = "config", $name = "") {
		global $xoopsDB, $xoopsTpl, $tbl, $kind_arr,$moduleName;

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
			$DBV['sn'] = intval($DBV['sn']);
			$DBV['name'] = $myts->htmlSpecialChars($DBV['name']);
			$DBV['kind'] = $myts->htmlSpecialChars($DBV['kind']);
			#以表單型態分類
			if ($DBV['formtype'] == "textbox" or $DBV['formtype'] == "textarea" or $DBV['formtype'] == "select") {
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

			  #----單檔圖片上傳
			  $moduleName = $moduleName;       //專案名稱
			  $subdir     = "system";       //子目錄
			  $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);//實體化

			  $col_name   = $DBV['name'];   //資料表關鍵字
			  $col_sn     = $DBV['sn'];     //關鍵字流水號
			  $thumb = true ; //顯示縮圖
			  $DBV['value'] = $ugmUpFiles->get_rowPicSingleUrl($col_name,$col_sn,$thumb);
			  #-----------------------------------
			  if($DBV['value']){				
					$DBV['value'] = "<img src='{$DBV['value']}' class='img-responsive center-block'>";
			  }



			} elseif ($DBV['formtype'] == "yesno") {
				$DBV['value'] = ($DBV['value']) ? _YES : "<span class='text-danger'>" . _NO . "</span>";
			} elseif ($DBV['formtype'] == "mColorPicker") {
				$color = ($DBV['value'] == "000000") ? "#FFFFFF" : "#000000";
				//$color = ($DBV['value'] == "000000") ? "FFFFFF" : $DBV['value'];
				$DBV['value'] = "<span style='background-color:#{$DBV['value']};color:{$color};padding:10px;'>{$DBV['value']}</span>";
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

################################################
#  編輯表單
################################################
if (!function_exists("ugm_module_system_form")) {
	function ugm_module_system_form($sn = "", $kind = "system", $name = "") {
		global $xoopsDB, $xoopsUser, $tbl, $xoopsTpl, $moduleName, $kind_arr;
		//----------------------------------*/
		//抓取預設值
		if (!empty($sn)) {
			$DBV = get_ugm_module_tbl($sn, $tbl);
		} elseif (!empty($name)) {
			$sql = "select *
            from " . $xoopsDB->prefix($tbl) . "
            where name='{$name}' and kind='{$kind}'";
			$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
			$DBV = $xoopsDB->fetchArray($result);
		}

		$pre = _EDIT;

		#防呆
		if (empty($DBV)) {
			redirect_header($_SESSION['return_url'], 3, "錯誤！！");
		}

		$DBV['form_title'] = $pre . $kind_arr[$kind]['title'];

		//預設值設定 sn  name  title value description formtype  valuetype sort  enable  kind
		//設定「sn」欄位預設值
		$DBV['sn'] = (!isset($DBV['sn'])) ? "" : $DBV['sn'];
		//設定「表單名稱」欄位預設值
		$DBV['name'] = (!isset($DBV['name'])) ? "" : $DBV['name'];
		//設定「標題」欄位預設值
		$DBV['title'] = (!isset($DBV['title'])) ? "" : constant($DBV['title']);
		//設定「值」欄位預設值
		$DBV['value'] = (!isset($DBV['value'])) ? "" : $DBV['value'];
		//設定「描述」欄位預設值
		$DBV['description'] = (!isset($DBV['description'])) ? "" : constant($DBV['description']);
		//設定「表單型態」欄位預設值
		$DBV['formtype'] = (!isset($DBV['formtype'])) ? "" : $DBV['formtype'];
		//設定「值的型態」欄位預設值
		$DBV['valuetype'] = (!isset($DBV['valuetype'])) ? "" : $DBV['valuetype'];
		//設定「排序」欄位預設值
		$DBV['sort'] = (!isset($DBV['sort'])) ? "" : $DBV['sort'];
		//設定「狀態」欄位預設值
		$DBV['enable'] = (!isset($DBV['enable'])) ? "" : $DBV['enable'];
		//設定「類別」欄位預設值
		$DBV['kind'] = (!isset($DBV['kind'])) ? "" : $DBV['kind'];

		//設定「類別」欄位預設值
		$DBV['options'] = (!isset($DBV['options'])) ? "" : json_decode($DBV['options'], true);

		$DBV['return_url'] = $_SESSION['return_url'];

		$DBV['op'] = "opInsert";

		if ($DBV['formtype'] == "file") {

			if ($DBV['valuetype'] == "single_img") {
				
			    #----單檔圖片上傳
			    $moduleName = $moduleName; //模組名稱
			    $subdir = "system";                          //子目錄
			    $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);//實體化 

			    $name     = $DBV['name'];//欄位名稱
			    $col_name = $DBV['name'];//資料表關鍵字
			    $col_sn   = $DBV['sn'];  //商品流水號
			    $multiple = false;       //單檔 or 多檔上傳
			    $accept = "image/*";     //可接受副檔名

			    $DBV['form'] = $ugmUpFiles->upform($name,$col_name,$col_sn,$multiple,$accept);


			} elseif ($DBV['valuetype'] == "multiple_img") {
				#上傳多張圖片
				$multiple = true;
				$dir_name = "/system/" . $DBV['kind']; # --- 表單欄位名稱
				$col_name = $DBV['name'];
				$ugmUpFiles = new ugmUpFiles($moduleName, $dir_name, NULL, "", "/thumbs", $multiple);
				$ugmUpFiles->set_col($col_name, $DBV['sn']);
				#上傳html語法(表單名稱，上傳類型，顯示舊圖，驗證)
				$DBV['form'] = $ugmUpFiles->upform($col_name, "image/*", "show");
			}
		} elseif ($DBV['formtype'] == "textbox") {
			$DBV['form'] = "<input type='text' class='form-control' name='value' id='value' value='{$DBV['value']}'>";
		} elseif ($DBV['formtype'] == "textarea") {
			$DBV['form'] = "<textarea class='form-control' rows='5' id='value' name='value'>{$DBV['value']}</textarea>";
		} elseif ($DBV['formtype'] == "fck") {
			//內容#資料放「content」
			# ======= ckedit====
			if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/ck.php")) {
				redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50", 3, _TAD_NEED_TADTOOLS);
			}
			include_once XOOPS_ROOT_PATH . "/modules/tadtools/ck.php";

			#---- 檢查資料夾
			mk_dir(XOOPS_ROOT_PATH . "/uploads/{$moduleName}/system/");
			mk_dir(XOOPS_ROOT_PATH . "/uploads/{$moduleName}/system/{$DBV['kind']}");
			mk_dir(XOOPS_ROOT_PATH . "/uploads/{$moduleName}/system/{$DBV['kind']}/fck");
			mk_dir(XOOPS_ROOT_PATH . "/uploads/{$moduleName}/system/{$DBV['kind']}/fck/image");
			mk_dir(XOOPS_ROOT_PATH . "/uploads/{$moduleName}/system/{$DBV['kind']}/fck/flash");

			$dir_name = $moduleName . "/system/{$DBV['kind']}/fck";
			$ck = new CKEditor($dir_name, "value", $DBV['value'], $moduleName);
			$ck->setHeight(300);
			//pinrt_r($ck);die();
			$DBV['form'] = $ck->render();

		} elseif ($DBV['formtype'] == "yesno") {
			$value_1 = $DBV['value'] ? " checked" : "";
			$value_0 = $DBV['value'] ? "" : " checked";
			$DBV['form'] = "<input type='radio' name='value' id='value_1' value='1' {$value_1}>\n
    <label for='value_1'>" . _YES . "</label>&nbsp;&nbsp;\n
    <input type='radio' name='value' id='value_0' value='0' {$value_0}>\n
    <label for='value_0'>" . _NO . "</label>";

		} elseif ($DBV['formtype'] == "mColorPicker") {

			$mColorPicker = "
				<script type='text/javascript' src='" . XOOPS_URL . "/modules/ugm_tools/jscolor/jscolor.js'></script>\n
			";

			$DBV['form'] = "{$mColorPicker}<input type='text' class='form-control jscolor' name='value' id='value' value='{$DBV['value']}'>
			";

		} elseif ($DBV['formtype'] == "select"){
			$options = "";
			foreach($DBV['options'] as $k => $v){
				$selected = ($v == $DBV['value']) ? " selected":"";
				$options .= "<option value='{$v}'{$selected}>{$k}</option>";
			}
			if($options){				
				$DBV['form'] = "<select name='value' class='form-control'>{$options}</select>";
			}else{
				$DBV['form'] = "";
			}

		}

		$xoopsTpl->assign('DBV', $DBV);
	}
}