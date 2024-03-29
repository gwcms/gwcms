{function toolbar_button}
	<a class="{if $indropdown}gwtoolbarbtn{else}gwtoolbarbtn btn btn-{$btnnormal|default:'primary'} btn-active-{$btnactive|default:'primary'}{/if} {$btnclass}" 
	   {if $toggle}data-toggle="button" aria-pressed="false"{/if} 
	   {*2018-10 outdate if $query_param}onclick="var ss=window.prompt('{$query_param}');if(ss)location.href=this.href+ss;return false;"{/if*}
	   {if $query_param}onclick="var ss=window.prompt('{$query_param.1}');if(ss)location.href=gw_navigator.url(this.href, { '{$query_param.0}': ss  });return false;"{/if}
	   {*dar gali but toks: this.href=gw_navigator.url*}
	   {foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
	   {if $confirm}{$app->fh()->gw_link_confirm()}{/if}
	   {if $onclick}onclick="{$onclick};return false"{/if} href="{$href|default:'#'}"
	   {if $newwindow}target="_blank"{/if}>{if $iconhtml}{$iconhtml}{/if}{if $iconclass}<i class="{$iconclass}"></i>{/if} <span>{$title}</span>
		
	</a>

	{if strpos($btnclass, 'iframeopen')!==false}
		<script>require(['gwcms'], function(){ gw_adm_sys.init_iframe_open(); }) </script>
	{/if}
{/function}



{function name=do_toolbar_buttons_addnew}
	{toolbar_button title=GW::ln('/g/CREATE_NEW') iconclass='gwico-Plus' href=$m->buildUri('form',[id=>0])}
	
{/function}

{function name=do_toolbar_buttons_filters}
		{*toolbar_button title=GW::ln('/g/SEARCH') iconclass='gwico-SearchFilled' onclick="$('#filters').toggle();" toggle=1*}
{/function}	

{function name=do_toolbar_buttons_print}
	
	{toolbar_button title=GW::ln('/g/PRINT_VIEW') iconclass='gwico-Print-Filled' href=$m->buildUri(false,[print_view=>1],[carry_params=>1])}
{/function}


{function name=do_toolbar_buttons_info}
	{if $page->notes}
		{toolbar_button title="`GW::ln('/g/ABOUT')` `$page->title`" iconclass='gwico-Info' href=$app->buildUri('system/modules',[act=>doGet_Notes,path=>$app->path]) btnclass="iframeopen"}	
	{/if}
{/function}

{function name=do_toolbar_buttons_modinfo}
		{toolbar_button title="`GW::ln('/g/ABOUT')` `$page->title`" iconclass='gwico-Info' href=$m->buildUri('modinfo',[clean=>2]) btnclass="iframeopen"}
{/function}

{function name=do_toolbar_buttons_dialogconf}
	{toolbar_button title=GW::ln('/g/LIST_DISPLAY_SETTINGS') iconclass='gwico-Vertical-Settings-Mixer' onclick="lds_config(this);" }
	{capture append="footer_hidden"}
	<script type="text/javascript">
		function lds_config(obj)
		{
			gwcms.open_dialog2({ url: '{$m->buildURI('dialogconfig',[listpar_updatetime=>$m->list_params.updatetime])}', iframe:1, title:$(obj).text() })
		}
	</script>
	{/capture}
{/function}	

{function name=do_toolbar_buttons_dialogconf2}
	{toolbar_button title=GW::ln('/g/VIEWS/dialogconfig2') 
		iconclass='gwico-Horizontal-Settings-Mixer-Filled' 
		tag_params=["data-dialog-minheight"=>$dlgCfg2MWdth|default:200]
		btnclass="iframeopen" href=$m->buildURI('dialogconfig2',[dialog_iframe=>1,clean=>2,listpar_updatetime=>$m->list_params.updatetime])}
{/function}	

{function name=do_toolbar_buttons_addinlist}
	{toolbar_button title=GW::ln('/g/CREATE_NEW') iconclass='gwico-Plus' onclick="gwToogleAdd($(this).hasClass('active'));" toggle=1}

	{capture append="footer_hidden"}
	<script type="text/javascript">
		function gwToogleAdd(active){

			if($('#iframebeforelist').length == 0){
				$('#additemscontainer').html('<iframe id="iframebeforelist" src="" style="width:800px;margin-top:5px;margin-bottom:20px;" frameborder="0"></iframe>');
				
				$('#iframebeforelist').contents().find('body').html('Loading...');
				
			}

			$('#additemscontainer').toggle();

			if(!active){
				gwcms.initAutoresizeIframe('#iframebeforelist', false, function(){
					$('#iframebeforelist').attr('src','{$m->buildUri('form',[id=>0,clean=>1,'RETURN_TO'=>$m->buildUri('iframeclose')])}');
				});
			}
			
			window.iframeClose = function(){
				$('#additemscontainer').hide();
				location.href = location.href;
			}
			
		}
	</script>
	{/capture}
{/function}

{function name=do_toolbar_buttons_dropdown}

	
						<div class="btn-group">
						
					                            <a type="button" data-toggle="dropdown" class="gwtoolbarbtn btn btn-default btn-active-dark dropdown-toggle dropdown-toggle-icon">
					                                <i class="{$groupiconclass}"></i> {if $grouptitle}<span>{$grouptitle}</span>{/if}
					                            </a>
					                            <ul class="dropdown-menu">
									    {if is_array($do_toolbar_buttons_drop)}
			{foreach $do_toolbar_buttons_drop as $button_func}
				{if $button_func=='divider'}
					<li class="divider"></li>
				{else}
					<li >{call name="do_toolbar_buttons_`$button_func`" indropdown=1}</li>
				{/if}
			{/foreach}										    
									{else}
										<li >{$do_toolbar_buttons_drop}</li>
									{/if}

					                            </ul>
					                        </div>	
	
	
{/function}

{function name=do_toolbar_buttons_search}	
	<div class="searchbox">
		<div class="input-group custom-search-form setListParamsContainer">
			<input type="text" id="quicksearch" class="setListParams form-control" placeholder="{GW::ln('/g/QUICK_SEARCH')}" name="list_params[search]" value="{$m->list_params.search|escape}">
			<span class="input-group-btn">
                                <button class="text-muted" type="button" onclick="$('#quicksearch').trigger('submit')"><i class="fa fa-search"></i></button>
			</span>
		</div>
	</div>	
{/function}


{function name=do_toolbar_buttons_hidden}
	{call name="do_toolbar_buttons_dropdown" do_toolbar_buttons_drop=$do_toolbar_buttons_hidden groupiconclass="gwico-Menu-Filled"}
{/function}

{function name=do_toolbar_buttons_importdata} 
	{toolbar_button title=GW::ln('/A/VIEWS/importdata') iconclass='gwico-Import' href=$m->buildUri(importdata)}
	
{/function}	
{function name=do_toolbar_buttons_exportdata}	
	{toolbar_button title=GW::ln('/A/VIEWS/exportdata') iconclass='gwico-Export' href=$m->buildUri(exportdata)}
	
{/function}

{function name=do_toolbar_buttons_edit} 
	{toolbar_button title=GW::ln('/A/VIEWS/edit') iconclass='gwico-Vertical-Settings-Mixer' href=$m->buildUri(edit)}
{/function}

{function name=do_toolbar_buttons_rtlog}
	{toolbar_button href=$app->buildUri(false, [act=>doShowLogFile]) title="Foninių veiksmų žurnalas" iconclass="gwico-Console"}
{/function}		



{function name=do_display_toolbar_buttons}
	{if $do_toolbar_buttons}
		<div class="btn-group mar-rgt">
		{foreach $do_toolbar_buttons as $button_func}
			{call name="do_toolbar_buttons_`$button_func`"}
		{/foreach}	
		</div>
		{assign var=gw_toolbar_show value=1 scope=global}
	{/if}
{/function}


{function name=do_display_toolbar_buttons2}
	{if !$smarty.get.print_view}



		{capture assign="tmp"}
			{call do_display_toolbar_buttons}


			{if $do_display_toolbar_pull_right}
				<div class="pull-right">
				{foreach $do_display_toolbar_pull_right as $include}
					{call gwinclude}
				{/foreach}		
				</div>
				{assign var=gw_toolbar_show value=1 scope=global}
			{/if}
		{/capture}

		{if $gw_toolbar_show}
		<div class="row gwtoolbarcont">{$tmp}</div>
		{/if}



		{*if $m->list_params.paging_enabled && count($list)}
		<td	align="right" width="1%">
		{include file="list/page_by.tpl"}
		</td>
		{/if*}

	{/if}
{/function}
