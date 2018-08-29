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
						
	
						<div class="btn-group dropright gwcmsAction" style="display: inline">

							<i class="fa fa-bars dropdown-toggle dropdown-toggle-icon gwcms-ajax-dd" data-toggle="dropdown" data-url="{$item.actions}"></i>	
							    <ul class="dropdown-menu dropdown-menu-right">
								<li><i class="fa fa-spinner fa-pulse"></i></li>
							    </ul>
						</div>									    
						
						{if !isset($GLOBALS.dropdown_init_done)}
							{$GLOBALS.dropdown_init_done=1}
							<script type="text/javascript">
								require(['gwcms'], function(){
								gwcms.initDropdowns();
							});
							</script>
						{/if}						
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

