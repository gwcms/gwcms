

<ul class="sf-menu" style="clear:none">
	{foreach $lang_products.CATEGORY_OPT as $key => $title}
		<li><a href="{$ln}/padangos?category={$key}">{$title}</a></li>
	{/foreach} 
	
	{$tmp=GW::getInstance('GW_Page')->getByPath('a')} {foreach from=$tmp->getChilds([in_menu=>1]) item=item key=key}

	{$active=$page->id==$item->id} {*$childs=$item->getChilds()*}

	<li>
		<a href="{$request->buildUri($item->path)}" {if $active}class="active"{/if} >{$item->get(title)}</a>
	</li>
	
	{/foreach}
	
	
</ul>

<ul class="sf-menu langmenu">
	<li><a href="lt" {if $ln=='lt'}class="active"{/if}>LT</a></li>
	<li><a class="sep">|</a></li> 
	<li><a href="ru" {if $ln=='ru'}class="active"{/if}>RU</a></div></li>
</ul>