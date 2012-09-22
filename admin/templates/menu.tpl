<div id="firstpane" class="menu_list"> <!--Code for menu starts here-->



{foreach from=GW::getInstance('GW_ADM_Page')->getChilds() item=item key=key}

	{$active=($request->path_arr.0.path_clean == $item->pathname)}
	{$childs=$item->getChilds()}

	<p class="menu_head{if $active} menu_head_active{/if} {if !count($childs)}no_childs{/if}">
		<a href="{$request->buildUri($item->path)}" >{$item->get(title,$ln)}</a>
	</p>

	{if count($childs)}
	<div class="menu_body {if $active} menu_body_active{/if}">
			{foreach from=$childs item=item}
					<a {if $request->path_arr.1.path_clean == $item->path}class="current"{/if} href="{$request->buildUri($item->path)}">{$item->get(title,$ln)}</a>
			{/foreach}
	</div>
	{/if}

{/foreach}

</div>

