<style>
	.dl_page_view_container .btn { padding: 3px 10px 3px 10px ; }
</style>


{function "page_view_a"}
	<a class="btn {if $m->list_config.pview->id==$view->id}btn-primary{elseif $m->list_config.pviews_and[$view->id]}btn-info{else}btn-default{/if} shiftbtn" 
		href="{$m->buildUri(false,[act=>doSetView,view_id=>$view->id])}"
		title="{if $view->title_short}{$view->title}{else}{$view->condition}{/if} {if $view->fields}[Stulpeliai]{/if} {if $view->condition}[Filtrai]{/if} {if $view->order}[Rikiavimas]{/if}"
		>
		{if $view->default}<i class="fa fa-home" title="{GW::l('/g/PAGE_VIEW_DEFAULT')}"></i>{/if} 
		{if $view->title_short}{$view->title_short}{else}{$view->title}{/if}{if $view->calculate} ({$view->count_result}){/if}</a>		
{/function}

{$views_visible=GW_Adm_Page_View::select2Display($views,false,"")}
{$views_drop=GW_Adm_Page_View::select2Display($views,true,"")}
{$orders_visible=GW_Adm_Page_View::select2Display($views,false,"order")}
{$orders_drop=GW_Adm_Page_View::select2Display($views,true,"order")}

{if $views_visible || $views_drop || $orders_visible || $orders_drop}

<div class="pad-ver mar-btm dl_page_view_container">
					
{if $views_visible || $views_drop}
<div class="btn-group">
    <div class="btn-group">
	<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">

	    <i class="fa fa-filter" aria-hidden="true" title="{GW::l('/g/VIEWS_LABEL')}"></i>
	</button>
	<ul class="dropdown-menu">
	    <li><a class="iframeopen" href="{$m->buildUri(false,[act=>doCreatePageView,clean=>2])}">{GW::l('/g/CREATE_NEW_VIEW')}</a></li>
	    {if $m->list_config.pview->id}
		<li><a class="iframeopen" href="{$m->buildUri(false,[act=>doCreatePageView,update=>1,clean=>2])}">{GW::l('/g/UPDATE_CURRENT_VIEW')}</a></li>
	    {/if}
	    <li><a class="iframeopen" href="{$m->buildUri(false,[act=>doManagePageViews,clean=>2])}" title="{GW::l('/M/SYSTEM/MAP/childs/page_views/title')}">{GW::l('/g/MANAGE_PVIEWS')}</a></li>
	</ul>
    </div>	
	
	{foreach $views_visible as $view}
		{call "page_view_a"}
	{/foreach}

	{if $views_drop}
    <div class="btn-group">
	<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">

	    <i class="dropdown-caret"></i>
	</button>
	<ul class="dropdown-menu">
		{foreach $views_drop as $view}
			 <li>{call "page_view_a"}</li>
		{/foreach}		
	</ul>
    </div>
	{/if}
</div>

{/if}


{if $orders_visible || $orders_drop}
<div class="btn-group">
    <div class="btn-group">
	<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">

	    <i class="fa fa-sort-amount-asc" aria-hidden="true" title="{GW::l('/g/VIEWS_LABEL')}"></i>
	</button>
	<ul class="dropdown-menu">
	    <li><a class="iframeopen" href="{$m->buildUri(false,[act=>doCreatePageView,clean=>2,saveasorder=>1])}">{GW::l('/g/CREATE_NEW_ORDER')}</a></li>
	    <li><a class="iframeopen" href="{$m->buildUri(false,[act=>doManagePageViews,clean=>2])}" title="{GW::l('/M/SYSTEM/MAP/childs/page_views/title')}">{GW::l('/g/MANAGE_PVIEWS')}</a></li>
	</ul>
    </div>	
	
	{foreach $orders_visible as $view}
		{call "page_view_a"}
	{/foreach}

	{if $orders_drop}
    <div class="btn-group">
	<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">

	    <i class="dropdown-caret"></i>
	</button>
	<ul class="dropdown-menu">
		{foreach $orders_drop as $view}
			 <li>{call "page_view_a"}</li>
		{/foreach}		
	</ul>
    </div>
	{/if}
</div>

{/if}



</div>

{/if}


{capture append=footer_hidden}	
	<script>
		require(['gwcms'], function(){	gw_adm_sys.init_iframe_open(); })
	</script>		
{/capture}

