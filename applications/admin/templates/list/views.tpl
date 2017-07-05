<style>
	.dl_page_view_container .btn { padding: 3px 10px 3px 10px ; }
</style>


{function "page_view_a"}
	<a class="btn {if $view->current}btn-primary{else}btn-default{/if}" 
		href="{$m->buildUri(false,[act=>doSetView,view_id=>$view->id],[carry_params=>1])}"
		title="{if $view->title_short}{$view->title}{else}{$view->conditions}{/if}"
		>{if $view->title_short}{$view->title_short}{else}{$view->title}{/if}{if $view->calculate} ({$view->count_result}){/if}</a>		
{/function}

{$views_visible=GW_Adm_Page_View::select2Display($views,false,"")}
{$views_drop=GW_Adm_Page_View::select2Display($views,true,"")}
{$orders_visible=GW_Adm_Page_View::select2Display($views,false,"order")}
{$orders_drop=GW_Adm_Page_View::select2Display($views,true,"order")}

{if $views_visible || $views_drop || $orders_visible || $orders_drop}

<div class="pad-ver mar-btm bord-top dl_page_view_container">
					
{if $views_visible || $views_drop}
<div class="btn-group">
    <div class="btn-group">
	<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">

	    <i class="fa fa-filter" aria-hidden="true" title="{$lang.VIEWS_LABEL}"></i>
	</button>
	<ul class="dropdown-menu">
	    <li><a class="iframeopen" href="{$m->buildUri(false,[act=>doCreatePageView,clean=>2])}">{GW::l('/g/CREATE_NEW_VIEW')}</a></li>
	    <li><a class="iframeopen" href="{$m->buildUri(false,[act=>doManagePageViews,clean=>2])}" title="{GW::l('/M/SYSTEM/MAP/childs/page_views/title')}">{GW::l('/g/MANAGE')}</a></li>
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

	    <i class="fa fa-sort-amount-asc" aria-hidden="true" title="{$lang.VIEWS_LABEL}"></i>
	</button>
	<ul class="dropdown-menu">
	    <li><a class="iframeopen" href="{$m->buildUri(false,[act=>doCreatePageView,clean=>2,saveasorder=>1])}">{GW::l('/g/CREATE_NEW_ORDER')}</a></li>
	    <li><a class="iframeopen" href="{$m->buildUri(false,[act=>doManagePageViews,clean=>2])}" title="{GW::l('/M/SYSTEM/MAP/childs/page_views/title')}">{GW::l('/g/MANAGE')}</a></li>
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

