<?php
/*-----------引入檔案區--------------*/
$isAdmin = true;
$xoopsOption['template_main'] = '模組目錄_adm_kind.tpl';
require_once "header.php";
require_once "../function.php";
#引入類別物件---------------------------------
require_once XOOPS_ROOT_PATH . "/modules/ugm_tools2/ugmKind3.php";
#引入上傳物件
require_once XOOPS_ROOT_PATH . "/modules/ugm_tools2/ugmUpFiles3.php";
/*-----------執行動作判斷區----------*/
#system_CleanVars (&$global, $key, $default= '', $type= 'int')
require_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op = system_CleanVars($_REQUEST, 'op', '', 'string');
$sn = system_CleanVars($_REQUEST, 'sn', '', 'int');
$kind = system_CleanVars($_REQUEST, 'kind', 'kind_prod', 'string'); 

#foreign key
$foreign = array(
  "kind_prod" => array("title" => "商品類別", "stopLevel" => 2)
);

switch ($kind) {
  //預設動作
  default:         //商品類別
  	$kind = "kind_prod";
  	/*---- 定義 Form ----*/
  	#標題		
  	$col = "title";
  	$foreign[$kind]['form'][$col] = true;
  	$forms[0][$col]['label'] = "<span>*</span>商品類別標題";
  	$forms[0][$col]['type'] = "text";
  	$forms[0][$col]['width'] = 6;
  	#啟用		
  	$col = "enable";
  	$foreign[$kind]['form'][$col] = true;
  	$forms[0][$col]['label'] = "啟用狀態";
  	$forms[0][$col]['type'] = "radio";
  	$forms[0][$col]['width'] = 2;
  	
    #父類別   
    $col = "ofsn";
    $foreign[$kind]['form'][$col] = true;
    $forms[0][$col]['label'] = "父類別";
    $forms[0][$col]['type'] = $foreign[$kind]['stopLevel'] > 1 ?"select":"hidden";
    $forms[0][$col]['width'] = 2;

  	#圖示		
  	$col = "ps";
  	$foreign[$kind]['form'][$col] = true;
  	$forms[1][$col]['label'] = "圖示";
  	$forms[1][$col]['type'] = "icon";
  	$forms[1][$col]['width'] = 3;

    #圖片   
    $col = "single_img";
    $foreign[$kind]['form'][$col] = true;
    $foreign[$kind]['form'][$col]['main_width'] = 768;
    $foreign[$kind]['form'][$col]['thumb_width'] = 120;

    $forms[1][$col]['label'] = "圖片<span>(768x400)</span>";
    $forms[1][$col]['type'] = "single_img";
    $forms[1][$col]['width'] = 3;


  	/*---- 定義 List ----*/
    $col = "title";
  	$listHead[$col]['th']['title']="商品類別標題";
  	$listHead[$col]['th']['attr']=" class='col-sm-5 text-center'";
  	$listHead[$col]['td']['attr']=" class='text-left'";

    // $col = "url";
    // $listHead[$col]['th']['title']="網址";
    // $listHead[$col]['th']['attr']=" class='col-sm-3 text-center'";
    // $listHead[$col]['td']['attr']=" class='text-left'";

    $col = "single_img";
    $listHead[$col]['th']['title']="圖片";
    $listHead[$col]['th']['attr']=" class='col-sm-1 text-center'";
    $listHead[$col]['td']['attr']=" class='text-center'";
    $listHead[$col]['td']['imgWidth']=50;//縮圖尺吋

    $col = "ps";
    $listHead[$col]['th']['title']="圖示";
    $listHead[$col]['th']['attr']=" class='text-center' style='width:2%;'";
    $listHead[$col]['td']['attr']=" class='text-center'";

    // $col = "target";
    // $listHead[$col]['th']['title']="外連";
    // $listHead[$col]['th']['attr']=" class='text-center' style='width:2%;'";
    // $listHead[$col]['td']['attr']=" class='text-center'";

    $col = "enable";
    $listHead[$col]['th']['title']="啟用";
    $listHead[$col]['th']['attr']=" class='text-center' style='width:2%;'";
    $listHead[$col]['td']['attr']=" class='text-center'";

    $col = "function";
    $listHead[$col]['th']['title']="功能";
    $listHead[$col]['th']['attr']=" class='text-center' style='width:12%;'";

    $listHead[$col]['td']['attr']=" class='text-center'";
    $listHead[$col]['td']['btn'][]="view";//瀏覽
    $listHead[$col]['td']['btn'][]="edit";//編輯
    $listHead[$col]['td']['btn'][]="del";//刪除
  break;
}
#-------------------------------------------

#引入類別物件---------------------------------
require_once XOOPS_ROOT_PATH . "/modules/ugm_tools2/ugm_kind3.php";


###########################################################
#  異動後，要執行的動作
###########################################################
function transaction(){
  global $xoopsDB, $ugmKind,$op;
  #---- 過濾讀出的變數值 ----
  $myts = MyTextSanitizer::getInstance();

  $kind = $ugmKind->get_kind();
  $moduleName =$ugmKind->get_moduleName();

  if($kind == "area" or $kind == "mood" or $kind == "kind" ){
    # ----得到陣列 ----------------------------
    $rows = $ugmKind->get_listArr();
    $content=array();
    foreach ($rows as $row){
      $content[] = $myts->htmlSpecialChars($row['title']);
    }
    $contents = json_encode($content, JSON_UNESCAPED_UNICODE);    
    #---- 檢查資料夾
    mk_dir(XOOPS_ROOT_PATH . "/uploads/{$moduleName}/{$kind}");
    $file = XOOPS_ROOT_PATH . "/uploads/{$moduleName}/{$kind}/{$kind}.json";
    $f = fopen($file, 'w'); //以寫入方式開啟文件
    fwrite($f, $contents); //將新的資料寫入到原始的文件中
    fclose($f);
  }

  if($kind == "link"){
    # ----得到陣列 ----------------------------
    $rows = $ugmKind->get_listArr();
    $content=array();
    
    #----單檔圖片上傳
    //$moduleName = $DIRNAME;                             //模組名稱(ugm_tools2)前面已處理
    $subdir = "link";                                      //子目錄(前後不要有 / )
    $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);   //實體化
    $col_name = "link";    
    $thumb = false ;                                      //顯示縮圖 

    foreach ($rows as $row){

      $col_sn = $row['sn'];                                 //關鍵字流水號
      $tmp['img'] = $ugmUpFiles->get_rowPicSingleUrl($col_name,$col_sn,$thumb);
      $tmp['url'] = $myts->htmlSpecialChars($row['url']);
      $content[] = $tmp;
    }
    $contents = json_encode($content, JSON_UNESCAPED_UNICODE);    
    #---- 檢查資料夾
    mk_dir(XOOPS_ROOT_PATH . "/uploads/{$moduleName}/{$kind}");
    $file = XOOPS_ROOT_PATH . "/uploads/{$moduleName}/{$kind}/{$kind}.json";
    $f = fopen($file, 'w'); //以寫入方式開啟文件
    fwrite($f, $contents); //將新的資料寫入到原始的文件中
    fclose($f);

  }


}


###########################################################
#  刪除資料 額外檢查
###########################################################
function opDeleteCheck($sn = "") {
  global $xoopsDB, $ugmKind;

  // #檢查商品檔，是否有使用類別
  // $sql = "select sn
  //         from " . $xoopsDB->prefix("ugm_rg_prod") . "
  //         where kind = '{$sn}'"; //die($sql);
  // $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
  // if ($xoopsDB->getRowsNum($result)) {
  //   redirect_header($_SESSION['returnUrl'], 3, "已有商品檔使用，無法刪除");
  // }

  // #檢查新聞檔，是否有使用類別
  // $sql = "select sn
  //         from " . $xoopsDB->prefix("ugm_rg_news") . "
  //         where kind = '{$sn}'"; //die($sql);
  // $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
  // if ($xoopsDB->getRowsNum($result)) {
  //   redirect_header($_SESSION['returnUrl'], 3, "已有新聞檔使用，無法刪除");
  // }

  // #檢查自訂頁面，是否有使用類別
  // $sql = "select sn
  //         from " . $xoopsDB->prefix("ugm_rg_page") . "
  //         where kind = '{$sn}'"; //die($sql);
  // $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
  // if ($xoopsDB->getRowsNum($result)) {
  //   redirect_header($_SESSION['returnUrl'], 3, "已有自訂頁面使用，無法刪除");
  // }

}
