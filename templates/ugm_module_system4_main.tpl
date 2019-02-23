<{includeq file="$xoops_rootpath/modules/ugm_tools2/templates/ugm_tools_toolbar.tpl"}>
<{if $op=="opList"}>
  <div>
    <div class='row' style="margin-bottom: 10px;">
      <div class='col-sm-3'>
        <{$kind_form}>
      </div>
    </div>
    <div id="save_msg"></div>
    <table id="form_table" class="table table-bordered table-condensed table-hover">
      <thead>
        <tr>
          <th class="text-center" style="width:60px;">序</th>
          <th class="text-center" style="width:35%;">變數名稱</th>
          <th class="text-center">變數值</th>
          <th class="text-center" style="width:60px;"><{$smarty.const._MD_UGMMODULE_FUN}></th>
        </tr>
      </thead>
      <tbody id='sort'>
        <{foreach  from=$rows item=row key=id}>
          <tr id='tr_<{$row.sn}>'>
            <td class="text-center">
              <{$row.sort}>
            </td>
            <td class="text-left">
              <{$row.title}>
            </td>
            <td class="text-left">
              <{$row.value}>
            </td>
            <td class="text-center">
              <a href="system.php?op=opForm&sn=<{$row.sn}>&kind=<{$kind}>&name=<{$row.name}>" class="btn  btn-success btn-sm">
                <{$smarty.const._EDIT}>
              </a>
            </td>
          </tr>
        <{/foreach}>
      </tbody>
    </table>        
  </div>
<{/if}>

<{if $op=="opForm"}>
  <div class="card">
    <div class="card-header bg-primary text-white">
      <{$row.form_title}>
    </div>
    <div class="card-body">
      <form role="form" action="<{$SCRIPT_NAME}>" method="post" id="myForm"  enctype="multipart/form-data">
        <div class="row">
          <div class="col-sm-8">
            <div class="form-group">
              <label for="title"><{$row.title}></label>
              <{$row.form}>
            </div>
          </div>
          <div class="col-sm-4">
            <br />
            <{$row.description}>
          </div>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary btn-sm"><{$smarty.const._SUBMIT}></button>
          <button type="button" class="btn btn-warning btn-sm" onclick="location.href='<{$smarty.session.return_url}>'"><{$smarty.const._BACK}></button>
          <input type='hidden' name='kind' value='<{$row.kind}>'>
          <input type='hidden' name='name' value='<{$row.name}>'>
          <input type='hidden' name='op' value='<{$row.op}>'>
          <input type='hidden' name='sn' value='<{$row.sn}>'>
          <input type='hidden' name='return' value='<{$smarty.session.return_url}>'>
        </div>
      </form>
    </div>
  </div>
<{/if}>









