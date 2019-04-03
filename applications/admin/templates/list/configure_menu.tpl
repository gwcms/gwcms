{include "list/actions.tpl"}
{$action_class=gwcmsLinksInDD}


{$addlitag=1}

{list_item_action_m url=[false, [act=>doCreatePageView,clean=>2]] iconclass="fa fa-floppy-o" title=GW::l('/g/CREATE_NEW_VIEW') caption=GW::l('/g/CREATE_NEW_VIEW') action_addclass="iframeopen"}

{if $m->list_config.pview->id}
	{list_item_action_m url=[false, [act=>doCreatePageView,update=>1,clean=>2]] iconclass="fa fa-refresh" title="{GW::l('/g/UPDATE_CURRENT_VIEW')}" caption="{GW::l('/g/UPDATE_CURRENT_VIEW')}" action_addclass="iframeopen"}
{/if}
{list_item_action_m url=[false, [act=>doManagePageViews,clean=>2]] iconclass="fa fa-pencil-square-o" title=GW::l('/M/SYSTEM/MAP/childs/page_views/title') caption=GW::l('/g/MANAGE') action_addclass="iframeopen"}

{list_item_action_m href=$m->buildUri(false,[print_view=>1],[carry_params=>1]) caption=$lang.PRINT_VIEW iconclass='fa fa-print'}


{*{include "`$m->tpl_dir`/addlistconfig.tpl"}*}
