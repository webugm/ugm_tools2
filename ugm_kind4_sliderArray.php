<?php
defined('XOOPS_ROOT_PATH') || die("XOOPS root path not defined");
/*---- 定義 Form ----*/
#標題   
$col = "title";
$formsSwitch[$col] = true;//請一定設為true
$forms[0][$col]['label'] = "<span>*</span>"._TITLE;
$forms[0][$col]['type'] = "text";
$forms[0][$col]['width'] = 6;

#啟用   
$col = "enable";
$formsSwitch[$col] = true;
$forms[0][$col]['label'] = "啟用狀態";
$forms[0][$col]['type'] = "radio";
$forms[0][$col]['width'] = 2;

// #外連   
// $col = "target";
// $formsSwitch[$col] = true;
// $forms[0][$col]['label'] = "外連狀態";
// $forms[0][$col]['type'] = "radio";
// $forms[0][$col]['width'] = 2;

// #父類別   
// $col = "ofsn";
// $formsSwitch[$col] = true;
// $forms[0][$col]['label'] = "父類別";
// $forms[0][$col]['type'] = $foreign[$kind]['stopLevel'] > 1 ?"select":"hidden";
// $forms[0][$col]['width'] = 2;

// #網址   
// $col = "url";
// $formsSwitch[$col] = true;
// $forms[1][$col]['label'] = "網址";
// $forms[1][$col]['type'] = "text";
// $forms[1][$col]['width'] = 6;

// #圖示   
// $col = "ps";
// $formsSwitch[$col] = true;
// $forms[1][$col]['label'] = "圖示";
// $forms[1][$col]['type'] = "icon";
// $forms[1][$col]['width'] = 3;

#副標題   
$col = "content";
$formsSwitch[$col] = true;
$forms[1][$col]['label'] = "副標題";
$forms[1][$col]['type'] = "text";
$forms[1][$col]['width'] = 6;

#圖片   
$col = "single_img";
$formsSwitch[$col] = true;
$formsSwitch[$col]['main_width'] = 1140;
$formsSwitch[$col]['thumb_width'] = 120;

$forms[1][$col]['label'] = "圖片<span>(1140x350)</span>";
$forms[1][$col]['type'] = "single_img";
$forms[1][$col]['width'] = 3;



/*---- 定義 List ----*/
$col = "title";
$listHead[$col]['th']['title']=_TITLE;
$listHead[$col]['th']['attr']=" class=' text-center'";
$listHead[$col]['td']['attr']=" class='text-left'";

// $col = "url";
// $listHead[$col]['th']['title']="網址";
// $listHead[$col]['th']['attr']=" class='col-sm-3 text-center'";
// $listHead[$col]['td']['attr']=" class='text-left'";

$col = "single_img";
$listHead[$col]['th']['title']="圖片";
$listHead[$col]['th']['attr']=" class='text-center' style='width:150px;'";
$listHead[$col]['td']['attr']=" class='text-center'";
$listHead[$col]['td']['imgWidth']=50;//縮圖尺吋

// $col = "ps";
// $listHead[$col]['th']['title']="圖示";
// $listHead[$col]['th']['attr']=" class='text-center' style='width:2%;'";
// $listHead[$col]['td']['attr']=" class='text-center'";

// $col = "target";
// $listHead[$col]['th']['title']="外連";
// $listHead[$col]['th']['attr']=" class='text-center' style='width:2%;'";
// $listHead[$col]['td']['attr']=" class='text-center'";

$col = "enable";
$listHead[$col]['th']['title']="啟用";
$listHead[$col]['th']['attr']=" class='text-center' style='width:50px;'";
$listHead[$col]['td']['attr']=" class='text-center'";


$col = "function";
$listHead[$col]['th']['title']="功能";
$listHead[$col]['th']['attr']=" class='text-center' style='width:130px;'";

$listHead[$col]['td']['attr']=" class='text-center'";
$listHead[$col]['td']['btn'][]="view";//瀏覽
$listHead[$col]['td']['btn'][]="edit";//編輯
$listHead[$col]['td']['btn'][]="del";//刪除
#-------------------------------------------