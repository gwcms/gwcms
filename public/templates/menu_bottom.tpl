{*
{function menu_item}
	{$active=$page->id==$item->id}

	<li><a href="{$request->buildUri($item->path)}"
		style="{if $active}font-weight:bold;{/if}{if $red}color:red{/if}">{$item->get(title)}
		</a>
	</li>
{/function}

<ul class="footer_menu">

	{if GW::$user}		
			{$tmp=GW::getInstance('GW_Page')->getByPath('userzone')} 
	
			{foreach from=$tmp->getChilds([in_menu=>1]) item=item key=key}
				{menu_item red=1}
			{/foreach}
	{else}
		{$item=GW::getInstance('GW_Page')->getByPath('b/users')} 
		{menu_item red=0}
	{/if}

	{$tmp=GW::getInstance('GW_Page')->getByPath('b')} 
	
	{foreach from=$tmp->getChilds([in_menu=>1]) item=item key=key}

		{menu_item}

	{/foreach}
</ul>
*}
{*
<ul class="footer_menu">
<li>
<a>kontaktai</a>
</li>
<li>
<a>Apie mus</a>
</li>
<li>
<a>Nuolaidų politika</a>
</li>
<li>
<a>Pristatymas ir grąžinimas</a>
</li>
</ul>
*}