{extends file="default_list.tpl"}


{block name="init"}

	
	{function name=do_toolbar_buttons_synchronizefromxml} 
		{toolbar_button title=GW::l('/A/VIEWS/synchronizefromxml') iconclass='gwico-Refresh' href=$m->buildUri(synchronizefromxml)}	

	{/function}	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}
	
	{$do_toolbar_buttons_hidden=[synchronizefromxml,exportdata,importdata,dialogconf,print]}		
	
	
	
	
		
	{$dl_actions=[edit,clone,delete,ext_actions]}
	
	{$dl_inline_edit=1}	


	{$dl_output_filters=[]}
	
	{$dl_output_filters_truncate_size=100}
	{$dl_output_filters.insert_time=short_time}
	{$dl_output_filters.update_time=short_time}	
	
	{foreach GW::$settings.LANGS as $lncode}
		{$dl_output_filters["value_`$lncode`"]=autotrans}
	{/foreach}	
	
	
	{function name=dl_output_filters_autotrans}
		{if trim($item->get($field))}
			{call "dl_output_filters_truncate"}
		{else}
			{$dest=str_replace('value_','', $field)}
			<div style="text-align:right"><a class='ajax-link' href="{$m->buildUri(false, [id=>$item->id,act=>doAutoTrans,dest=>$dest])}"><i class="fa fa-magic"></i></div>
		{/if}
	{/function}
	
	
	
	
	{$dl_checklist_enabled=1}
	{capture append="dl_checklist_actions"}<option value="checked_action('{$m->buildUri(false,[act=>doSeriesAct,action=>doSeriesTranslate,all=>1])}', 1)">{GW::l('/A/VIEWS/doSeriesTranslate')}</option>{/capture}		
	{$dl_cl_actions=[dialogremove]}	
	
{/block}


