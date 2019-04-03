<?php
/* 
  $Arr =>
  $options=array(
    'preselectFirst' => '自行車',
    'preselectSecond' => 18,
    'emptyOption'=> false,
    'emptyValue'=> '...',
    'emptyKey'=> ''
  );
  $array = array(        
    '獨木舟'=> array(
      'key' => '獨木舟',
      'values' => array(
        '全日'=> 13,
        '上午'=> 14,
        '下午'=> 15
      )
    ),
    '自行車'=> array(
      'key' => '自行車',
      'values' => array(
        '全日'=> 16,
        '上午'=> 17,
        '下午'=> 18
      )
    ),
    '沙灘車'=> array(
      'key' => '沙灘車',
      'values' => array(
        '全日'=> 19,
        '上午'=> 20,
        '下午'=> 21
      )
    )
  );
  $time = new doubleSelect("","",$array);
  $timeOption = $time->render($options,"first","second2");

  //---------------
  
 */
include_once "ugm_tools2_header.php";

class doubleSelect
{
    public $Arr;

    //建構函數
    public function __construct($firstArr="",$secondArr="",$Arr="",$type="sn"){
      if($Arr){
        $this->Arr            = $Arr;
      }else{
        $this->Arr            = $this->setArr($firstArr,$secondArr,$type);
      }
    }
    public function setArr($firstArr,$secondArr,$type){
      $Arr = [];
      foreach($firstArr as $first){
        $values = [];
        foreach($secondArr[$first['sn']] as $row){
          if($type == "title"){                   
            $values[$row['title']] = $row['title'];
          }else{               
            $values[$row['title']] = $row['sn'];
          }
        }
        if($type == "title"){     
          $Arr[$first['title']]['key'] = $first['title'];    
        }else{                         
          $Arr[$first['title']]['key'] = $first['sn'];
        }    
        $Arr[$first['title']]['values'] = $values;
      }
      return $Arr;
    }
    //產生
    public function render($firstId,$secondId,$options="")
    {
      global $xoTheme;
      $jquery = get_jquery();
      if($xoTheme) {
        $render="";
        //$xoTheme->addStylesheet("");
        $xoTheme->addScript("modules/ugm_tools2/class/jquery.doubleSelect/jquery.doubleSelect.js");
      }else {        
        $render = "
        {$jquery}
        <script type='text/javascript' src='".UGM_TOOLS2_URL."/ugm_tools2/class/jquery.doubleSelect/jquery.doubleSelect.js'></script>
        ";
      }
      if(!$options){ 
        $options=array(
          'preselectFirst' => null,
          'preselectSecond' => null,
          'emptyOption'=> false,
          'emptyValue'=> -1,
          'emptyKey'=> 'Choose ...');
      }
      
      $options = json_encode($options, JSON_UNESCAPED_UNICODE);
      $array = json_encode($this->Arr, JSON_UNESCAPED_UNICODE);
      $render .="
        var options = {$options};
        var selectoptions = {$array};
        $('#{$firstId}').doubleSelect('{$secondId}', selectoptions,options);
      
      ";

      return $render;
    }
}
