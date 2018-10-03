<?php
defined('XOOPS_ROOT_PATH') || die("XOOPS root path not defined");
class ugmKind {
	public $moduleName;             //模組名稱
	public $tbl;                    //資料表(已含前置字元)
	public $kind;                   //類別
	public $stopLevel = 1;          //層數

	function __construct($moduleName, $kind, $stopLevel) {
		$this->set_moduleName($moduleName);
		$this->set_tbl($moduleName."_kind");
		$this->set_kind($kind);
		$this->set_stopLevel($stopLevel);
	}
	#--------- 設定類 --------------------
	#設定模組名稱
	public function set_moduleName($value) {
		$this->moduleName = $value;
	}
	#設定資料表
	public function set_tbl($value) {
		global $xoopsDB;
		$this->tbl = $xoopsDB->prefix($value);

	}
	#設定類別
	public function set_kind($value = "") {
		$this->kind = $value;
	}
	#設定層數
	public function set_stopLevel($value = 1) {
		$this->stopLevel = $value;
	}
	//--------- 取得類 ------------*/
	#取得模組名稱
	public function get_moduleName() {
		return $this->moduleName;
	}
	#取得資料表
	public function get_tbl() {
		return $this->tbl;
	}
	#取得分類
	public function get_kind() {
		return $this->kind;
	}
	#取得層數
	public function get_stopLevel() {
		return $this->stopLevel;
	}

	################################################################
	#  自己底下有幾層
	################################################################
	#get_downLevel
	public function get_downLevel($sn,$downLevel=0) {
		global $xoopsDB;

		if ($downLevel > $this->stopLevel) {
			return $downLevel;
		}

		$level = $downLevel+1;
		$sql = "select sn
            from `{$this->tbl}`
            where ofsn='{$sn}'"; // return $sql;	
		$result = $xoopsDB->query($sql) or redirect_header(XOOPS_URL, 3, web_error());

		while ($row = $xoopsDB->fetchArray($result)) {
			$downLevel_tmp = $this->get_downLevel($row['sn'], $level);
			$downLevel = ($downLevel_tmp > $downLevel) ? $downLevel_tmp : $downLevel;
		}
		return $downLevel;
	}

	###########################################################
	#  用流水號 得到自己的層數
	###########################################################
	public function get_thisLevel($sn, $level = 1) {
		global $xoopsDB;

		if($sn=="0" and $level == "1")return "0";
		if ($level > $this->stopLevel)return $level;

		$sql = "select ofsn
            from `{$this->tbl}`
            where sn='{$sn}'"; // die($sql);

	  $result = $xoopsDB->query($sql)  or redirect_header(XOOPS_URL, 3, web_error());	          
	  list($ofsn) = $xoopsDB->fetchRow($result); 

		if (!$ofsn) {
			return $level;
		}
		return $this->get_thisLevel($ofsn, ++$level);
	}

	################################################################
	#  取得外鍵下拉選單 的 選項
	#  傳入：($foreign, $default)
	#  回傳：foreignOption
	################################################################
	public function get_foreignOption($foreign, $default="") {
	  # ----得到Foreign key選單 ----------------------------
	  $foreignOption = "";
	  foreach ($foreign as $key => $value) {
	    $selected = "";
	    if ($default == $key) {
	      $selected = " selected";
	    }
	    $foreignOption .= "<option value='{$key}'{$selected}>{$value['title']}</option>";
	  }
	  return $foreignOption;
	}

	################################################################
	#  取得類別記錄
	#  $enable=0 全部
	#  $enable=1 啟用
	################################################################
	public function get_listArr($ofsn=0,$level=1,$enable=0) {
		global $xoopsDB;

		#---- 過濾讀出的變數值 ----
		$myts = MyTextSanitizer::getInstance();
		
		$andKey = $enable ? " and `enable`='{$enable}'":"";

		#檢查目前階層是否大於層次
		if ($level > $this->stopLevel) {
			return;
		}

		#設定下層
		$downLevel = $level + 1;

		$sql = "select * from `{$this->tbl}`
            where `ofsn`='{$ofsn}' and `kind`='{$this->kind}'{$andKey} 
            order by sort"; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header(XOOPS_URL, 3, web_error());

		#--------------------------------------------------------------------
		$rows = [];
		while ($row =  $xoopsDB->fetchArray($result)) {
			//以下會產生這些變數： sn	ofsn	kind	title	sort	enable	url	target	col_sn	content	ps
			$row['sn'] = intval($row['sn']);
			$row['ofsn'] = intval($row['ofsn']);
			$row['kind'] = $myts->htmlSpecialChars($row['kind']); 
			$row['title'] = $myts->htmlSpecialChars($row['title']);
			$row['sort'] = intval($row['sort']);
			$row['enable'] = intval($row['enable']); 
			$row['url'] = $myts->htmlSpecialChars($row['url']);
			$row['target'] = intval($row['target']);
			$row['col_sn'] = intval($row['col_sn']);		
			$row['ps'] = !is_null($row['ps']) ? $myts->htmlSpecialChars($row['ps']):"";
			/*------------------------------------*/
			$row['level'] = $level;
			#取得底下有幾層
			$row['downLevel'] = $this->get_downLevel($row['sn']);
			#子類別記錄			
			$row['sub'] = $this->get_listArr($row['sn'], $downLevel,$enable);

			//資料夾圖示(最後一層沒有)
			$icon['folder_i'] = $this->stopLevel > $level ? true : false;
			//移動圖示
			$icon['move_i'] = $icon['folder_i'] ? false:true;
			//增加類別圖示
			$icon['add_downLevel_i'] = $this->stopLevel > $level ? true : false;
			//排序圖示
			$icon['sort_i'] = true;

			$row['icon'] = $icon;

			$rows[] = $row;
		}
		return $rows;
	}

	################################################################
	#  
	################################################################
	public function get_rowsHtml($rows,$lestHead) {
		$html = "";	
		foreach ($rows as $item => $row) {
			$html .= $this->get_rowHtml($row,$lestHead);
			if ($row['sub']) {
				$html .= $this->get_rowsHtml($row['sub'],$lestHead);
			}
		}
		return $html;
	}
		
	################################################################
	#  取得類別body的html
	#  data-tt-id 自己
	#  data-tt-parent-id 父層
	################################################################
	public function get_listHtml($rows,$lestHead) {
		#標題
		$thHtml = "";
		foreach($lestHead as $col => $v){
				$thHtml .= "<th{$v['th']['attr']}>{$v['th']['title']}</th>";
		}

		#內容
		$rowsHtml = $this->get_rowsHtml($rows,$lestHead);


		$count = count($lestHead);
		$html = "			
			<table id='form_table' class='table table-bordered table-striped table-hover'>
			  <thead>
			    <tr class='active'>
			      {$thHtml}
			    </tr>
			  </thead>
			  <!-- 根目錄開始 -->
			  <tr id='tr_0' data-tt-id='0'>
			    <td class='text-left' colspan={$count}>
			      <span class='folder'></span>
			      <i class='fa fa-home' aria-hidden='true'></i>根目錄

			      <a href='#' class='btn btn-success btn-xs' onclick=\"jQuery('#form_table').treetable('expandAll'); return false;\">展開<i class='fa fa-expand' aria-hidden='true'></i></a>

			      <a href='#' class='btn btn-danger btn-xs' onclick=\"jQuery('#form_table').treetable('collapseAll'); return false;\">闔起<i class='fa fa-compress' aria-hidden='true'></i></a>

			      <a href='?op=opForm&kind={$this->kind}&ofsn=0' class='btn btn-primary btn-xs' ait='在根目錄建立子類別'>新增<i class='fa fa-plus' aria-hidden='true'></i></a>
			    </td>
			  </tr>
			  <!-- 根目錄結束 -->

			  <tbody id='sort'>
			    {$rowsHtml}
			  </tbody>

			  <tfoot>
			    <tr>
			      <td colspan={$count} class='text-center'>
			        <input type='hidden' name='op' value='opAllInsert'>
			        <input type='hidden' name='kind' value='{$this->kind}'>
			        <button type='submit' class='btn btn-primary'>送出</button>
			      </td>
			    </tr>
			  </tfoot>
			</table>
		";
		

		return $html;
	}
	################################################################
	#  取得類別body的html
	#  data-tt-id 自己
	#  data-tt-parent-id 父層
	################################################################
	public function get_rowHtml($row,$lestHead) {		
		#row自己的層數
		$level = $this->get_thisLevel($row['sn']);
		$downLevel = $this->get_downLevel($row['sn']);
		//style='letter-spacing: 0;'
		$html = "
			<tr id='tr_{$row['sn']}' data-tt-id='{$row['sn']}' data-tt-parent-id='{$row['ofsn']}' level='{$row['level']}' downLevel='{$row['downLevel']}' sn='{$row['sn']}' kind='{$row['kind']}' class='level{$row['level']}' >\n";		

		foreach ($lestHead as $col => $table){

			$html .= "<td{$table['td']['attr']}>";

			if($col == "title"){
				#新增子類別
				$addLevelButton = ($this->stopLevel > $level) ? "
          <a href='?op=opForm&ofsn={$row['sn']}&kind={$row['kind']}' title='IN ({$row['title']}) 建立子類別' class='btn-primary btn-xs'>
          	<i class='fa fa-plus' aria-hidden='true'></i>          
          </a>" : "";

				#移動
				$moveButton = ($row['icon']['move_i']) ? "
					<i class='fa fa-arrows folder' aria-hidden='true' title='用來搬移此分類到其他分類之下，請拖曳之，到目的地分類。'></i>" : "";

				#資料夾
				$folderButton = ($row['icon']['folder_i']) ? "<span class='folder'></span>" : "";

				#一般input
				$html .= "
                      {$folderButton}{$moveButton}
                      <input type='text' name='title[{$row['sn']}]' value='{$row['title']}' id='title_{$row['sn']}' class='kind_title form-control'  style='width:65%;'>{$addLevelButton}
                    ";

			}elseif($col == "url"){	#一般input
				$html .= "                      
                      <input type='text' name='url[{$row['sn']}]' value='{$row['url']}' id='url_{$row['sn']}' class='kind_url form-control'  >
                  ";

			}elseif($col == "single_img"){

			  #----單檔圖片上傳
			  $moduleName = $this->moduleName; //專案名稱
			  $subdir = $this->kind; //子目錄
			  $col_name = $this->kind;//資料表關鍵字
			  $col_sn = $row['sn'];//商品流水號
			  $thumb = true ; //顯示縮圖
			  $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);//實體化
			  $row[$col] = $ugmUpFiles->get_rowPicSingleUrl($col_name,$col_sn,$thumb);
			  #-----------------------------------
			  if($row[$col]){				
					$html .= "<img src='{$row[$col]}' style='width:{$table['td']['imgWidth']}px;' class='img-responsive center-block'>";
			  }

			}elseif($col == "ps"){
				if($row[$col]){
					$html .= "<i class='fa {$row[$col]}' aria-hidden='true'></i>";
				}

			}elseif($col == "target"){

				if ($row[$col] == 1) {
					#啟用
					$target_0 = "本站";
					$html .= "<a href='?op=opUpdateTarget&sn={$row['sn']}&target=0&kind=" . $this->get_kind() . "' title='{$target_0}' atl='{$target_0}' class='btn-success btn-xs'><i class='fa fa-check' aria-hidden='true'></i></a>";
				} else {
					#停用
					$target_1 = "外連";
					$html .= "<a href='?op=opUpdateTarget&sn={$row['sn']}&target=1&kind=" . $this->get_kind() . "' title='{$target_1}' atl='{$target_1}' class='btn-danger btn-xs'><i class='fa fa-times' aria-hidden='true'></i></a>";
				}

			}elseif($col == "enable"){				

				if ($row[$col] == 1) {
					#啟用 
					$enable_0 = "停用";
					$html .= "<a href='?op=opUpdateEnable&sn={$row['sn']}&enable=0&kind=" . $this->get_kind() . "' title='{$enable_0}' atl='{$enable_0}' class='btn-success btn-xs'><i class='fa fa-check' aria-hidden='true'></i></a>";

				} else {
					#停用
					$enable_1 = "啟用";
					$html .= "<a href='?op=opUpdateEnable&sn={$row['sn']}&enable=1&kind=" . $this->get_kind() . "' title='{$enable_1}' atl='{$enable_1}' class='btn-danger btn-xs'><i class='fa fa-times' aria-hidden='true'></i></a>";

				}

			}elseif($col == "function"){
				$html .= "<i class='fa fa-sort' aria-hidden='true' style='cursor: s-resize;' title='可直接拉動排序'></i> ";
				foreach ($table['td']['btn'] as $btn) {
					if ($btn == "view") {

					} elseif ($btn == "edit") {
						$html .= "<a href='?op=opForm&sn={$row['sn']}&kind=" . $this->get_kind() . "' class='btn btn-xs btn-success'>"._EDIT."</a> ";

					} elseif ($btn == "del") {
						$disable = $downLevel ?" disabled":"";
						$html .= "<button type='button' class='btn btn-xs btn-danger btnDel{$disable}'>"._DELETE."</button> ";

					}
				}

			}
			$html .= "</td>";
		}
		$html .= "</tr>\n";
		return $html;

	}

	#以流水號取得某筆分類資料
	public function get_rowBYsn($sn) {
		global $xoopsDB;
		if (empty($sn)) {
			return;
		}
		$sql = "select * from `$this->tbl` where sn='{$sn}'"; //die($sql);

		$result = $xoopsDB->query($sql) or redirect_header(XOOPS_URL, 3, web_error());	 
		$row = $xoopsDB->fetchArray($result);
		return $row;
	}
	
	#######################################################
	#  取得父類別選單->選項(後台類別表單用)
	#  $default：外部傳進來預設值
	#  $enable：1 停用不顯示
	#######################################################
	public function get_ofsnOption($default, $ofsn = 0, $level = 1, $indent = "", $enable = 0) {
		global $xoopsDB;
		if ($level >= $this->stopLevel) {
			return;
		}
		$andKey = $enable ? " and `enable='{$enable}'`":"";

		$downLevel = $level + 1;
		$indent .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		$sql = "select * 
						from `{$this->tbl}`
            where ofsn='{$ofsn}' and kind='{$this->kind}'{$andKey}
            order by sort"; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header(XOOPS_URL, 3, web_error());
		$options = "";
		while ($row = $xoopsDB->fetchArray($result)) {
			$selected = ($default == $row['sn']) ? " selected" : "";
			$options .= "<option value='{$row['sn']}'{$selected}>{$indent}{$row['title']}</option>\n";
			$options .= $this->get_ofsnOption($default, $row['sn'], $downLevel, $indent, $enable);
		}
		return $options;
	}
	
	#######################################################
	#  取得類選項(前台)
	#  $default：外部傳進來預設值，一般與陣列
	#  $enable：1 停用不顯示
	#######################################################
	public function get_kindOption($default, $ofsn = 0, $level = 1, $indent = "", $enable = 1) {
		global $xoopsDB;
		if ($level > $this->stopLevel) {
			return;
		}
		$andKey = $enable ? " and `enable`='{$enable}'":"";
		$downIndent .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		$downLevel = $level + 1;
		$sql = "select * 
						from `{$this->tbl}`
            where ofsn='{$ofsn}' and kind='{$this->kind}'{$andKey}
            order by sort"; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header(XOOPS_URL, 3, web_error());
		$options = "";
		while ($row = $xoopsDB->fetchArray($result)) {
			if(is_array($default)){
				$selected = (in_array($row['sn'],$default)) ? " selected" : "";
			}else{
				$selected = ($default == $row['sn']) ? " selected" : "";
			}
			
			$options .= "<option value='{$row['sn']}'{$selected}>{$indent}{$row['title']}</option>\n";
			$options .= $this->get_kindOption($default, $row['sn'], $downLevel, $downIndent, $enable);
		}
		return $options;
	}

	#######################################################
	#  取得類選項(前台)
	#  $default：外部傳進來預設值
	#  $enable：1 停用不顯示
	#######################################################
	public function get_kindOptionVeqT($default, $ofsn = 0, $level = 1, $indent = "", $enable = 1) {
		global $xoopsDB;
		if ($level > $this->stopLevel) {
			return;
		}
		$andKey = $enable ? " and `enable`='{$enable}'":"";
		$downIndent .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		$downLevel = $level + 1;
		$sql = "select * 
						from `{$this->tbl}`
            where ofsn='{$ofsn}' and kind='{$this->kind}'{$andKey}
            order by sort"; //die($sql);
		$result = $xoopsDB->query($sql) or redirect_header(XOOPS_URL, 3, web_error());
		$options = "";
		while ($row = $xoopsDB->fetchArray($result)) {
			$selected = ($default == $row['title']) ? " selected" : "";
			$options .= "<option value='{$row['title']}'{$selected}>{$indent}{$row['title']}</option>\n";
			$options .= $this->get_kindOptionVeqT($default, $row['sn'], $downLevel, $downIndent, $enable);
		}
		return $options;
	}

	#######################################################
	#  取得記錄之父類別，最大排序
	#######################################################
	public function get_rowMaxSort($ofsn) {
		global $xoopsDB;
	  #取得記錄排序-----------------------------#
	  $sql = "select max(sort) as sort
	          from `{$this->tbl}`
	          where ofsn='{$ofsn}' and kind='{$this->kind}'";//die($sql);

	  $result = $xoopsDB->query($sql) or redirect_header(XOOPS_URL, 3, web_error());
	  list($sort) = $xoopsDB->fetchRow($result); 
	  return ++$sort;
	}
	
}