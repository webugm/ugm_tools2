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
		$result = $xoopsDB->query($sql) or mysql_error($sql);
		$row = $xoopsDB->fetchArray($result);
		$row['enable'] = $row['enable'] ? "<img src='../images/on.png' />" : "<img src='../images/off.png' />";
		$row['date'] = date("Y-m-d", xoops_getUserTimestamp($row['date']));
		if ($kind == "system") {
			$row['form_title'] = _MD_UGM_SHOW10_NEWS_PRE_SYS;
		} elseif ($kind == "page") {
			$row['form_title'] = _MD_UGM_SHOW10_NEWS_PRE_PAGE;
		} else {
			$row['form_title'] = _MD_UGM_SHOW10_NEWS_PRE_NEWS;
		}
		$button = $button ? "
	    <button onclick=\"window.location.href='?op=op_form&kind={$row['kind']}&sn={$row['sn']}'\" class='btn  btn-success '>" . _EDIT . "</button>
	    <button onclick=\"window.location.href='?op=op_form&kind={$row['kind']}'\" class='btn  btn-primary '>" . _ADD . "</button>
	    <button type='button' class='btn btn-warning' onclick=\"location.href='{$_SESSION['return_url']}'\">" . _BACK . "</button>
	  " : "";
		#--------------
		$main = "
	    <div class='container-fluid'>
	      <div class='row'>
	        <div class='panel panel-default'>
	          <div class='panel-heading'>
	            <h3 class='panel-title'>{$row['form_title']}</h3>
	          </div>
	          <div class='panel-body'>
	            <div style='margin-bottom:10px;'>{$button}</div>
	            <table id='form_table' class='table table-bordered table-condensed table-hover' >
	              <tr>
	                <th class='col-sm-2 text-center'>" . _MD_UGM_SHOW10_NEWS_TITLE . "</th>
	                <td class='col-sm-10'>
	                  {$row['title']}
	                </td>
	              </tr>
	              <tr>
	                <th class='col-sm-2 text-center'>" . _MD_UGM_SHOW10_NEWS_ENABLE . "</th>
	                <td class='col-sm-10'>
	                  {$row['enable']}
	                </td>
	              </tr>
	              <tr>
	                <th class='col-sm-2 text-center'>" . _MD_UGM_SHOW10_NEWS_DATE . "</th>
	                <td class='col-sm-10'>
	                  {$row['date']}
	                </td>
	              </tr>
	              <!--內容-->
	              <tr>
	                <th class='text-center'>" . _MD_UGM_SHOW10_NEWS_CONTENT . "</th>
	                <td>
	                  {$row['content']}
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
		global $xoopsDB,$xoopsUser,$tbl,$module_name,$xoopsConfig;

		//---- 過濾資料 ------------------------*/
		//替特殊符號加入脫逸符號，再存入資料庫
		$myts = &MyTextSanitizer::getInstance();

		$_POST['kind'] = $myts->addSlashes($_POST['kind']); //類別
		$_POST['name'] = $myts->addSlashes($_POST['name']); //系統變數名稱
		$_POST['retrun'] = $myts->addSlashes($_POST['retrun']); //系統變數名稱
		$_POST['sn'] = intval($_POST['sn']); //數字
		$_POST['value'] = $myts->addSlashes($_POST['value']); //


		$row = get_ugm_module_tbl($_POST['sn'], $tbl);
		$row['title'] = constant($row['title']);

		//print_r($row);die();
		#防呆
		if (empty($row)) {
			redirect_header($_SESSION['return_url'], 3, "錯誤！！");
		}

		if ($row['formtype'] == "file") {
			#上傳單張圖片
			if ($row['valuetype'] == "single_img") {
				$value = json_decode($row['value'], true);

		    #----單圖上傳  
		    //$module_name = $module_name;             //專案名稱
		    $subdir = "system"; //子目錄
		    $ugmUpFiles = new ugmUpFiles($module_name, $subdir);
		    
		    $col_name = $_POST['name'];                           //資料表關鍵字
		    $col_sn = $_POST['sn'];                               //商品流水號
		    $name = $_POST['name'];                               //欄位名稱
		    $multiple = false;                                    //單檔 or 多檔上傳
		    $main_width =$value['main_width'];                    //大圖壓縮尺吋，-1則不壓縮
		    $thumb_width =$value['thumb_width'];                  //小圖壓縮尺吋
		    $ugmUpFiles->upload_file($name,$col_name,$col_sn,$multiple,$main_width,$thumb_width);
		    #------------------------------------------------
			
			} elseif ($row['valuetype'] == "multiple_img") {

				$safe_name = true;
				$multiple = true;
				$dir_name = "/system/" . $row['kind'];
				$col_name = $row['name'];
				$ugmUpFiles = new ugmUpFiles($module_name, $dir_name, NULL, "", "/thumbs", $multiple);

				$ugmUpFiles->set_col($col_name, $row['sn']);
				#上傳多檔圖片($col_name,主圖寬,縮圖寬,$file_sn,$desc,$safe_name,false)
				$ugmUpFiles->upload_file($col_name, $row['value'], 120, NULL, "", $safe_name, false);
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
		global $xoopsDB,$xoopsTpl,$tbl,$kind_arr,$module_name;

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
		$result = $xoopsDB->query($sql) or web_error($sql);

		$myts = MyTextSanitizer::getInstance();
		$i = 1;
		$rows = array();
		while ($row = $xoopsDB->fetchArray($result)) {
			#---- 過濾讀出的變數值 ----
			#sn name  title value description formtype  valuetype sort  enable  kind
			$row['sn'] = intval($row['sn']);
			$row['name'] = $myts->htmlSpecialChars($row['name']);
			$row['kind'] = $myts->htmlSpecialChars($row['kind']);
			
			#以表單型態分類
			if ($row['formtype'] == "textbox" or $row['formtype'] == "textarea") {
				#---- 文字框
				$html = 0;
				$br = 1;
				$row['value'] = $myts->displayTarea($row['value'], $html, 1, 0, 1, $br);
			} elseif ($row['formtype'] == "fck") {
				#---- fck編輯器
				$html = 1;
				$br = 0;
				$row['value'] = $myts->displayTarea($row['value'], $html, 1, 0, 1, $br);
			} elseif ($row['formtype'] == "file") {
				if ($row['valuetype'] == "single_img") {
					#---- 單圖
					$multiple = false;
				} elseif ($row['valuetype'] == "multiple_img") {
					#---- 多圖
					$multiple = true;
				}

			  #----單檔圖片上傳
			  $module_name = $module_name;       //專案名稱
			  $subdir     = "system";       //子目錄
			  $ugmUpFiles = new ugmUpFiles($module_name, $subdir);//實體化

			  $col_name   = $row['name'];   //資料表關鍵字
			  $col_sn     = $row['sn'];     //關鍵字流水號
			  $thumb = true ; //顯示縮圖
			  $row['value'] = $ugmUpFiles->get_rowPicSingleUrl($col_name,$col_sn,$thumb);
			  #-----------------------------------
			  if($row['value']){				
					$row['value'] = "<img src='{$row['value']}' class='img-responsive center-block'>";
			  }

			} elseif ($row['formtype'] == "yesno") {
				$row['value'] = ($row['value']) ? _YES : "<span class='text-danger'>" . _NO . "</span>";
			} elseif ($row['formtype'] == "mColorPicker") {
				$color = ($row['value'] == "000000") ? "#FFFFFF" : "#000000";
				//$color = ($row['value'] == "000000") ? "FFFFFF" : $row['value'];
				$row['value'] = "<span style='background-color:#{$row['value']};color:{$color};padding:10px;'>{$row['value']}</span>";
			}
			$row['title'] = $myts->htmlSpecialChars(constant($row['title']));
			$row['description'] = $myts->htmlSpecialChars(constant($row['description']));
			$row['sort'] = $i;
			$i++;
			$rows[] = $row;
		}

		# ------------------------------------------------------------
		$xoopsTpl->assign("rows", $rows);
	}
}

################################################
#  編輯表單
################################################
if (!function_exists("ugm_module_system_form")) {
	function ugm_module_system_form($sn = "", $kind = "system", $name = "") {
		global $xoopsDB,$xoopsUser,$tbl,$xoopsTpl,$module_name,$kind_arr;
		//----------------------------------*/
		//抓取預設值
		if (!empty($sn)) {
			$row = get_ugm_module_tbl($sn, $tbl);
		} elseif (!empty($name)) {
			$sql = "select *
            from " . $xoopsDB->prefix($tbl) . "
            where name='{$name}' and kind='{$kind}'";
			$result = $xoopsDB->query($sql) or web_error($sql);
			$row = $xoopsDB->fetchArray($result);
		}

		$pre = _EDIT;

		#防呆
		if (empty($row)) {
			redirect_header($_SESSION['return_url'], 3, "錯誤！！");
		}

		$row['form_title'] = $pre . $kind_arr[$kind]['title'];

		//預設值設定 sn  name  title value description formtype  valuetype sort  enable  kind
		//設定「sn」欄位預設值
		$row['sn'] = (!isset($row['sn'])) ? "" : $row['sn'];
		//設定「表單名稱」欄位預設值
		$row['name'] = (!isset($row['name'])) ? "" : $row['name'];
		//設定「標題」欄位預設值
		$row['title'] = (!isset($row['title'])) ? "" : constant($row['title']);
		//設定「值」欄位預設值
		$row['value'] = (!isset($row['value'])) ? "" : $row['value'];
		//設定「描述」欄位預設值
		$row['description'] = (!isset($row['description'])) ? "" : constant($row['description']);
		//設定「表單型態」欄位預設值
		$row['formtype'] = (!isset($row['formtype'])) ? "" : $row['formtype'];
		//設定「值的型態」欄位預設值
		$row['valuetype'] = (!isset($row['valuetype'])) ? "" : $row['valuetype'];
		//設定「排序」欄位預設值
		$row['sort'] = (!isset($row['sort'])) ? "" : $row['sort'];
		//設定「狀態」欄位預設值
		$row['enable'] = (!isset($row['enable'])) ? "" : $row['enable'];
		//設定「類別」欄位預設值
		$row['kind'] = (!isset($row['kind'])) ? "" : $row['kind'];

		//設定「類別」欄位預設值
		$row['options'] = (!isset($row['options'])) ?"" : json_decode($row['options'], true);

		$row['return_url'] = $_SESSION['return_url'];

		$row['op'] = "opInsert";

		if ($row['formtype'] == "file") {

			if ($row['valuetype'] == "single_img") {
				
			    #----單檔圖片上傳
			    //$module_name = $module_name; //模組名稱
			    $subdir = "system";                          //子目錄
			    $ugmUpFiles = new ugmUpFiles($module_name, $subdir);//實體化 

			    $name     = $row['name'];//欄位名稱
			    $col_name = $row['name'];//資料表關鍵字
			    $col_sn   = $row['sn'];  //商品流水號
			    $multiple = false;       //單檔 or 多檔上傳
			    $accept = "image/*";     //可接受副檔名

			    $row['form'] = $ugmUpFiles->upform($name,$col_name,$col_sn,$multiple,$accept);


			} elseif ($row['valuetype'] == "multiple_img") {
				#上傳多張圖片
				$multiple = true;
				$dir_name = "/system/" . $row['kind']; # --- 表單欄位名稱
				$col_name = $row['name'];
				$ugmUpFiles = new ugmUpFiles($module_name, $dir_name, NULL, "", "/thumbs", $multiple);
				$ugmUpFiles->set_col($col_name, $row['sn']);
				#上傳html語法(表單名稱，上傳類型，顯示舊圖，驗證)
				$row['form'] = $ugmUpFiles->upform($col_name, "image/*", "show");
			}
		} elseif ($row['formtype'] == "textbox") {
			$row['form'] = "<input type='text' class='form-control' name='value' id='value' value='{$row['value']}'>";
		} elseif ($row['formtype'] == "textarea") {
			$row['form'] = "<textarea class='form-control' rows='5' id='value' name='value'>{$row['value']}</textarea>";
		} elseif ($row['formtype'] == "fck") {
			//內容#資料放「content」
			# ======= ckedit====
			if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/ck.php")) {
				redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50", 3, _TAD_NEED_TADTOOLS);
			}
			include_once XOOPS_ROOT_PATH . "/modules/tadtools/ck.php";

			#---- 檢查資料夾
			mk_dir(XOOPS_ROOT_PATH . "/uploads/{$module_name}/fck");
			mk_dir(XOOPS_ROOT_PATH . "/uploads/{$module_name}/image");
			mk_dir(XOOPS_ROOT_PATH . "/uploads/{$module_name}/fck/flash");

			$dir_name = $module_name . "/fck";
			$ck = new CKEditor($dir_name, "value", $row['value'], $module_name);
			$ck->setHeight(300);
			//pinrt_r($ck);die();
			$row['form'] = $ck->render();

		} elseif ($row['formtype'] == "yesno") {
			$value_1 = $row['value'] ? " checked" : "";
			$value_0 = $row['value'] ? "" : " checked";
			$row['form'] = "<input type='radio' name='value' id='value_1' value='1' {$value_1}>\n
    <label for='value_1'>" . _YES . "</label>&nbsp;&nbsp;\n
    <input type='radio' name='value' id='value_0' value='0' {$value_0}>\n
    <label for='value_0'>" . _NO . "</label>";

		} elseif ($row['formtype'] == "mColorPicker") {

			$mColorPicker = "
				<script type='text/javascript' src='" . XOOPS_URL . "/modules/ugm_tools2/jscolor/jscolor.js'></script>\n
			";

			$row['form'] = "{$mColorPicker}<input type='text' class='form-control jscolor' name='value' id='value' value='{$row['value']}'>
			";

		} elseif ($row['formtype'] == "select"){
			$options = "";
			foreach($row['options'] as $k => $v){
				$selected = ($v == $row['value']) ? " selected":"";
				$options .= "<option value='{$v}'{$selected}>{$k}</option>";
			}
			if($options){				
				$row['form'] = "<select name='value' class='form-control'>{$options}</select>";
			}else{
				$row['form'] = "";
			}

		}

		$xoopsTpl->assign('row', $row);
	}
}