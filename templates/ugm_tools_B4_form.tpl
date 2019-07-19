<form role="form" action="<{$SCRIPT_NAME}>" method="post" id="myForm" enctype="multipart/form-data">
  
  <{foreach from=$forms item=form key=r}>
    <div class="row">
    <{foreach from=$form item=cell key=col}>
      <{if $cell.formtype != "hidden"}>
        <div class="col-sm-<{$cell.width}>">
          <div class="form-group">
            <label for="<{$col}>"><{$cell.label}></label>
            <{if $cell.formtype == "text"}>
              <input type='text' name='<{$col}>' value='<{$row.$col}>' id='<{$col}>' class="form-control">
            <{elseif $cell.formtype == "radio"}>
              <div>                
                <input type='radio' name='<{$col}>' id='<{$col}>_1' value='1' <{if  $row.$col==1}>checked<{/if}>>
                <label for='<{$col}>_1'>啟用</label>&nbsp;&nbsp;
                <input type='radio' name='<{$col}>' id='<{$col}>_0' value='0' <{if $row.$col==0}>checked<{/if}>>
                <label for='<{$col}>_0'>停用</label>                    
              </div>
            <{elseif $cell.formtype == "select"}>
              <select name="<{$col}>" id="<{$col}>" class="form-control" size="1" >  
                <{$cell.option}>
              </select>
            <{elseif $cell.formtype == "textarea"}>
              <textarea class="form-control" rows="<{$cell.height}>" id="<{$col}>" name="<{$col}>"></textarea>
            <{elseif $cell.formtype == "icon"}>

              <input type='text' name='<{$col}>' value='<{$row.$col}>' id='<{$col}>' class="form-control">
              <{if $row.$col}>
                <span><i class="fa <{$row.$col}>" aria-hidden="true"></i></span>
              <{/if}>
              <span><a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">取得圖示</a></span>

            <{elseif $cell.formtype == "single_img"}>
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
    <button type="submit" class="btn btn-primary btn-sm">送出</button>
    <{if !$row.sn}>
      <button type="reset" class="btn btn-danger btn-sm">重設</button>
    <{/if}>
    <button type="button" class="btn btn-warning btn-sm" onclick="location.href='<{$smarty.session.return_url}>'">返回</button>
    <input type='hidden' name='op' value='<{$row.op}>'>
    <input type='hidden' name='sn' value='<{$row.sn}>'>
    <input type='hidden' name='kind' value='<{$row.kind}>'>
    <{$token}>
  </div>
</form>
<!-- jquery.validate/validate_b4.css -->
<link rel="stylesheet" type="text/css" href="<{xoAppUrl}>modules/ugm_tools2/class/jquery.validate/validate_b4.css">
<script type="text/javascript" src="<{xoAppUrl}>modules/ugm_tools2/class/jquery.validate/jquery.validate.min.js"></script>
<script type="text/javascript">
  $( document ).ready( function () {
    $( "#myForm" ).validate( {
      submitHandler: function(form) {
          //驗證成功之後就會進到這邊：
          //方法一：直接把表單 POST 或 GET 到你的 Action URL
          //方法二：讀取某些欄位的資料，ajax 給別的 API。
          //此處測試方法一的寫法如下：
          form.submit();
      },
      rules: {
        title: "required", //必填
      },

      messages: {
        title: "必填"
      },
      errorElement: "em",
      errorPlacement: function ( error, element ) {
        // Add the `help-block` class to the error element
        error.addClass( "help-block" );

        // Add `has-feedback` class to the parent div.form-group
        // in order to add icons to inputs
        element.closest( "div.form-group" ).addClass( "has-feedback" );

        //因checkbox、radio，外面多一層 label
        if ( element.prop( "type" ) === "checkbox" ||  element.prop( "type" ) === "radio") {
          error.insertAfter( element.parent( "label" ) );
        } else {
          error.insertAfter( element );
        }

        // Add the span element, if doesn't exists, and apply the icon classes to it.
        if ( !element.next( "span" )[ 0 ] ) {
          $( "<span class='form-control-feedback feedback-no fa fa-times'></span>" ).insertAfter( element );
        }
      },
      success: function ( label, element ) {
        // Add the span element, if doesn't exists, and apply the icon classes to it.
        if ( !$( element ).next( "span" )[ 0 ] ) {
          $( "<span class='form-control-feedback feedback-ok  fa fa-check'></span>" ).insertAfter(  element );
        }
      },
      highlight: function ( element, errorClass, validClass ) {
        //驗證失敗要做的事
        //在父親(div)+ has-error - has-success
        //在後面(span) + glyphicon-remove - glyphicon-ok

        $( element ).closest( "div.form-group" ).addClass( "has-error" ).removeClass( "has-success" );
        $( element ).next( "span" ).addClass( "fa-times" ).removeClass( "fa-check" );
        $( element ).next( "span" ).addClass( "feedback-no" ).removeClass( "feedback-ok" );
      },
      unhighlight: function ( element, errorClass, validClass ) {
        //驗證成功要做的事
        $( element ).closest( "div.form-group" ).addClass( "has-success" ).removeClass( "has-error" );
        $( element ).next( "span" ).addClass( "fa-check" ).removeClass( "fa-times" );
        $( element ).next( "span" ).addClass( "feedback-ok" ).removeClass( "feedback-no" );
      }
    } );
  } );
</script>



