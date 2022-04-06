{extends file="default_list.tpl"}



{block name="init"}

{if $m->feat(itax)}
	{include "`$smarty.current_dir`/itax_stat.tpl"}	
{/if}
	
	{$dl_inline_edit=1}
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}


	{function name=do_toolbar_buttons_orderacts} 		
		{*
		{toolbar_button iconclass="fa fa-money"
				title=GW::ln('/m/VIEWS/doCreateInvoices')
				href=$m->buildUri(false, [act=>doCreateInvoices])}
		*}
		{toolbar_button iconclass="fa fa-money"
				title=GW::l('/m/VIEWS/paymentsummary')
				href=$m->buildUri(paymentsummary)}
				
				
		{if $m->feat(rivile)}
			{toolbar_button iconclass="fa fa-money"
				title="Eksportas į 'Rivilė' sistemą"
				href=$m->buildUri(false,[act=>doRivileExport])}
		{/if}				
	{/function}
	

	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,dialogconf2,print,orderacts]}		
	
	
	{$display_fields=['tour_part_id'=>0]}
	
	{$dl_smart_fields=[user_title,relations,user_id,admin_id,status,pay_type,itax_status_ex,delivery_opt]}

	
	{$dl_actions=[preview,items,invoice,editshift,ext_actions]}
	{$dl_group_list_by=['tour_part_id']}
	
	
	{function dl_actions_items}
		
		<a title="Cart items {$item->items_count}" class='gwcmsAction iframe-under-tr' href="{$m->buildUri("`$item->id`/orderitems", [clean=>2])}">
			<i class="fa fa-shopping-basket"></i> <span style='color:red;position:relative;left:-6px'>{$item->itmcnt}</span>
		</a>	
		
	{/function}	

	{function dl_prepare_item}
		{if !$item->approved}{$item->set(row_class,notapproved)}{/if}
	{/function}
	
		
	

	{function dl_cell_user_title}
		<a class="iframeopen" href="{$app->buildUri("customers/users/`$item->user_id`/form",[clean=>2,readonly=>1])}" title="Vartotojo info">{$options.user_id[$item->user_id]->title}</a>
	{/function}	
	{function dl_cell_user_id}
		<a class="iframeopen" href="{$app->buildUri("customers/users/`$item->user_id`/form",[clean=>2,readonly=>1])}" title="Vartotojo info">{$item->user_id}</a>
	{/function}
	{function dl_cell_admin_id}
		<a class="iframeopen" href="{$app->buildUri("users/usr/`$item->admin_id`/form",[clean=>2])}" title="Admin info">{$item->admin_id}</a>
	{/function}		
	

	{function dl_actions_invoice}
		{if $item->isdir==0}
			{list_item_action_m 
				url=[invoice,[id=>$item->id,clean=>1]] iconclass="fa fa-file-o" action_addclass="iframe-under-tr"
				tag_params=['data-iframeopt'=>'{"width":"1000px","height":"600px"}']
			}
		{/if}
	{/function}
	{function name=dl_actions_preview}
		<a class='iframe-under-tr' href="{$m->buildUri("oitems",[id=>$item->id,clean=>2])}"><i class="fa fa-search"></i></a>
	{/function}
	
	

	{capture append=footer_hidden}	
		
	
	{/capture}	


	{$dl_checklist_enabled=1}
	{capture append="dl_checklist_actions"}<option value="checked_action('dialogremove')">{GW::l('/A/VIEWS/dialogremove')}</option>{/capture}	
	
	
	{function dl_cell_itax_status_ex}
		{if $m->feat(itax)}
			{call "itax_status"}
		{/if}
	{/function}
	
	{function dl_cell_status}
		{if $item->status}
			{GW::ln("/M/orders/status/`$item->status`")}
		{else}
			-
		{/if}
	{/function}
	
	{function dl_cell_pay_type}
		{if $item->pay_type}
			{GW::ln("/M/orders/PAY_METHOD_{$item->pay_type|strtoupper}")} {if $item->pay_subtype}/ {$item->pay_subtype_human}{/if}
		{else}
			-
		{/if}
	{/function}	
	
	{function dl_cell_delivery_opt}
		{if $item->delivery_opt==1}
			<i class="fa fa-truck" style="color:darkgreen" title="{GW::ln('/M/orders/DELIVERY_1')}"></i>
		{elseif $item->delivery_opt==2}
			<i class="fa fa-truck" style="color:silver" title="{GW::ln('/M/orders/DELIVERY_2')}"></i>
		{elseif $item->delivery_opt==3}
			<span title="{GW::ln('/M/orders/DELIVERY_3')}">@</span>
		{/if}
	{/function}		
	
	{$dl_output_filters.insert_time=short_time}
	{$dl_output_filters.update_time=short_time}	
	{$dl_output_filters.pay_time=short_time}	
	{$dl_output_filters.placed_time=short_time}	
	{$dl_output_filters.changetrack=changetrack}
	
{/block}


