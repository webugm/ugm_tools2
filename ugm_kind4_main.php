<?php
defined('XOOPS_ROOT_PATH') || die("XOOPS root path not defined");
/*---- 實體化類別物件  ----*/
#實體化 類別物件
$stopLevel = $foreign[$kind]['stopLevel']; //層數

#(模組名稱,關鍵字，層數)
$ugmKind = new ugmKind($module_name,$kind,$stopLevel);
# 如果資料表非預設 請自行設定
# $ugmKind -> set_tbl($tbl)
/*------------------------*/

switch ($op) {
case "opUpdateSort": //更新排序
  #強制關除錯
  ugm_module_debug_mode(0);
  echo opUpdateSort();
  transaction($module_name,$kind,$stopLevel);
  exit;

case "opSaveDrag": //移動類別儲存
  #強制關除錯
  ugm_module_debug_mode(0);
  echo opSaveDrag();
  transaction($module_name,$kind,$stopLevel);
  exit;
//更新狀態
case "opUpdateEnable":
  #強制關除錯
  ugm_module_debug_mode(0);
  opUpdateEnable();
  transaction($module_name,$kind,$stopLevel);
  redirect_header($_SESSION['return_url'], 3, _BP_SUCCESS);
  exit;

//更新外連狀態
case "opUpdateTarget":
  #強制關除錯
  ugm_module_debug_mode(0);
  opUpdateTarget();
  transaction($module_name,$kind,$stopLevel);
  redirect_header($_SESSION['return_url'], 3, _BP_SUCCESS);
  exit;

//新增資料
case "opInsert":
  opInsert();
  transaction($module_name,$kind,$stopLevel);
  redirect_header($_SESSION['return_url'], 3, _BP_SUCCESS);
  exit;

//編輯資料
case "opUpdate":
  opUpdate($sn);
  transaction($module_name,$kind,$stopLevel);
  redirect_header($_SESSION['return_url'], 3, _BP_SUCCESS);
  exit;

//新增資料
case "opAllInsert":
  opAllInsert();
  transaction($module_name,$kind,$stopLevel);
  redirect_header($_SESSION['return_url'], 3, _BP_SUCCESS);
  exit;

//輸入表格
case "opForm":
  opForm($sn);
  break;

//刪除資料
case "opDelete":
  opDelete($sn);
  transaction($module_name,$kind,$stopLevel);
  redirect_header($_SESSION['return_url'], 3, _BP_DEL_SUCCESS);
  exit;

//預設動作
default:
  # ---- 目前網址 ----
  $_SESSION['return_url'] = getCurrentUrl();
  $op = "opList";
  opList();
  break;
  /*---判斷動作請貼在上方---*/
}
/*-----------秀出結果區--------------*/
$xoTheme->addStylesheet(XOOPS_URL . "/modules/{$module_name}/css/module.css");
$xoopsTpl->assign( "moduleMenu" , $moduleMenu) ;
//$xoopsTpl->assign( "isAdmin" , $isAdmin) ;//interface_menu.php
$xoopsTpl->assign( "op" , $op) ;
$xoopsTpl->assign( "xoops_showlblock" , 0) ;//關閉左區塊(佈景需處理)
$xoopsTpl->assign( "xoops_showrblock" , 0) ;//關閉右區塊(佈景需處理)
include_once XOOPS_ROOT_PATH . '/footer.php';


/*-----------功能函數區--------------*/
###########################################################
#  列出所有類別的資料
###########################################################
function opList() {
  global $xoopsDB, $xoopsModule, $xoopsTpl, $foreign, $ugmKind,$listHead;
  # ----得到foreign key選單 
  $foreignOption = $ugmKind->get_foreignOption($foreign,$ugmKind->get_kind());
  $foreignForm = "
    <div class='row' style='margin-bottom:10px;'>
      <div class='col-sm-3'>
        <select name='kind' id='kind' class='form-control' onchange=\"location.href='?kind='+this.value\">
          $foreignOption
        </select>
      </div>
    </div>
  ";
  $xoopsTpl->assign('foreignForm', $foreignForm);
  $xoopsTpl->assign('kind', $ugmKind->get_kind());

  $kind      = $ugmKind->get_kind();
  $tbl       = $ugmKind->get_tbl();
  $stopLevel = $ugmKind->get_stopLevel();

  # ----列表標題 ----------------------------
  $xoopsTpl->assign('listTitle', $foreign[$kind]['title']);

  # ----得到陣列 ----------------------------
  $rows = $ugmKind->get_listArr();
 
  $listHtml = $ugmKind->get_listHtml($rows,$listHead);
  $xoopsTpl->assign("listHtml", $listHtml);

  #防止偽造表單
  $token = getTokenHTML();
  $xoopsTpl->assign("token", $token);

}

###########################################################
#  編輯表單
###########################################################
function opForm($sn = "") {
  global $xoopsDB,$xoopsTpl,$ugmKind,$foreign,$forms,$formsSwitch,$module_name;

  $kind = $ugmKind->get_kind(); //關鍵字
  $tbl = $ugmKind->get_tbl(); //資料表
  $stopLevel = $ugmKind->get_stopLevel(); //層次
  //$moduleName = $ugmKind->get_moduleName(); //模組名稱
  //----------------------------------*/
  $_GET['ofsn'] = !isset($_GET['ofsn']) ? 0 : intval($_GET['ofsn']);

  //抓取預設值
  if (!empty($sn)) {
    $row = $ugmKind->get_rowBYsn($sn); 
    $pre = _EDIT;
    $row['op'] = "opUpdate";
  } else {
    $row = array();
    $pre = _ADD;
    $row['op'] = "opInsert";
  }

  $row['formTitle'] = $pre . $foreign[$kind]['title'];
  $row['stopLevel'] = $stopLevel;
  //預設值設定
  //設定「sn」欄位預設值
  $row['sn'] = (!isset($row['sn'])) ? "" : $row['sn'];
  //設定「ofsn」欄位預設值
  $row['ofsn'] = (!isset($row['ofsn'])) ? $_GET['ofsn'] : $row['ofsn'];
  if ($stopLevel > 1) {
    # ugm_fix 2019-07-17
    //$row['ofsnOption'] = $ugmKind->get_ofsnOption($row['ofsn']);
    $forms[0]['ofsn']['option'] = "<option value='0'>/</option>\n".$ugmKind->get_ofsnOption($row['ofsn']);
    //$row['ofsn'] = "<option value='0'>/</option>\n".$ugmKind->get_ofsnOption($row['ofsn']);
  }else{
    $row['ofsn'] = 0;
  }
  
  //設定「title」欄位預設值
  $row['title'] = (!isset($row['title'])) ? "" : $row['title'];
  //設定「enable」欄位預設值
  $row['enable'] = (!isset($row['enable'])) ? "1" : $row['enable'];
  //設定「kind」欄位預設值
  $row['kind'] = (!isset($row['kind'])) ? $ugmKind->get_kind() : $row['kind'];
  #----以上為必選

  #外連
  if(isset($formsSwitch['target'])){
    $row['target'] = (!isset($row['target'])) ? "0": $row['target'];
  }
  #網址
  if(isset($formsSwitch['url'])){
    $row['url'] = (!isset($row['url'])) ? "": $row['url'];
  }
  #備註
  if(isset($formsSwitch['ps'])){
    $row['ps'] = (!isset($row['ps'])) ? "": $row['ps'];
  }
  #內容
  if(isset($formsSwitch['content'])){
    $row['content'] = (!isset($row['content'])) ? "": $row['content'];
    $type = "";
    foreach($forms as $r=>$c){
      foreach($c as $col=>$f){
        if($col == "content"){
          $type = $f['type'];
        }
      }
    }

    #fck
    if($type == "fck"){
      #強制關除錯
      ugm_module_debug_mode(0);
      //內容#資料放「content」
      # ======= ckedit====
      if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/ck.php")) {
        redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50", 3, _TAD_NEED_TADTOOLS);
      }
      include_once XOOPS_ROOT_PATH . "/modules/tadtools/ck.php";
      #---- 檢查資料夾
      mk_dir(XOOPS_ROOT_PATH . "/uploads/{$module_name}");
      mk_dir(XOOPS_ROOT_PATH . "/uploads/{$module_name}/fck");
      mk_dir(XOOPS_ROOT_PATH . "/uploads/{$module_name}/fck/image");
      mk_dir(XOOPS_ROOT_PATH . "/uploads/{$module_name}/fck/flash");
      $dir_name = $module_name . "/fck";
      #----
      $ck = new CKEditor($dir_name, "content", $row['content'], $module_name);
      $ck->setHeight(200);
      $row['content'] = $ck->render();
      #-------------------------------------
    }elseif($type == "textarea"){
      $row['content'] = "<textarea class='content form-control' rows='10' id='content' name='content'>{$row['content']}</textarea>";
    }
  }

  #圖片
  if(isset($formsSwitch['single_img'])){
    #----單檔圖片上傳
    $moduleName = $ugmKind->get_moduleName(); //模組名稱
    $subdir = $kind;                          //子目錄
    $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);//實體化 

    $name = "single_img";//欄位名稱
    $col_name = $kind;//資料表關鍵字
    $col_sn = $row['sn'];//商品流水號
    $multiple = false;//單檔 or 多檔上傳
    $accept = "image/*"; //可接受副檔名

    $row['single_img'] = $ugmUpFiles->upform($name,$col_name,$col_sn,$multiple,$accept);
    #-----------------------------------    
  }
  
  $xoopsTpl->assign('row', $row);  

  #防止偽造表單
  $token = getTokenHTML();
  $xoopsTpl->assign("token", $token);
  #-----------------------------------
  $xoopsTpl->assign('forms', $forms); 
    #-----------------------------------    

}
###########################################################
#  編輯表單
###########################################################
function opForm1($sn = "") {
  global $xoopsDB,$xoopsTpl,$ugmKind,$foreign,$forms;
  $xoopsTpl->assign('forms', $forms); 

  $kind = $ugmKind->get_kind(); //關鍵字
  $tbl = $ugmKind->get_tbl(); //資料表
  $stopLevel = $ugmKind->get_stopLevel(); //層次
  //$moduleName = $ugmKind->get_moduleName(); //模組名稱

  //----------------------------------*/
  $_GET['ofsn'] = !isset($_GET['ofsn']) ? 0 : intval($_GET['ofsn']);

  //抓取預設值
  if (!empty($sn)) {
    $row = $ugmKind->get_rowBYsn($sn); 
    $pre = _EDIT;
    $row['op'] = "opUpdate";
  } else {
    $row = array();
    $pre = _ADD;
    $row['op'] = "opInsert";
  }

  $row['formTitle'] = $pre . $foreign[$kind]['title'];
  $row['stopLevel'] = $stopLevel;
  //預設值設定
  //設定「sn」欄位預設值
  $row['sn'] = (!isset($row['sn'])) ? "" : $row['sn'];
  //設定「ofsn」欄位預設值
  $row['ofsn'] = (!isset($row['ofsn'])) ? $_GET['ofsn'] : $row['ofsn'];
  if ($stopLevel > 1) {
    //$row['ofsnOption'] = $ugmKind->get_ofsnOption($row['ofsn']);
    $row['ofsn'] = "<option value='0'>/</option>\n".$ugmKind->get_ofsnOption($row['ofsn']);
  }else{
    $row['ofsn'] = 0;
  }
  //設定「title」欄位預設值
  $row['title'] = (!isset($row['title'])) ? "" : $row['title'];
  //設定「enable」欄位預設值
  $row['enable'] = (!isset($row['enable'])) ? "1" : $row['enable'];
  //設定「kind」欄位預設值
  $row['kind'] = (!isset($row['kind'])) ? $ugmKind->get_kind() : $row['kind'];
  #----以上為必選
  #備註
  if(isset($foreign[$kind]['form']['ps'])){
    $row['ps'] = (!isset($row['ps'])) ? "": $row['ps'];
  }

  #圖片
  if(isset($foreign[$kind]['form']['single_img'])){
    #----單檔圖片上傳
    $moduleName = $ugmKind->get_moduleName(); //模組名稱
    $subdir = $kind;                          //子目錄
    $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);//實體化 

    $name = "single_img";//欄位名稱
    $col_name = $kind;//資料表關鍵字
    $col_sn = $row['sn'];//商品流水號
    $multiple = false;//單檔 or 多檔上傳
    $accept = "image/*"; //可接受副檔名

    $row['single_img'] = $ugmUpFiles->upform($name,$col_name,$col_sn,$multiple,$accept);
    #-----------------------------------    
  }
  
  $xoopsTpl->assign('row', $row);  

  #防止偽造表單
  $token = getTokenHTML();
  $xoopsTpl->assign("token", $token);

}

###########################################################
#  新增資料
###########################################################
function opInsert() {
  global $xoopsDB,$ugmKind,$formsSwitch;
  //---- 過濾資料 -----------------------------------------*/
  $myts = &MyTextSanitizer::getInstance();
  #驗證token
  verifyToken($_POST['token']);

  $kind = $ugmKind->get_kind();           //關鍵字
  $tbl = $ugmKind->get_tbl();             //資料表
  $stopLevel = $ugmKind->get_stopLevel(); //層次

  $_POST['sn'] = intval($_POST['sn']);//sn
  $_POST['ofsn'] = intval($_POST['ofsn']);//ofsn
  $_POST['title'] = $myts->addSlashes($_POST['title']);//類別名稱
  $_POST['enable'] = intval($_POST['enable']);//狀態
  $_POST['kind'] = $myts->addSlashes($_POST['kind']);//分類

  #----取得記錄之父類別，最大排序
  $_POST['sort'] = $ugmKind->get_rowMaxSort($_POST['ofsn']);


  $col=["`ofsn`","`title`","`enable`","`kind`","`sort`"];
  $values=["'{$_POST['ofsn']}'","'{$_POST['title']}'","'{$_POST['enable']}'","'{$_POST['kind']}'","'{$_POST['sort']}'"];
 
  #網址
  if(isset($formsSwitch['url'])){
    $_POST['url'] = $myts->addSlashes($_POST['url']);//
    $col[]="`url`";
    $values[]="'{$_POST['url']}'";
  }

  #外連
  if(isset($formsSwitch['target'])){
    $_POST['target'] = intval($_POST['target']);//外連
    $col[]="`target`";
    $values[]="'{$_POST['target']}'";
  }
 
  #內容
  if(isset($formsSwitch['content'])){
    $_POST['content'] = $myts->addSlashes($_POST['content']);
    $col[]="`content`";
    $values[]="'{$_POST['content']}'";
  }

  #備註
  if(isset($formsSwitch['ps'])){
    $_POST['ps'] = $myts->addSlashes($_POST['ps']);
    $col[]="`ps`";
    $values[]="'{$_POST['ps']}'";
  }

  $col = implode(",",$col);
  $values = implode(",",$values);

  #---------寫入-------------------------
  $sql = "insert into `{$tbl}`
          ({$col})
          values
          ({$values})";//die($sql);
  $result = $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL, 3, web_error());
  //取得最後新增資料的流水編號
  $sn = $xoopsDB->getInsertId();

  if(isset($formsSwitch['single_img'])){
    #----單圖上傳  
    $moduleName = $ugmKind->get_moduleName();                 //專案名稱
    $subdir = $kind; //子目錄
    $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);
    
    $col_name = $kind;                                        //資料表關鍵字
    $col_sn = $sn;                                            //商品流水號
    $name = "single_img";                                     //欄位名稱
    $multiple = false;                                        //單檔 or 多檔上傳
    $main_width = $formsSwitch['main_width'];   //大圖壓縮尺吋，-1則不壓縮
    $thumb_width = $formsSwitch['thumb_width']; //小圖壓縮尺吋

    $ugmUpFiles->upload_file($name,$col_name,$col_sn,$multiple,$main_width,$thumb_width);
    #------------------------------------------------
  } 

  return "新增資料->{$_POST['title']} 成功！";
} 

###########################################################
#  更新資料
###########################################################
function opUpdate($sn = "") {
  global $xoopsDB,$ugmKind,$formsSwitch; 

  if (!$sn) {
    redirect_header($_SESSION['return_url'], 3, "更新資料錯誤！！");
  }
  //---- 過濾資料 -----------------------------------------*/
  $myts = &MyTextSanitizer::getInstance();
  #驗證token
  verifyToken($_POST['token']);

  $kind = $ugmKind->get_kind();           //關鍵字
  $tbl = $ugmKind->get_tbl();             //資料表
  $stopLevel = $ugmKind->get_stopLevel(); //層次

  #檢查類別層次
  $thisLevel = $ugmKind->get_thisLevel($sn);//自己層數
  $downLevel = $ugmKind->get_downLevel($sn);//自己底下有幾層
  $ofsnLevel = $ugmKind->get_thisLevel($_POST['ofsn']);//自己上面有幾層

  $_POST['sn'] = intval($_POST['sn']);//sn
  $_POST['ofsn'] = intval($_POST['ofsn']);//ofsn
  $_POST['title'] = $myts->addSlashes($_POST['title']);//類別名稱
  $_POST['enable'] = intval($_POST['enable']);//狀態
  $_POST['kind'] = $myts->addSlashes($_POST['kind']);//分類

  #防呆
  
  #檢查類別層次
  $thisLevel = $ugmKind->get_thisLevel($sn);//自己層數
  $downLevel = $ugmKind->get_downLevel($sn);//自己底下有幾層
  $ofsnLevel = $ugmKind->get_thisLevel($_POST['ofsn']);//自己上面有幾層
  if($_POST['ofsn'] == $_POST['sn'])redirect_header($_SESSION['return_url'], 3000, "不能設定自己為父類別");
  #父層 + 底層 >= 層
  if($ofsnLevel + $downLevel >= $stopLevel)redirect_header($_SESSION['return_url'], 3000, "子類別太多，請先將子類別移動，再更新！");

  $updates = ["`ofsn`='{$_POST['ofsn']}'","`title`='{$_POST['title']}'","`enable`='{$_POST['enable']}'","`kind`   ='{$_POST['kind']}'"];

 
  if(isset($formsSwitch['url'])){
    $_POST['url'] = $myts->addSlashes($_POST['url']);//網址
    $updates[]="`url`='{$_POST['url']}'";
  }

  if(isset($formsSwitch['target'])){
    $_POST['target'] = intval($_POST['target']);//外連
    $updates[]="`target`='{$_POST['target']}'";
  }
 
  if(isset($formsSwitch['content'])){
    $_POST['content'] = $myts->addSlashes($_POST['content']);//外連
    $updates[]="`content`='{$_POST['content']}'";
  }
 
  if(isset($formsSwitch['ps'])){
    $_POST['ps'] = $myts->addSlashes($_POST['ps']);//外連
    $updates[]="`ps`='{$_POST['ps']}'";
  }

  $updates = implode(",",$updates);
  #---------寫入-------------------------  
  $sql = "update `{$tbl}` set
          {$updates}
          where sn='{$_POST['sn']}'";//die($sql);
  $result = $xoopsDB->queryF($sql) or redirect_header(XOOPS_URL, 3, web_error());

  if(isset($formsSwitch['single_img'])){
    #----單圖上傳  
    $moduleName = $ugmKind->get_moduleName();             //專案名稱
    $subdir = $kind; //子目錄
    $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);
    
    $col_name = $kind;                                              //資料表關鍵字
    $col_sn = $sn;                                                  //商品流水號
    $name = "single_img";                                           //欄位名稱
    $multiple = false;                                              //單檔 or 多檔上傳
    $main_width = $formsSwitch['main_width'];                       //大圖壓縮尺吋，-1則不壓縮
    $thumb_width = $formsSwitch['thumb_width'];                     //小圖壓縮尺吋
    $ugmUpFiles->upload_file($name,$col_name,$col_sn,$multiple,$main_width,$thumb_width);
    #------------------------------------------------
  } 
  return "編輯資料->{$_POST['title']} 成功！";
}

###########################################################
#  自動更新排序
###########################################################
function opUpdateSort() {
  global $xoopsDB, $ugmKind; 
  $sort = 1;
  foreach ($_POST['tr'] as $sn) {
    if (!$sn) {
      continue;
    }
    $sql = "update " . $ugmKind->get_tbl() . " set `sort`='{$sort}' where `sn`='{$sn}'";
    $xoopsDB->queryF($sql) or die($sql);
    $sort++;
  }
  return "排序成功";
}

###########################################################
#  移動類別儲存
###########################################################
function opSaveDrag() {
  global $xoopsDB, $ugmKind;
  $_POST['ofsn'] = intval($_POST['ofsn']); //目的
  $_POST['sn'] = intval($_POST['sn']);     //來源

  #檢查類別層次
  $stopLevel = $ugmKind->get_stopLevel(); //層次
  $thisLevel = $ugmKind->get_thisLevel($_POST['sn']);//來源層數
  $downLevel = $ugmKind->get_downLevel($_POST['sn']);//來源底下有幾層
  $ofsnLevel = $ugmKind->get_thisLevel($_POST['ofsn']);//目的的層數
  
  if (!$_POST['sn']) {
    #根目錄不能移動！！
    die(_MD_UGMMODULE_ROOT_ERROR);
  } elseif ($_POST['ofsn'] == $_POST['sn']) {
    #移動錯誤，不能自己移入自己！！
    die(_MD_TREETABLE_MOVE_ERROR1);
  } elseif ($downLevel + $ofsnLevel >= $stopLevel) {
    #來源底層數+目的的階層 >= 層數，請先調整來源的底層！
    die(_MD_UGMMODULE_LEVEL_ERROR);
  }

  $sql = "update " .$ugmKind->get_tbl() . "
        set `ofsn`='{$_POST['ofsn'] }' where `sn`='{$_POST['sn']}'";
  $xoopsDB->queryF($sql) or die($sql);

  return _MD_TREETABLE_MOVE_SUCCESS;//移動成功！！
}

###########################################################
#  更新啟用
###########################################################
function opUpdateEnable() {
  global $xoopsDB, $ugmKind; 
  #權限
  /***************************** 過瀘資料 *************************/
  $enable = intval($_GET['enable']);
  $sn = intval($_GET['sn']);
  /****************************************************************/
  //更新
  $sql = "update " . $ugmKind->get_tbl() . " set  `enable` = '{$enable}' where `sn`='{$sn}'";//die($sql);
  $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
  return;
}

###########################################################
#  更新外連
###########################################################
function opUpdateTarget() {
  global $xoopsDB, $ugmKind; 
  #權限
  /***************************** 過瀘資料 *************************/
  $target = intval($_GET['target']);
  $sn = intval($_GET['sn']);
  /****************************************************************/
  //更新
  $sql = "update " . $ugmKind->get_tbl() . " set  `target` = '{$target}' where `sn`='{$sn}'";
  $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
  return;
}

###########################################################
#  批次編輯資料
###########################################################
function opAllInsert() {
  global $xoopsDB, $ugmKind,$listHead;  
  #驗證token
  verifyToken($_POST['token']);
  //---- 過濾資料 -----------------------------------------*/
  $myts = &MyTextSanitizer::getInstance();

  foreach ($_POST['title'] as $sn => $title) {
    $title = $myts->addSlashes($title);
    $sn = intval($sn); #編輯
    if($listHead['url']){
      $url = $myts->addSlashes($_POST['url'][$sn]);
      $sql = "update " . $ugmKind->get_tbl() . " set
             `title` = '{$title}',
             `url` = '{$url}'
             where sn='{$sn}'";//die($sql);
    }else{
      $sql = "update " . $ugmKind->get_tbl() . " set
             `title` = '{$title}'
             where sn='{$sn}'";//die($sql);
    }
    $xoopsDB->queryF($sql) or web_error($sql);
  }
}

###########################################################
#  刪除資料
###########################################################
function opDelete($sn = "") {
  global $xoopsDB, $ugmKind,$foreign;
  if (empty($sn)) {
    redirect_header($_SERVER['PHP_SELF'], 3, _BP_DEL_ERROR);
  }

  #刪除資料 額外檢查
  opDeleteCheck($sn);

 // echo $ugmKind->get_downLevel($sn);die();

  #檢查是否有子類別
  if ($ugmKind->get_downLevel($sn)) {
    redirect_header($_SERVER['PHP_SELF'], 3, _MD_UGMMODULE_HAVE_SUB_NOT_DEL);
  }

  $sql = "delete from " . $ugmKind->get_tbl() . "
          where sn='{$sn}'"; //die($sql);
  $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
  $kind = $ugmKind->get_kind();
  if($foreign[$kind]['form']['single_img']){    
    #----單檔圖片上傳
    $moduleName = $ugmKind->get_moduleName(); //模組名稱
    $subdir = $kind;                          //子目錄
    $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);//實體化 
    $ugmUpFiles -> set_col($kind,$sn);
    $ugmUpFiles -> del_files();
  }
  return ture;
}