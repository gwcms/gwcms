
{if $app->page->isFrontendContentEditingEnabled()}
        <style>
                .lnresulthighl{
			background-color: brown !important;
			color: white !important;
		}
                .transover{
			background-color: blue !important;
		}
        </style>
        <script>
		var gw_lang_results_active = {intval($app->sess['lang-results-active'])};
		var gw_ln = "{$app->ln}";
		var gw_page_content_fields = {json_encode($app->page->getFrontendContentFields())};
        </script>
	<script src="{$app_root}assets/js/admin.js?v={GW::$globals.version_short}"></script>
	<link href="{$app_root}assets/css/admin.css?v={GW::$globals.version_short}" rel="stylesheet" />
	<script src="/vendor/ckeditor422/ckeditor.js"></script>





{function name=display_site}
	{$image=$site->favico}
	{if $image}
		<img src='{$app->sys_base}tools/imga/{$image->id}?size=20x20'> 
	{else}
		<i style='font-size:20px;color:silver' class='fa fa-sitemap'></i>
	{/if}
	
	<span class="alabel">{$site->title}</span>
{/function}
	
	
	 {$options.sites=GW_Site::singleton()->findAll('active=1',['key_field'=>'id'])}

	{$current=$options.sites[$app->site->id]}
		<div class="btn-group">

		<a type="button" data-toggle="dropdown" class="gwtoolbarbtn btn btn-default btn-active-dark dropdown-toggle dropdown-toggle-icon dropdown-menu-up" aria-expanded="false">
		    <i class="fa fa-angle-down"></i> 
		    
		  	
		    {if $current}
			   {call display_site site=$current} 
		    {else}
			     <i style='font-size:20px;color:silver' class='fa fa-sitemap'></i>
	            {/if}
		</a>
		<ul class="dropdown-menu">
			
		{foreach $options.sites as $id => $site}
		<li>
			<a class="gwtoolbarbtn " href="admin/{$ln}/system/tools?act=doSwitchSite&app=SITE&site_id={$site->id}&uri={rawurlencode('/')}">
				{call display_site}
			</a>
		</li>
		{/foreach}			
		</ul>
	    </div>



<div class="btn-group dropup adm-menu-wrap">
	<a href="#" class="g-ml-5 text-uppercase dropdown-toggle" data-toggle="dropdown" style="color:orange">
		[ADM]
	</a>

	{if GW::s(MULTISITE)}
		{*$mainurl="https://{GW::s(MAIN_HOST)}"*}
		{*
		{$hosts=array_flip(GW::s("MULTISITE_CFG/{GW::s(MULTISITE_DEFAULT)}/hosts"))}
		{$mainhost=$hosts[GW::s("PROJECT_ENVIRONMENT")]}
		*}
		{$mainurl=GW::s(SITE_URL)}
	{else}
		{$mainurl=""}
	{/if}
	
	<ul class="dropdown-menu">
		<li><a target="_blank" href="{$mainurl}/admin/{$ln}/system/tools?act=doDebugModeToggle&app=SITE&uri={rawurlencode($smarty.server.REQUEST_URI)}">Debug rėžimas</a></li>
		<li><a target="_blank" href="{$mainurl}/admin/{$ln}/sitemap/pages/{$app->page->id}/form?pid={$app->page->id}">adm edit {$app->page->title}</a></li>
		<li><a target="_blank" href="{$mainurl}/admin/{$ln}/system/tools?act=doSwitchEnvironment&uri={rawurlencode($smarty.server.REQUEST_URI)}">switch DEV-PROD</a></li>
		<li><a target="_blank" href="{$mainurl}/admin/{$ln}/system/tools?act=doSwitchEnvironmentTEST&uri={rawurlencode($smarty.server.REQUEST_URI)}">switch DEV-TEST</a></li>
		<li><a target="_blank" href="{$mainurl}/admin/lt/sitemap/blocks?filters_unset=0&act=do%3Aset_filters&filters%5Bct%5D%5Bsite_id%5D%5B%5D=IN&filters%5Bvals%5D%5Bsite_id%5D%5B%5D={$app->site->id}">Svetainės blokeliai</a></li>
		
		

		

</div> 
	</ul>
</div> 


{/if}


{if $smarty.get.admin_func_test}
	{d::ldump([
		user_admin=>$app->user && $app->user->is_admin, 
		office=>GW::s('DEVELOPER_PRESENT'),
		ip=>GW::ip()
	])}
{/if}
