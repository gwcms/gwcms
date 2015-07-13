{if count($breadcrumbs)}
	<div id="breadcrumbs">
	{foreach $breadcrumbs as $item}
		{if $item@last}
			{$item.title}
		{else}
			<a href="{$ln}/{$item.path}">{$item.title}</a> &raquo;
		{/if}	
	{/foreach}
	
	{if count($breadcrumbs_attach)}
		::
		{foreach $breadcrumbs_attach as $item}
			<a href="{$item.path}">{$item.title}</a> {if !$item@last}&raquo;{/if}	
		{/foreach}		
	{/if}
	
	</div>
{/if}

