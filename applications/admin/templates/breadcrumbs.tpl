{if count($breadcrumbs)}
	{if !$nobreadcrumbscontainer}<ol class="breadcrumb gwbreadrumb">{/if}
	{foreach $breadcrumbs as $item}
		{if $item@last}
			<li class="active">{$item.title|escape}</li>
		{else}
			{if !$smarty.get.print_view}
				<li>
					<a href="{$app->buildURI($item.path)}">{$item.title|escape}</a>
					{if $item.actions}
						
						{include "tools/ajaxdropdown.tpl"}
												
					{/if}
					</a>
				</li>
			{else}
				{$item.title|escape} &raquo;
			{/if}
		{/if}	
	{/foreach}
	
	{if $breadcrumbs_attach}
		::
		{foreach $breadcrumbs_attach as $item}
			{if !$smarty.get.print_view}
				<a href="{$item.path}">{$item.title|escape}</a> 
			{else}
				{$item.title|escape} &raquo;
			{/if}
			
			{if !$item@last}&raquo;{/if}	
		{/foreach}		
	{/if}
	
	{if !$nobreadcrumbscontainer}</ol>{/if}
{/if}

{*
                <!--Breadcrumb-->
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <ol class="breadcrumb">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Library</a></li>
                    <li class="active">Data</li>
                </ol>
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <!--End breadcrumb-->	
				
*}

