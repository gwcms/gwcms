

<ul class="sf-menu" style="clear:none">
	
	{$tmp=GW::getInstance('GW_Page')->getByPath('usr')} {foreach from=$tmp->getChilds([in_menu=>1]) item=item key=key}

	{$active=$page->id==$item->id} {*$childs=$item->getChilds()*}

	<li>
		<a href="{$request->buildUri($item->path)}" {if $active}class="active"{/if} >{$item->get(title)}</a>
	</li>
	
	{/foreach}
	
	
</ul>
