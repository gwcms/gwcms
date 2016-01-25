{if count($breadcrumbs)}
	{if !$nobreadcrumbscontainer}<div id="breadcrumbs">{/if}
	{foreach $breadcrumbs as $item}
		{if $item@last}
			{$item.title}
		{else}
			{if !$smarty.get.print_view}
			<a href="{$app->buildURI($item.path)}">{$item.title}</a> &raquo;
			{else}
				{$item.title} &raquo;
			{/if}
		{/if}	
	{/foreach}
	
	{if count($breadcrumbs_attach)}
		::
		{foreach $breadcrumbs_attach as $item}
			{if !$smarty.get.print_view}
				<a href="{$item.path}">{$item.title}</a> 
			{else}
				{$item.title} &raquo;
			{/if}
			
			{if !$item@last}&raquo;{/if}	
		{/foreach}		
	{/if}
	
	{if !$nobreadcrumbscontainer}</div>{/if}
{/if}

