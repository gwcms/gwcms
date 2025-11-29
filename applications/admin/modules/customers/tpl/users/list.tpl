{extends file="default_list.tpl"}

{*
oneline example
<a href="#" onclick="var ss=window.prompt('Enter sim id 0-{$item->sim_num_slots}');if(ss)location.href='{gw_path do=switchsim params=[id=>$item->id,simid=>'']}'+ss;return false;" title="Switch sim">Sw</a>
*}

	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}
	
	{if $app->user->isRoot()}
		{capture append="dl_checklist_actions"}<option value="checked_action_postids('{$m->buildUri(false,[act=>doRealRemove])}', true)">Negražintinas šalinimas</option>{/capture}
	{/if}
	{capture append="dl_checklist_actions"}<option value="checked_action_postids('{$m->buildUri(false,[act=>doRecalc4user])}', true)">Taškų perskačiavimas</option>{/capture}
	

{block name="init"}

	{*function dl_actions_switchtouser}
		{gw_link do="switch_user" icon="switch_user" params=[id=>$item->id] show_title=0}
	{/function*}
	

{function name=do_toolbar_buttons_modactions} 

	{**}		
		
{/function}


	{$dl_smart_fields=[name,online,particcnt]}
	{$do_toolbar_buttons[] = dialogconf}	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[modactions]}	
	
	{$do_toolbar_buttons[] = search}
	
	{$dl_actions=[invert_active,edit,delete,ext_actions]}
	
	{$dl_output_filters.insert_time=short_time}
	{$dl_output_filters.update_time=short_time}
	{$dl_output_filters.clubObj=linkedobj_title}
	{$dl_output_filters.coachObj=linkedobj_title}
	{$dl_output_filters.changetrack=changetrack}	
	
	
	
	{function dl_actions_balance_log}
		{gw_link relative_path="`$item->id`/balancelog"  params=[id=>$item->id] title="BL"}
	{/function}		
	
	{$dl_output_filters.update_time=short_time}
	
	
	
	{function dl_cell_name}
                {$item->name} {$item->surname}
	{/function}
	

	{function dl_cell_insert_time}
                {$x=explode(' ',$item->insert_time)}
		{$x.0}
	{/function}
	
	
	{function name=dl_output_filters_linkedobj_title}
		
		{if $item->get({$field})->short}
			<span title="{$item->get({$field})->title}">{$item->get({$field})->short}</span>
		{else}
			{$item->get({$field})->title}
		{/if}
	{/function}		
	

	

{/block}


