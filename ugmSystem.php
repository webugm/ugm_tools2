<?php
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

class ugmSystem
{
  private $tbl;            //資料表(已含前置字元)
  function __construct($tbl) {
    $this->set_tbl($tbl);
  }
  #--------- 設定類 --------------------
  #設定資料表
  public function set_tbl($value) {
    global $xoopsDB;
    $this->tbl = $xoopsDB->prefix($value);
  }
  #---- 取得類 --------------------------

  #取得變數值 by name
  public function get_valueBYname($name) {
    global $xoopsDB;
    $sql = "select `value`
      from " . $this->tbl . "
      where `name`='{$name}'"; //die($sql);
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
    list($value) = $xoopsDB->fetchRow($result);
    return $value;
  }

  #取得變數值 by sn
  public function get_valueBYsn($sn) {
    global $xoopsDB;
    $sql = "select `value`
      from " . $this->tbl . "
      where `sn`='{$sn}'"; //die($sql);
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
    list($value) = $xoopsDB->fetchRow($result);
    return $value;
  }
}