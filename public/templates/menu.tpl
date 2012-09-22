<ul>

{foreach from=GW::getInstance('GW_Page')->getChilds() item=item key=key}

	{$active=($request->path_arr.0.path_clean == $item->pathname)}
	{*$childs=$item->getChilds()*}

	<li>
		<a href="{$request->buildUri($item->path)}" >{$item->get(title)}</a>
	</li>

	{*if count($childs)}
	<div class="menu_body {if $active} menu_body_active{/if}">
			{foreach from=$childs item=item}
					<a {if $request->path_arr.1.path_clean == $item->path}class="current"{/if} href="{$request->buildUri($item->path)}">{$item->get(title,$ln)}</a>
			{/foreach}
	</div>
	{/if*}

{/foreach}

</ul>
