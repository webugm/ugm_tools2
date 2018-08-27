<style>
  .toolbar_bootstrap_nav {
    position: relative;
    margin: 0;
  }
  .toolbar_bootstrap_nav ul {
    margin: 0;
    padding: 0;
  }
  .toolbar_bootstrap_nav li {
    /*margin: 0 5px 10px 0;*/
    padding: 0;
    list-style: none;
    display: inline-block;
  }
  .toolbar_bootstrap_nav a {
    /*padding: 3px 6px;*/
    text-decoration: none;
    color: #999;
    line-height: 100%;
  }
  .toolbar_bootstrap_nav a:hover {
    color: #000;
  }
  .toolbar_bootstrap_nav .current a {
    background: #999;
    color: #fff;
    border-radius: 5px;
  }
</style>
<div class="row">
  <div class="col-sm-12">
    <nav class="toolbar_bootstrap_nav">
      <ul>
        <{foreach from=$moduleMenu item=menu}>          
          <li><a href="<{$menu.url}>" title="<{$menu.title}>"><{if $menu.icon}><i class="fa <{$menu.icon}>"></i><{/if}></a></li>
        <{/foreach}>
      </ul>
    </nav>
  </div>
</div>