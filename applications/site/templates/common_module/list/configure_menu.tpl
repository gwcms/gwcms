{include "list/actions.tpl"}
{$action_class=gwcmsLinksInDD}


{$addlitag=1}

{list_item_action_m url=[false, [act=>doCreatePageView,clean=>2]] iconclass="fa fa-floppy-o" title=GW::ln('/g/CREATE_NEW_VIEW') caption=GW::ln('/g/CREATE_NEW_VIEW') action_addclass="iframeopen"}

{if $m->list_config.pview->id}
	{list_item_action_m url=[false, [act=>doCreatePageView,update=>1,clean=>2]] iconclass="fa fa-refresh" title="{GW::ln('/g/UPDATE_CURRENT_VIEW')}" caption="{GW::ln('/g/UPDATE_CURRENT_VIEW')}" action_addclass="iframeopen"}
{/if}
{list_item_action_m url=[false, [act=>doManagePageViews,clean=>2]] iconclass="fa fa-pencil-square-o" title=GW::ln('/M/SYSTEM/MAP/childs/page_views/title') caption=GW::ln('/g/MANAGE') action_addclass="iframeopen"}

{list_item_action_m href=$m->buildUri(false,[print_view=>1],[carry_params=>1]) caption=GW::ln('/g/PRINT_VIEW') iconclass='fa fa-print'}


{list_item_action_m onclick="gwSearchReplace();return false" caption=GW::ln('/G/common_module/SEARCH_REPLACE') iconclass='fa fa-search'}


{if $app->user->isRoot()}
	{*Paslėptieji veiksmai*}
	<li id="gwlrootmenutr"><a class="gwcmsLinksInDD"  href="#" onclick="return false">
			<i class="fa fa-cog text-danger"></i> <span class="text-danger">RootActions</span></a>
	<li>

	{list_item_action_m url=[false, [act=>doresetListVars]] iconclass="fa fa-cog" caption="doResetListVars" action_addclass="rootactions"}
	{list_item_action_m url=[false, [act=>doCopyFieldData]] iconclass="fa fa-cog" caption="doCopyFieldData" action_addclass="rootactions"}
	{list_item_action_m url=[false, [act=>doAutoTranslate]] iconclass="fa fa-cog" caption="doAutoTranslate" action_addclass="rootactions"}
	{list_item_action_m url=[false, [act=>doMultiSetValue]] iconclass="fa fa-cog" caption="doMultiSetValue" action_addclass="rootactions"}
	
	{list_item_action_m url=[false, [act=>doManageFields,clean=>2]] iconclass="fa fa-pencil-square-o" 
		caption="Manage fields"
	}

	<script>
		require(['gwcms'], function(){
			$('#gwlrootmenutr').click(function(event){
				event.stopPropagation();
				$('.rootactions').fadeIn();
				$('#gwlrootmenutr').hide();
			})
			$('#gwlrootmenu').click(function(){
				$('.rootactions').fadeOut();
				$('#gwlrootmenutr').show();
			})
			$('.rootactions').hide();
		})
	</script>
	<style>
		.rootactions{ display:none;opacity:0.5;color:red !important; }
		#gwlrootmenutr{ opacity:0.1 }
	</style>
{/if}

{*{include "`$m->tpl_dir`/addlistconfig.tpl"}*}
