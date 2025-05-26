{extends file="default_list.tpl"}

{block name="init"}
	

	{function name=do_toolbar_buttons_log}

		{toolbar_button href=$app->buildUri('system/logwatch/entire',[id=>'ticket_import.log']) title="Importavimo žurnalas" iconclass="gwico-Console" btnclass="iframeopen"}
	{/function}		


	{function name=do_toolbar_buttons_actions}
		{call name="do_toolbar_buttons_dropdown" do_toolbar_buttons_drop=$do_toolbar_buttons_actions groupiconclass="fa fa-angle-down" grouptitle="Veiksmai"}
	{/function}	

	{function name=do_toolbar_buttons_doscanimap}
		{toolbar_button title="Skaityti iš pašto dėžutės" iconclass="fa fa-cog" href=$m->buildUri(false,[act=>doScanMail])}

	{/function}


	{$do_toolbar_buttons_actions[]=doscanimap}

	{$do_toolbar_buttons[]=actions}	

	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[dialogconf,print,log]}	
	{$do_toolbar_buttons_hidden[] = dialogconf2}	
	{$do_toolbar_buttons[] = search}
	
	{if $m->filters}
		{$do_toolbar_buttons=[]}
		{$dl_filters=[]}
	{/if}
	

	{function dl_cell_reservation_code}
		<a href="{$app->buildUri('travel/trips',[act=>doFindByReservCode,code=>$item->reservation_code])}">{$item->reservation_code}</a>	
	{/function}
	
	{function dl_cell_attach_list}
		{foreach $item->attach_list as $attach}
			<a href="{$m->buildUri(false,[act=>doGetAttachment,id=>$item->id,file=>$attach->filename])}" target="_blank">
				<i title="{$attach->filename}" class="{Mime_Type_Helper::icon($attach->filename)}"></i>
			</a>
		{/foreach}
	{/function}
	{function name=dl_cell_changetrack}
		{$tmp=$item->extensions.changetrack->count()}	
		{if $tmp}
			
			<a class='badge bg-bro iframe-under-tr' href="{$app->buildUri("datasources/changetrack",[owner_id=>$item->id,owner_type=>$item->ownerkey,clean=>2])}">{$tmp}</a>
		{else}{/if}
		
	{/function}	
	
	
	{function name=dl_cell_data}
		
		
		{foreach $item->data as $key => $val}
			{if $val}
				<span style="color:blue" title="{json_encode($val)|escape}">{$key}</span>
			{/if}
		{/foreach}
	{/function}		
	


	{$dl_checklist_enabled=1}
	{*invertactive,*}
	{$dl_cl_actions=[dialogremove,doparse]}
	{$dl_smart_fields=[attach_list,data,reservation_code,changetrack]}

	{$dl_actions=[edit,ext_actions]}	



	{$dl_output_filters=[insert_time=>short_time, update_time=>short_time, orig_time=>short_time, departure_time=>short_time]}	
	
	
	{capture append=footer_hidden}	
		
		<script>
			require(['gwcms'], function(){				
				gw_adm_sys.init_iframe_open();
			})
		</script>		
	{/capture}
	
	{function dl_cl_actions_doparse}
		<option value="checked_action('{$m->buildUri(false,[act=>doSeriesAct,action=>doParse])}', 1)">{GW::l('/A/VIEWS/doParse')}</option>
	{/function}	
	
	
{/block}


	{block name="after_list"}
		{if !$m->filters}
			<br />
			<small style="color:silver" title="Last mailbox read">Last mailbox read: {$m->config->last_mailbox_read}</small>
		{/if}
		
	{/block}	