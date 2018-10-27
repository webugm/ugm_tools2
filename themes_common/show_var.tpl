<{if $xoops_isadmin and $show_var}>
  <style>
    .show_var .red{color:red;}
    .show_var .title{background-color:#64FA74;color:block;font-weight: bold;text-align:center;}
  </style>
  <!-- &lt;{}&gt; -->
  <div style="background-color:#D2C7C7">
    <table class="table table-striped table-bordered table-hover show_var">
      <!-- XOOPS內置的Smarty全局變數 -->
      <thead>
        <tr>
          <th colspan=3 class="title">XOOPS內置的Smarty全局變數</th>
        </tr>
        <tr>
          <th>標籤 </th>
          <th>說明</th>
          <th>值</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="red">&lt;{$xoops_page}&gt;</td>
          <td>當前頁面精簡名稱</td>
          <td><{$xoops_page}></td>
        </tr>
        <tr>
        <tr>
          <td class="red">&lt;{$xoops_theme}&gt;</td>
          <td>使用佈景名稱</td>
          <td><{$xoops_theme}></td>
        </tr>
        <tr>
          <td class="red">&lt;{$xoops_isadmin}&gt;</td>
          <td>是否為管理員</td>
          <td><{$xoops_isadmin}></td>
        </tr>
        <tr>
          <td class="red">&lt;{$xoTheme->folderName}&gt;</td>
          <td>本佈景名稱</td>
          <td><{$xoTheme->folderName}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_imageurl}&gt;</td>
          <td>佈景路徑</td>
          <td><{$xoops_imageurl}></td>
        </tr>
        <tr>
          <td class="red">&lt;{xoImgUrl}&gt;</td>
          <td>佈景路徑(plugins)</td>
          <td><{xoImgUrl}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_url}&gt;</td>
          <td>網址</td>
          <td><{$xoops_url}></td>
        </tr>
        <tr>
          <td class="red">&lt;{xoAppUrl}&gt;</td>
          <td>網址(plugins)</td>
          <td><{xoAppUrl}></td>
        </tr>        
        <tr>
          <td class="red">&lt;{$xoops_rootpath}&gt;</td>
          <td>實體路徑</td>
          <td><{$xoops_rootpath}></td>
        </tr>
        <tr>
          <td>&lt;{$theme_name}&gt;</td>
          <td>樣版自訂變數(目錄名稱)</td>
          <td><{$theme_name}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_sitename}&gt;</td>
          <td>網站名稱</td>
          <td><{$xoops_sitename}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_slogan}&gt;</td>
          <td>網站口號</td>
          <td><{$xoops_slogan}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_charset}&gt;</td>
          <td>網頁編碼(字符集) 如UTF-8、GB2312</td>
          <td><{$xoops_charset}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_langcode}&gt;</td>
          <td>語言代碼如en、zh-CN</td>
          <td><{$xoops_langcode}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_banner}&gt;</td>
          <td>廣告內容： 系統/廣告管理</td>
          <td><{$xoops_banner}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_footer}&gt;</td>
          <td>頁腳信息</td>
          <td><{$xoops_footer}></td>
        </tr>
      </tbody>
      <!-- 用戶相關的變數 -->
      <thead>
        <tr>
          <th colspan=3 class="title">用戶相關的變數</th>
        </tr>
        <tr>
          <th>標籤 </th>
          <th>說明</th>
          <th>值</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="red">&lt;{$xoops_userid}&gt;</td>
          <td>用戶ID</td>
          <td><{$xoops_userid}></td>
        </tr>
        <tr>
          <td class="red">&lt;{$xoops_uname}&gt;</td>
          <td>帳號</td>
          <td><{$xoops_uname}></td>
        </tr>
        <tr>
          <td class="red">&lt;{$xoops_name}&gt;</td>
          <td>用戶姓名</td>
          <td><{$xoops_name}></td>
        </tr>
        <tr>
          <td class="red">&lt;{$xoops_isuser}&gt;</td>
          <td>如果是註冊用戶則為1，否則為0</td>
          <td><{$xoops_isuser}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_isadmin}&gt;</td>
          <td>如果是管理員則為1，否則為0(管理員是指當前模塊的管理員，不是全站)</td>
          <td><{$xoops_isadmin}></td>
        </tr>
        <tr>
          <td class="red">&lt;{$xoops_user_method}&gt;</td>
          <td>註冊用戶在個人資料中所選擇的通知方式</td>
          <td><{$xoops_user_method}></td>
        </tr>
      </tbody>
      <thead>
        <tr>
          <th colspan=3 class="title">Meta相關的變數</th>
        </tr>
        <tr>
          <th>標籤 </th>
          <th>說明</th>
          <th>值</th>
        </tr>
      </thead>  
      <tbody>
        <tr>
          <td class="red">&lt;{$xoops_meta_keywords}&gt;</td>
          <td>Meta關鍵詞</td>
          <td><{$xoops_meta_keywords}></td>
        </tr>
        <tr>
          <td class="red">&lt;{$xoops_meta_description}&gt;</td>
          <td>Meta網站描述</td>
          <td><{$xoops_meta_description}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_meta_copyright}&gt;</td>
          <td>Meta版權</td>
          <td><{$xoops_meta_copyright}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_meta_robots}&gt;</td>
          <td> Meta機器人</td>
          <td><{$xoops_meta_robots}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_meta_rating}&gt;</td>
          <td>Meta等級</td>
          <td><{$xoops_meta_rating}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_meta_author}&gt;</td>
          <td>Meta作者</td>
          <td><{$xoops_meta_author}></td>
        </tr>
      </tbody>
      <thead>
        <tr>
          <th colspan=3 class="title">區塊位置相關的變數</th>
        </tr>
        <tr>
          <th>標籤 </th>
          <th>說明</th>
          <th>值</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>&lt;{$xoBlocks.canvas_left}&gt;</td>
          <td>左區塊</td>
          <td><{$xoBlocks.canvas_left}></td>
        </tr>
        <tr>
          <td>&lt;{}&gt;</td>
          <td></td>
          <td></td>
        </tr>
      </tbody>

      <thead>
        <tr>
          <th colspan=3 class="title">判斷區塊位置是否有區塊的變量</th>
        </tr>
        <tr>
          <th>標籤 </th>
          <th>說明</th>
          <th>值</th>
        </tr>
      </thead>

       
      <tbody>
        <tr>
          <td>&lt;{$xoops_showlblock}&gt;</td>
          <td>若$xoBlocks.canvas_left或$xoops_lblocks非空則$xoops_showrblock值為1，否則為0</td>
          <td><{$xoops_showlblock}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_showrblock}&gt;</td>
          <td>若$xoBlocks.canvas_right或$xoops_lblocks非空則$xoops_showrblock值為1，否則為0</td>
          <td><{$xoops_showrblock}></td>
        </tr>

        <tr>
          <td>&lt;{$xoops_showcblock}&gt;</td>
          <td>若以下三個任何一個非空：<br>
              ‧$xoBlocks.page_topleft<br>
              ‧$xoBlocks.page_topcenter<br>
              ‧$xoBlocks.page_topright<br>
              則$xoops_showcblock值為1，否則為0</td>
          <td><{$xoops_showcblock}></td>
        </tr>
      </tbody>

      <thead>
        <tr>
          <th colspan=3 class="title">模組頁面相關輸出變數</th>
        </tr>
        <tr>
          <th>標籤 </th>
          <th>說明</th>
          <th>值</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="red">&lt;{$xoops_dirname}&gt;</td>
          <td>當前模塊的目錄名稱</td>
          <td><{$xoops_dirname}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_contents}&gt;</td>
          <td>模塊輸出的頁面內容</td>
          <td></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_pagetitle}&gt;</td>
          <td>模塊輸出的頁面標題</td>
          <td><{$xoops_pagetitle}> </td>
        </tr>
        <tr>
          <td>&lt;{$xoops_module_header}&gt;</td>
          <td>模塊輸出的header內容</td>
          <td></td>
        </tr>

        <tr>
          <td>&lt;{$SCRIPT_NAME}&gt;</td>
          <td>當前訪問頁面的php文件</td>
          <td><{$SCRIPT_NAME}></td>
        </tr>
        <tr>
          <td>&lt;{$xoops_requesturi}&gt;</td>
          <td>當前訪問頁面的URI</td>
          <td><{$xoops_requesturi}></td>
        </tr>        
        <tr>
          <td class="red">&lt;{$module_id}&gt;</td>
          <td>特定模組id</td>
          <td><{$module_id}></td>
        </tr> 
      </tbody>


      <thead>
        <tr>
          <th colspan=3 class="title"></th>
        </tr>
        <tr>
          <th>標籤 </th>
          <th>說明</th>
          <th>值</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>&lt;{xoImgUrl css/xoops.css}&gt;</td>
          <td></td>
          <td><{xoImgUrl css/xoops.css}></td>
        </tr>
        <tr>
          <td>&lt;{}&gt;</td>
          <td></td>
          <td></td>
        </tr>
      </tbody>

    </table>
    
  </div>
<{/if}>