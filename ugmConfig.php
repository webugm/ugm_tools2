<?php
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

class ugmConfig
{  
  private $tbl ;           //資料表(已含前置字元)

  function __construct() {
    $this->set_tbl();
  }
  #--------- 設定類 --------------------
  #設定資料表
  public function set_tbl() {
    global $xoopsDB;
    $this->tbl = $xoopsDB->prefix("config");
  }
  #---- 取得類 --------------------------
  //取得變數值 by name
  public function get_valueBYname($conf_name) {
    global $xoopsDB;
    $sql = "select `conf_value`
      from " . $this->tbl . "
      where `conf_name`='{$conf_name}'"; //die($sql);
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
    list($value) = $xoopsDB->fetchRow($result);
    return $value;
  }

  //取得變數值 by sn
  public function get_valueBYsn($conf_id) {
    global $xoopsDB;
    $sql = "select `conf_value`
      from " . $this->tbl . "
      where `conf_id`='{$conf_id}'"; //die($sql);
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
    list($value) = $xoopsDB->fetchRow($result);
    return $value;
  }
  #---- 更新類
  //取得變數值 by name
  public function update_valueBYname($value,$name) {
    global $xoopsDB;
    #更新系統變數值
    $sql = "update " . $this->tbl . "
            set
            `conf_value`='{$value}'
            where `conf_name`='{$name}'"; //die($sql);
    $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, $sql);
  }
}