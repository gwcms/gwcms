{extends file="default_list.tpl"}


{block name="init"}
	
	{$curdate=date('Y-m-d')}
	
	{function name=dl_prepare_item}
	
		{if $item->remind_date < $curdate}
			{if $item->remind_snooze_until <= $curdate}
				{$item->set('row_class', "row_red")}
			{else}
				{$item->set('row_class', "row_blue")}
			{/if}
			
			
		{/if}
	{/function}	
				
	
	{function name=do_toolbar_buttons_modactions} 
		{toolbar_button title="Priminimų siuntimas" href=$m->buildUri(false,[act=>doPeriodEndNotifications])  iconclass="fa fa-refresh"}
	{/function}	
	
	{$do_toolbar_buttons_hidden=[modactions]}
	{$do_toolbar_buttons[]=dialogconf}
	{$do_toolbar_buttons[]=hidden}
	{$do_toolbar_buttons[]=search}

	{$dl_output_filters.insert_time=short_time}
	{$dl_output_filters.update_time=short_time}	
	

	{$dl_output_filters.from=short_time}	
	{$dl_output_filters.to=short_time}
	{$dl_output_filters.changetrack=changetrack}	
		
	
	
	
	{$dl_actions=[invert_active,edit,ext_actions]}

	
		

	{$dl_inline_edit=1}	
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}
	
		
	{*
	
	{function dl_cell_count}
		{$url=$m->buildUri("`$item->id`/classificators",[clean=>2])}
		
		{list_item_action_m href=$url 
			action_addclass="iframe-under-tr" caption="Items({$item->count})" 
			tag_params=["data-iframeopt"=>'{ "min-width":"1000px" }']}
	{/function}			
	*}
		

	
	
	
	{$dl_smart_fields=[type,user_title,user_id]}
	
	{function dl_cell_user_id}
		<a class="iframeopen" href="{$app->buildUri("customers/users/`$item->user_id`/form",[clean=>2,readonly=>1])}" title="Vartotojo info">{$item->user_id}</a>
	{/function}	
	
	{function dl_cell_user_title}
		<a class="iframeopen" href="{$app->buildUri("customers/users/`$item->user_id`/form",[clean=>2,readonly=>1])}" title="Vartotojo info">{$item->user->title} {$item->user->country}</a>
	{/function}	

	
{/block}


{block name="after_list"}


	<style>.row_red{ color:red }</style>
	<style>.row_blue{ color:blue }</style>

{/block}