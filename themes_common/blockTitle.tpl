	<{if $block.title|regex_replace:"/.*\[pic\].*/":"Picture True" == "Picture True"}>
      <img src="<{if $block.title|regex_replace:"/.*http.*/":"url" != "url"}><{$xoops_imageurl}><{/if}><{$block.title|regex_replace:"/.*\[pic\]/":""}>" alt="<{$block.title|regex_replace:"/\[pic\].*/":""}>" title="<{$block.title|regex_replace:"/\[pic\].*/":""}>" align="absmiddle" hspace=2 class="img-responsive">
	<{else}>
    <{if $block.title|regex_replace:"/.*\[icon\].*/":"Icon True" == "Icon True"}>
      <img src="<{if $block.title|regex_replace:"/.*http.*/":"url" != "url"}><{$xoops_imageurl}><{/if}><{$block.title|regex_replace:"/.*\[icon\]/":""}>" alt="<{$block.title|regex_replace:"/\[icon\].*/":""}>" title="<{$block.title|regex_replace:"/\[icon\].*/":""}>" align="absmiddle" hspace=2 />
      <{$block.title|regex_replace:"/\[icon\].*/":""}>
    <{elseif $block.title|regex_replace:"/.*\[link\].*/":"Link True" == "Link True"}>
      <a href="<{$block.title|regex_replace:"/.*\[link\]/":""}>" alt="<{$block.title|regex_replace:"/\[link\].*/":""}>" title="<{$block.title|regex_replace:"/\[link\].*/":""}>"><{$block.title|regex_replace:"/\[link\].*/":""}></a>
    <{else}>
      <{$block.title}>
    <{/if}>
	<{/if}>
