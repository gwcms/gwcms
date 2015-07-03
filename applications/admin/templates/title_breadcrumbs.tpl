
{*
{if is_array($breadcrumbs_attach)}
	{$breadcrumbs=array_merge($breadcrumbs, $breadcrumbs_attach)}
{/if}
*}

{if $breadcrumbs.0.title_clean==$breadcrumbs.1.title_clean}
	{gw_unassign var=$breadcrumbs.0} 
{/if}

{if count($breadcrumbs)}
	{foreach $breadcrumbs as $item}
		{$item.title}{if !$item@last} / {/if}
	{/foreach}
	
	{if count($breadcrumbs_attach)}
		::
		{foreach $breadcrumbs_attach as $item}
			{$item.title} {if !$item@last}&raquo;{/if}	
		{/foreach}
	{/if}
		
{/if}

