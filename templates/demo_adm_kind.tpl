<div class="CPbigTitle" style="background-image: url(<{xoAppUrl /modules/ugm_tools2/images/admin/button.png}>); background-repeat: no-repeat; background-position: left; padding-left: 50px;margin-bottom: 20px;">
  <strong><{$labelTitle}></strong>
</div>  

<{if $op=="opList"}>
  <link href="<{xoAppUrl}>modules/tadtools/treeTable/stylesheets/jquery.treetable.css" rel="stylesheet">
  <link href="<{xoAppUrl}>modules/tadtools/treeTable/stylesheets/jquery.treetable.theme.default.css" rel="stylesheet">
  <script type="text/javascript" src="<{xoAppUrl}>modules/tadtools/treeTable/javascripts/src/jquery.treetable.js"></script>

  <!-- sweet-alert -->
  <link rel="stylesheet" href="<{xoAppUrl}>modules/tadtools/sweet-alert/sweet-alert.css" type="text/css">
  <script src="<{xoAppUrl}>modules/tadtools/sweet-alert/sweet-alert.js" type="text/javascript"></script>

  <script type="text/javascript">
    $(function()  {
      //可以展開，預設展開
      $('#form_table').treetable({ expandable: true ,initialState: 'expanded' });

      // 配置拖動節點
      $('#form_table .folder').draggable({
        helper: 'clone',
        opacity: .75,
        refreshPositions: true, // Performance?
        revert: 'invalid',
        revertDuration: 300,
        scroll: true
      });

      // Configure droppable rows
      $('#form_table .folder').each(function() {
        $(this).parents('#form_table tr').droppable({
          accept: '.folder',
          drop: function(e, ui) {
            var droppedEl = ui.draggable.parents('tr');
            console.log(droppedEl[0]);
            $('#form_table').treetable('move', droppedEl.data('ttId'), $(this).data('ttId'));
            //alert( droppedEl.data('ttId'));
            //目地的sn ：$(this).data('ttId')
            //自己的sn：droppedEl.data('ttId')
            $.ajax({
              type:   'POST',
              url:    '?op=opSaveDrag',
              data:   { ofsn: $(this).data('ttId'), sn: droppedEl.data('ttId'),kind :"<{$kind}>" },
              success: function(theResponse) {
                swal({
                  title: theResponse,
                  text: '<{$smarty.const._MD_TREETABLE_MOVE_RETURN}>',
                  type: 'success',
                  showCancelButton: 0,
                  confirmButtonColor: '#3085d6',
                  confirmButtonText: '確定',
                  closeOnConfirm: false ,
                  allowOutsideClick: true
                },
                function(){
                  location.href="<{$smarty.session.returnUrl}>";
                });
              }
            });

          },
          hoverClass: 'accept',
          over: function(e, ui) {
            var droppedEl = ui.draggable.parents('tr');
            if(this != droppedEl[0] && !$(this).is('.expanded')) {
              $('#form_table').treetable('expandNode', $(this).data('ttId'));
            }
          }
        });
      });

      //排序
      $('#sort').sortable({ opacity: 0.6, cursor: 'move', update: function() {
          var order = $(this).sortable('serialize') + '&op=opUpdateSort';
          $.post('<{$action}>', order, function(theResponse){
            swal({
              title: theResponse,
              text: '<{$smarty.const._MD_TREETABLE_MOVE_RETURN}>',
              type: 'success',
              showCancelButton: 0,
              confirmButtonColor: '#3085d6',
              confirmButtonText: '確定',
              closeOnConfirm: false ,
              allowOutsideClick: true
              },
              function(){
                location.href="<{$smarty.session.returnUrl}>";
            });
          });
        }
      });

      //每行的删除操作注册脚本事件
      $(".btnDel").bind("click", function(){
        var vbtnDel=$(this);//得到点击的按钮对象
        var vTr=vbtnDel.parents("tr");//得到父tr对象;
        var sn=vTr.attr("sn");//取得 sn
        var kind=vTr.attr("kind");//取得 sn
        var title=$("#title_"+sn).val();//取得 title
        //警告視窗
        swal({
          title: '確定要刪除此資料？',
          text: title,
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#c9302c',
          cancelButtonColor: '#ec971f',
          confirmButtonText: '確定刪除！',
          cancelButtonText: '取消！'
          },
          function(){
            location.href="?op=opDelete&sn=" + sn+"&kind="+kind;
        });
      });

    });
  </script>
  <div class="panel panel-primary">
    <div class="panel-heading"><h3 class="panel-title"><{$listTitle}></h3></div>

    <div class="panel-body">
      <form action='<{$action}>' method='post' id='myForm'>
        <{$foreignForm}>
        <{$listHtml}>
        <{$token}>
      </form>
    </div>
  </div>
<{/if}>


<{if $op=="opForm"}>
  <!-- bootstrap 驗證 -->
  <link rel="stylesheet" href="<{xoAppUrl}>modules/ugm_tools2/class/bootstrapValidator/css/bootstrapValidator.css"/>
  <script type="text/javascript" src="<{xoAppUrl}>modules/ugm_tools2/class/bootstrapValidator/js/bootstrapValidator.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#myForm').bootstrapValidator({
          live: 'disabled',//
          message: '此值無效',
          feedbackIcons: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            title: {
              validators: {
                notEmpty: {
                  message: '必填'
                }
              }
            }
          }
      });
    });
  </script>
  
  <div class="panel panel-primary">
    <div class="panel-heading"><h3 class="panel-title"><{$row.formTitle}></h3></div>
    <div class="panel-body">
      <form role="form" action="<{$action}>" method="post" id="myForm" enctype="multipart/form-data">
        
        <{foreach from=$forms item=form key=r}>
          <div class="row">
          <{foreach from=$form item=cell key=col}>
            <{if $cell.type != "hidden"}>
              <div class="col-sm-<{$cell.width}>">
                <div class="form-group">
                  <label for="<{$col}>"><{$cell.label}></label>
                  <{if $cell.type == "text"}>
                    <input type='text' name='<{$col}>' value='<{$row.$col}>' id='<{$col}>' class="form-control">
                  <{elseif $cell.type == "radio"}>
                    <div>                
                      <input type='radio' name='<{$col}>' id='<{$col}>_1' value='1' <{if  $row.$col==1}>checked<{/if}>>
                      <label for='<{$col}>_1'>啟用</label>&nbsp;&nbsp;
                      <input type='radio' name='<{$col}>' id='<{$col}>_0' value='0' <{if $row.$col==0}>checked<{/if}>>
                      <label for='<{$col}>_0'>停用</label>                    
                    </div>
                  <{elseif $cell.type == "select"}>
                    <select name="<{$col}>" id="<{$col}>" class="form-control" size="1" >                      
                      <{$row.$col}>
                    </select>
                  <{elseif $cell.type == "icon"}>
                    <link rel="stylesheet" type="text/css" href="<{xoAppUrl}>modules/ugm_tools2/class/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css">
                    <script type="text/javascript" src="<{xoAppUrl}>modules/ugm_tools2/class/fontawesome-iconpicker/js/fontawesome-iconpicker.min.js"></script>
                    <script type='text/javascript'>
                      $(document).ready(function(){
                        $('#<{$col}>').iconpicker();
                      });
                    </script>

                    <div class="input-group iconpicker-container">
                      <input type="text" data-placement="bottomRight" class="form-control icp icp-auto iconpicker-element iconpicker-input" id="<{$col}>" name="<{$col}>" value="<{$row.$col}>">
                      <span class="input-group-addon"><i class="fa <{$row.$col}>"></i></span>
                    </div>
                  <{elseif $cell.type == "single_img"}>
                    <{$row.$col}>
                  <{/if}>
                </div>
              </div>
            <{else}>
              <input type='hidden' name='<{$col}>' value='<{$row.$col}>'>
            <{/if}>
          <{/foreach}>
          </div>
        <{/foreach}>
        <hr>
        <div class="form-group text-center">
          <button type="submit" class="btn btn-primary">送出</button>
          <{if !$row.sn}>
            <button type="reset" class="btn btn-danger">重設</button>
          <{/if}>
          <button type="button" class="btn btn-warning" onclick="location.href='<{$smarty.session.return_url}>'">返回</button>
          <input type='hidden' name='op' value='<{$row.op}>'>
          <input type='hidden' name='sn' value='<{$row.sn}>'>
          <input type='hidden' name='kind' value='<{$row.kind}>'>
          <{$token}>
        </div>
      </form>
    </div>
  </div>
<{/if}>









