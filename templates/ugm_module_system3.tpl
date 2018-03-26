<{includeq file="$xoops_rootpath/modules/ugm_tools2/templates/ugm_tools_toolbar.tpl"}>

<{if $op=="opList"}>
  <div class='container-fluid'>
    <div class='row' style="margin-bottom: 10px;">
      <div class='col-sm-3'>
        <{$kind_form}>
      </div>
    </div>
    <div class='row'>
      <div id="save_msg"></div>
      <table id="form_table" class="table table-bordered table-condensed table-hover">
        <thead>
          <tr>
            <th class="col-sm-1 text-center"><{$smarty.const._MD_UGMMODULE_ITEM}></th>
            <th class="col-sm-3 text-center"><{$smarty.const._MD_UGMMODULE_VARNAME}></th>
            <th class="col-sm-7 text-center"><{$smarty.const._MD_UGMMODULE_VARVALUE}></th>
            <th class="col-sm-1 text-center"><{$smarty.const._MD_UGMMODULE_FUN}></th>
          </tr>
        </thead>
        <tbody id='sort'>
          <{foreach item=list key=id from=$list}>
            <tr id='tr_<{$list.sn}>'>
              <td class="text-center">
                <{$list.sort}>
              </td>
              <td class="text-left">
                <{$list.title}>
              </td>
              <td class="text-left">
                <{$list.value}>
              </td>
              <td class="text-center">
                <a href="system.php?op=opForm&sn=<{$list.sn}>&kind=<{$kind}>&name=<{$list.name}>" class="btn  btn-success btn-xs">
                  <{$smarty.const._EDIT}>
                </a>
              </td>
            </tr>
          <{/foreach}>
        </tbody>
      </table>
    </div>
  </div>
<{/if}>

<{if $op == "opForm"}>  
  <div class="panel panel-primary">
    <div class="panel-heading"><h3 class="panel-title"><{$DBV.form_title}></h3></div>
    <div class="panel-body">
      <form role="form" action="<{$SCRIPT_NAME}>" method="post" id="myForm"  enctype="multipart/form-data">
        <div class="row">
          <div class="col-sm-8">
            <div class="form-group">
              <label for="title"><{$DBV.title}></label>
              <{$DBV.form}>
            </div>
          </div>
          <div class="col-sm-4">
            <br />
            <{$DBV.description}>
          </div>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary"><{$smarty.const._SUBMIT}></button>
          <button type="button" class="btn btn-warning" onclick="location.href='<{$smarty.session.return_url}>'"><{$smarty.const._BACK}></button>
          <input type='hidden' name='kind' value='<{$DBV.kind}>'>
          <input type='hidden' name='name' value='<{$DBV.name}>'>
          <input type='hidden' name='op' value='<{$DBV.op}>'>
          <input type='hidden' name='sn' value='<{$DBV.sn}>'>
          <input type='hidden' name='return' value='<{$smarty.session.return_url}>'>
        </div>
      </form>
    </div>
  </div>
<{/if}>