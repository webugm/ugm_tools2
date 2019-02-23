<?php
//include_once XOOPS_ROOT_PATH . "/modules/ugm_tools/ugmQrcode.php";
$qrCacheDir = XOOPS_ROOT_PATH . "/uploads/{$module_name}/qrcache/";
$qrLogDir = XOOPS_ROOT_PATH . "/uploads/{$module_name}/qrlog/";
#檢查是否有目錄，沒有則建立

if (!file_exists($qrCacheDir)) {
	mkdir($qrCacheDir); 
  $file = $qrCacheDir ."index.html";
  $f = fopen($file, 'w'); //以寫入方式開啟文件
  fwrite($f, " <script>history.go(-1);</script>"); //將新的資料寫入到原始的文件中
  fclose($f);
}

if (!file_exists($qrLogDir)) {
	mkdir($qrLogDir); 
  $file = $qrLogDir ."index.html";
  $f = fopen($file, 'w'); //以寫入方式開啟文件
  fwrite($f, " <script>history.go(-1);</script>"); //將新的資料寫入到原始的文件中
  fclose($f);
}

$QR_BASEDIR = XOOPS_ROOT_PATH . "/modules/ugm_tools2/class/phpqrcode/";

include $QR_BASEDIR . "qrconst.php";
include $QR_BASEDIR . "qrconfig.php";
include $QR_BASEDIR . "qrtools.php";
include $QR_BASEDIR . "qrspec.php";
include $QR_BASEDIR . "qrimage.php";
include $QR_BASEDIR . "qrinput.php";
include $QR_BASEDIR . "qrbitstream.php";
include $QR_BASEDIR . "qrsplit.php";
include $QR_BASEDIR . "qrrscode.php";
include $QR_BASEDIR . "qrmask.php";
include $QR_BASEDIR . "qrencode.php";

function addQrcode($contnet="" ,$filename="", $errorCorrectionLevel="L", $matrixPointSize=4){
	QRcode::png($contnet, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
}
