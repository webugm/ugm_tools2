<?php
/*
#表單
    #----單檔圖片上傳
    $moduleName = $ugmKind->get_moduleName();          //模組名稱(ugm_tools)
    $subdir = $kind;                                   //子目錄(前後不要有 / )
    $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);//實體化 

    $name = "single_img";                              //表單欄位名稱
    $col_name = $kind;                                 //資料表關鍵字
    $col_sn = $row['sn'];                              //關鍵字流水號
    $multiple = false;                                 //單檔 or 多檔上傳
    $accept = "image/*";                               //可接受副檔名

    $row['single_img'] = $ugmUpFiles->upform($name,$col_name,$col_sn,$multiple,$accept);
    #-----------------------------------    
#寫入
    #----單圖上傳  
    $moduleName = $ugmKind->get_moduleName();             //模組名稱(ugm_tools)
    $subdir = $kind;                                      //子目錄(前後不要有 / )
    $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);   //實體化
    
    $name = "single_img";                                 //表單欄位名稱
    $col_name = $kind;                                    //資料表關鍵字
    $col_sn = $sn;                                        //關鍵字流水號
    $multiple = false;                                    //單檔 or 多檔上傳
    $main_width = -1;                                     //大圖壓縮尺吋，-1則不壓縮
    $thumb_width = 120;                                   //小圖壓縮尺吋
    $ugmUpFiles->upload_file($name,$col_name,$col_sn,$multiple,$main_width,$thumb_width);
    #------------------------------------------------
#顯示
	  #----單檔圖片上傳
	  $moduleName = $this->moduleName;                      //模組名稱(ugm_tools)
	  $subdir = $this->kind;                                //子目錄(前後不要有 / )
	  $ugmUpFiles = new ugmUpFiles($moduleName, $subdir);   //實體化

	  $col_name = $this->kind;                              //資料表關鍵字
	  $col_sn = $row['sn'];                                 //關鍵字流水號
	  $thumb = true ;                                       //顯示縮圖
	  $row[$col] = $ugmUpFiles->get_rowPicSingleUrl($col_name,$col_sn,$thumb);
	  #-----------------------------------
	  if($row[$col]){				
			$html .= "<img src='{$row[$col]}' style='width:{$table['td']['imgWidth']}px;' class='img-responsive center-block'>";
	  }

 */
#---------------------------------
defined('XOOPS_ROOT_PATH') || die("XOOPS root path not defined");
class ugmUpFiles {
	public $moduleName; //目錄名稱 「ugm_p」
	public $tbl; //資料表名稱
	public $subdir; //資料表欄位

	public $ugmUpFilesDir; //檔案dir
	public $ugmUpFilesUrl; //檔案url
	public $ugmUpFilesImgDir; //圖片dir
	public $ugmUpFilesImgUrl; //圖片url
	public $ugmUpFilesThumbDir; //縮圖dir
	public $ugmUpFilesThumbUrl; //縮圖url

	public $col_name; //資料表欄位
	public $col_sn; //資料表欄位
	public $sort; //資料表欄位

	public $file_dir = "file";
	public $image_dir = "";
	public $thumbs_dir = "thumbs";

	public $main_width = 1280; #縮圖寬度
	public $thumb_width = 120; #縮圖寬度
	public $thumb_height = 70; #縮圖高度
	public $multiple = true;   #多檔
	
	#建構元(模組名稱,子目錄(前後不含 / ))
	function __construct($moduleName, $subdir) {
		#設定模組名稱
		$this->set_moduleName($moduleName);
		#設定子目錄
		$this->set_subdir($subdir);
		#設定檔案目錄
		$this->set_file_dir();
		#設定檔案目錄
		$this->set_image_dir();
		#設定縮圖目錄
		$this->set_thumbs_dir();
	}

	#----設定類
	
	#設定模組名稱
	public function set_moduleName($moduleName) {
		global $xoopsDB;
		$this->moduleName = $moduleName;		
		#設定資料表
		$this->tbl = $xoopsDB->prefix("{$moduleName}_files_center");
	}

	#設定子目錄(前後不含 /)
	public function set_subdir($value="") {
		$this->subdir = $value ? $value : $this->subdir;
		$this->set_path();
	}

	#設定檔案目錄(前後不含 /)
	public function set_file_dir($value="") {
		$this->file_dir = $value ? $value : $this->file_dir;
		$this->set_path();
	}

	#設定圖片目錄(前後不含 /)
	public function set_image_dir($value="") {
		$this->image_dir = $value ? $value : $this->image_dir;
		$this->set_path();
	}

	#設定縮圖目錄(前後不含 /)
	public function set_thumbs_dir($value="") {
		$this->thumbs_dir = $value ? $value : $this->thumbs_dir;
		$this->set_path();
	}

	#設定路徑
	public function set_path() {
		$file_dir = $this->file_dir ?"{$this->file_dir}/":"";
		$image_dir = $this->image_dir ?"{$this->image_dir}/":"";
		$thumbs_dir = $this->thumbs_dir ?"{$this->thumbs_dir}/":"";

		$this->ugmUpFilesDir = XOOPS_ROOT_PATH . "/uploads/{$this->moduleName}/{$this->subdir}/{$file_dir}";
		$this->ugmUpFilesUrl = XOOPS_URL . "/uploads/{$this->moduleName}/{$this->subdir}/{$file_dir}";
		$this->ugmUpFilesImgDir = XOOPS_ROOT_PATH . "/uploads/{$this->moduleName}/{$this->subdir}/{$image_dir}";
		$this->ugmUpFilesImgUrl = XOOPS_URL . "/uploads/{$this->moduleName}/{$this->subdir}/{$image_dir}";
		$this->ugmUpFilesThumbDir = XOOPS_ROOT_PATH . "/uploads/{$this->moduleName}/{$this->subdir}/{$thumbs_dir}";
		$this->ugmUpFilesThumbUrl = XOOPS_URL . "/uploads/{$this->moduleName}/{$this->subdir}/{$thumbs_dir}";
	}

	//設定欄名，sn,sort
	public function set_col($col_name = "", $col_sn = "", $sort = "") {
		$this->col_name = $col_name;
		$this->col_sn = $col_sn;
		$this->sort = $sort;
	}

	#------------------------------------
	
	#上傳表單
	public function upform($name='pic',$col_name,$col_sn="",$multiple=false,$accept = "image/*") {
		$this->col_name = $col_name;
		$this->col_sn = $col_sn;
		$this->multiple = $multiple;

		$accept = $accept ? "accept='{$accept}'" : ""; // image/* ,
		$multiple = $multiple ? "multiple='multiple'" : "";
		$show = "";
		if ($col_sn) {
			$show = $this->upformShow();
		}
		$main = "
    <input type='file' name='{$name}[]' $multiple $accept class='form-control'><br>{$show}
    ";
		return $main;
	}

	//表單列出舊圖
	private function upformShow() {
		global $xoopsDB;
		$sql = "select * 
						from `{$this->tbl}`
            where `col_name`='{$this->col_name}' and `col_sn`='{$this->col_sn}'
            order by sort"; //die($sql);

		$result = $xoopsDB->query($sql) or redirect_header(XOOPS_URL, 3, web_error());
		
		$rows = "
		";
		while ($row = $xoopsDB->fetchArray($result)) {
			
			if ($row['kind'] == "file") {
				$thumb_pic = TADTOOLS_URL . "/multiple-file-upload/downloads.png";
			} else {
				$thumb_pic = $this->ugmUpFilesThumbUrl.$row['file_name'];
			}

			$move = $this->multiple ? "<p><i class='glyphicon glyphicon-move'></i>&nbsp;sort:&nbsp;{$row['sort']}</p>\n" : "";
			#顯示
			$del_checkbox = "
				<p>\n
          <input type='checkbox' name='del_{$this->col_name}[]' value='{$row['files_sn']}' id='del_{$row['files_sn']}'>\n
          <label class='checkbox-inline' for='del_{$row['files_sn']}'>"._DELETE."</label>\n
        </p>\n
			";
			$width = $this->multiple ?"3":"12";

			$rows .= "
        <li class='col-sm-{$width}' id='li_{$row['files_sn']}'>
          <div class='thumbnail'>
            <img src='{$thumb_pic}' alt='' class='img-responsive'>
            $del_checkbox
          </div>
        </li>";
		}

		$files = "";
		if ($rows) {
			$files = "
	      <div class='row' style='margin-top:5px;'>
	        <ul class='thumbnails' id='sort_{$this->col_sn}' style='list-style-type: none;'>
	          $rows
	        </ul>
	      </div>
	    ";
		}

		return $files;
	}

	//上傳
	public function upload_file($name,$col_name,$col_sn,$multiple=false,$main_width, $thumb_width) {
		global $xoopsDB;
		$this->col_name = $col_name;
		$this->col_sn = $col_sn;
		$this->multiple = $multiple;

		$main_width = $main_width ? $main_width :1280;
		$thumb_width = $thumb_width ? $thumb_width :120;

		//引入上傳物件
		include_once XOOPS_ROOT_PATH . "/modules/tadtools/upload/class.upload.php";

		//取消上傳時間限制
		set_time_limit(0);
		//設置上傳大小
		//ini_set('memory_limit', '80M');
		ini_set('memory_limit', '-1');

		#---------------------------------------
		//刪除勾選檔案
		if (!empty($_POST["del_{$this->col_name}"])) {
			foreach ($_POST["del_{$this->col_name}"] as $del_files_sn) {
				$this->del_files($del_files_sn);
			}
		}
		#---------------------------------------
		$files = array();
		if($_FILES[$name]){
			foreach ($_FILES[$name] as $k => $l) {
				foreach ($l as $i => $v) {
					if (!array_key_exists($i, $files)) {
						$files[$i] = array();
					}
					$files[$i][$k] = $v; //$file[0][name]=xxx.jpg
				}
			}			
		}
		//處理檔案上傳，檢查是否有上傳$_FILES[$name]['name'][0]
		if ($files) {
			#有上傳
			foreach ($files as $file) {
				
				//自動排序
				if (empty($this->sort)) {
					$this->sort = $this->auto_sort();
				}

				//取得檔案
				$file_handle = new upload($file, "zh_TW");

				if ($file_handle->uploaded) {
					
					#單檔上傳，先刪舊檔--------------------
					if (!$this->multiple) {
						$this->del_files();
					}
					#---------------------------------------
					//取得副檔名
					$ext = strtolower($file_handle->file_src_name_ext);

					//判斷檔案種類
					if ($ext == "jpg" or $ext == "jpeg" or $ext == "png" or $ext == "gif") {
						$kind = "img";
					} else {
						$kind = "file";
					}

					$file_handle->file_safe_name = false;//會把檔名的空白改為「_」
					$file_handle->file_overwrite = true;//強制覆寫相同檔名
					$file_handle->no_script = false;

    			$rand = substr(md5(uniqid(mt_rand(), 1)), 0, 5);//取得一個5碼亂數
					$new_filename = $rand ."_".$this->col_sn;

					$file_handle->file_new_name_body = $new_filename;//重新設定新檔名
					//print_r($file_handle);die();

					//若是圖片才縮圖 且 $main_width != -1
					if ($kind == "img" and $main_width != "-1") {
						if ($file_handle->image_src_x > $main_width) {
							$file_handle->image_resize = true;                 //要重設圖片大小
							$file_handle->image_x = $main_width;         //設定寬度為 $main_width
							$file_handle->image_ratio_y = true;                // 按比例縮放高度
							//$file_handle->image_convert = 'png';             //轉檔為png格式，方便管理
						}
					}

					$path = ($kind == "img") ? $this->ugmUpFilesImgDir : $this->ugmUpFilesDir;

					$file_handle->process($path);//檔案搬移到目的地
					$file_handle->auto_create_dir = true;
					#------------------------------------------------------

					//若是圖片才製作小縮圖
					if ($kind == "img") {
						$file_handle->file_safe_name = false;
						$file_handle->file_overwrite = true;

						$file_handle->file_new_name_body = $new_filename;
						//echo  $this->$thumb_width;die();

						if ($file_handle->image_src_x > $thumb_width) {
							$file_handle->image_resize = true;
							$file_handle->image_x = $thumb_width;
							$file_handle->image_ratio_y = true;
						}
						$file_handle->process($this->ugmUpFilesThumbDir);
						$file_handle->auto_create_dir = true;
					}
					#------------------------------------------------------

					#------------------------------------------------------
					//上傳檔案
					if ($file_handle->processed) {
						$file_handle->clean();
				
						$sql = "insert into `{$this->tbl}`  
										(`col_name`,`col_sn`,`sort`,`kind`,`file_name`,`file_type`,`file_size`,`description`,`counter`,`original_filename`,`sub_dir`) 
										values
										('{$this->col_name}','{$this->col_sn}','{$this->sort}','{$kind}','{$new_filename}.{$ext}','{$file['type']}','{$file['size']}','{$file['name']}',0,'{$file['name']}','{$this->subdir}')"; //die($sql);

						$xoopsDB->queryF($sql) or redirect_header(XOOPS_URL, 3, web_error());

					}else{
						redirect_header(XOOPS_URL, 3, $file_handle->error);
					}
				}
				$this->sort = "";
			}

		}

	}

	//自動編號
	public function auto_sort() {
		global $xoopsDB;

		$sql = "select max(sort) from `{$this->tbl}`  where `col_name`='{$this->col_name}' and `col_sn`='{$this->col_sn}'";

		$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
		list($max) = $xoopsDB->fetchRow($result);
		return ++$max;
	}

	//刪除實體檔案
	public function del_files($files_sn = "") {
		global $xoopsDB;
		if (!empty($files_sn)) {
			$del_what = "`files_sn`='{$files_sn}'";
		} elseif (!empty($this->col_name) and !empty($this->col_sn)) {
			$del_what = "`col_name`='{$this->col_name}' and `col_sn`='{$this->col_sn}'";
		}

		$sql = "select * from `{$this->tbl}`  where $del_what";// die($sql);		
		$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());

		while ($row =  $xoopsDB->fetchArray($result)) {		
			$del_sql = "delete  from `{$this->tbl}`  where files_sn='{$row['files_sn']}'"; 						
			$xoopsDB->queryF($del_sql) or redirect_header($_SERVER['PHP_SELF'], 3, web_error());
			if ($row['kind'] == "img") {
				unlink($this->ugmUpFilesImgDir.$row['file_name']);
				unlink($this->ugmUpFilesThumbDir.$row['file_name']);
			} else {
				unlink($this->ugmUpFilesDir.$row['file_name']);
			}
		}
	}

	//顯示記錄單一網址
	public function get_rowPicSingleUrl($col_name,$col_sn,$thumb) {
		global $xoopsDB;
		$sql = "select * 
				from `{$this->tbl}`
            where `col_name`='{$col_name}' and `col_sn`='{$col_sn}'
            order by sort
            limit 1
            "; //die($sql);
		$result = $xoopsDB->queryF($sql) or web_error($sql);
		$row = $xoopsDB->fetchArray($result);
		if ($row['kind'] == "file") {
				$thumb_pic = TADTOOLS_URL . "/multiple-file-upload/downloads.png";
		}else{
			if($row['file_name']){
				if($thumb){
					$thumb_pic = $this->ugmUpFilesThumbUrl.$row['file_name'];
				}else{					
					$thumb_pic = $this->ugmUpFilesImgUrl.$row['file_name'];
				}
			}else{
				$thumb_pic = "";
			}
		}
		return $thumb_pic;
	}
	//顯示圖片路徑
	public function get_rowPicSingleDir($col_name,$col_sn,$thumb) {
		global $xoopsDB;
		$sql = "select * 
				from `{$this->tbl}`
            where `col_name`='{$col_name}' and `col_sn`='{$col_sn}'
            order by sort
            limit 1
            "; //die($sql);
		$result = $xoopsDB->queryF($sql) or web_error($sql);
		$row = $xoopsDB->fetchArray($result);
		if ($row['kind'] == "file") {
				$thumb_pic = TADTOOLS_PATH . "/multiple-file-upload/downloads.png";
		}else{
			if($row['file_name']){
				if($thumb){
					$thumb_pic = $this->ugmUpFilesThumbDir.$row['file_name'];
				}else{					
					$thumb_pic = $this->ugmUpFilesImgDir.$row['file_name'];
				}
			}else{
				$thumb_pic = "";
			}
		}
		return $thumb_pic;
	}	

	//得到上傳檔案src(只有一張)
	public function get_rowFileSingleUrl($col_name,$col_sn) {
		global $xoopsDB;
		$src = "";
		$sql = "select *
				from `{$this->tbl}`
                where `col_name`='{$col_name}' and `col_sn`='{$col_sn}'
                order by sort
                limit 1
            "; //die($sql);
		$result = $xoopsDB->queryF($sql) or web_error($sql);
		$row = $xoopsDB->fetchArray($result);
		//以下會產生這些變數： $files_sn, $col_name, $col_sn, $sort, $kind, $file_name, $file_type, $file_size, $description
		if ($row['files_sn'] and $row['kind'] == "file") {
			$src = $this->ugmUpFilesUrl.$row['file_name'];
		}
		return $src;
	}	
	
	//得到上傳檔案名檔(只有一張)
	public function get_file_name($col_name="",$col_sn="",$sn="",$kind="url") {
		global $xoopsDB;
		$path = "";
		if($sn){
			$sql = "select *
							from `{$this->tbl}`
							where `sn`='{$sn}'
							"; //die($sql);
		}else{
			$sql = "select *
							from `{$this->tbl}`
							where `col_name`='{$col_name}' and `col_sn`='{$col_sn}'
							order by sort
							limit 1
							"; //die($sql);
		}
		$result = $xoopsDB->queryF($sql) or web_error($sql);
		$row = $xoopsDB->fetchArray($result);
		$name = "";
		if($row['files_sn'] ){
			if ($row['kind'] == "file") {
				if($kind == "url"){
					$name = $this->ugmUpFilesUrl.$row['file_name'];
				}else{
					$name = $this->ugmUpFilesDir.$row['file_name'];
				}
			}else{
				if($kind == "url"){
					$name = $this->ugmUpFilesImgUrl.$row['file_name'];
				}else{
					$name = $this->ugmUpFilesImgDir.$row['file_name'];
				}
			}
			return $name;
		}
		return false;
	}	

	//得到單筆 副檔名
	public function get_file_name_ext($col_name="",$col_sn="",$sn="") {
		global $xoopsDB;
		if($sn){
			$sql = "select *
							from `{$this->tbl}`
							where `sn`='{$sn}'
							"; //die($sql);
		}else{
			$sql = "select *
							from `{$this->tbl}`
							where `col_name`='{$col_name}' and `col_sn`='{$col_sn}'
							order by sort
							limit 1
							"; //die($sql);
		}
		$result = $xoopsDB->queryF($sql) or web_error($sql);
		$row = $xoopsDB->fetchArray($result);
		if($row['file_name']){			
  		$ext =  explode(".",$row['file_name']);
			return $ext[1];
		}
		return false;
	}

}