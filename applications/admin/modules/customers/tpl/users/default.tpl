{extends file="default_list.tpl"}

{block name="after_list"}
	{*ant newsletter geresnis sprendimas*}
<script type="text/javascript">function addcredit(id, val){ if(!val) return false; $('#addcredit_id').val(id); $('#addcredit_val').val(val); $('#addcredit').submit() }</script>
<form id="addcredit"><input type="hidden" id="addcredit_id" name="id" ><input type="hidden" name="act" value="do:addCredit" /><input type="hidden" id="addcredit_val" name="addcredit"/></form>
{/block}

{block name="init"}

	{*function dl_actions_switchtouser}
		{gw_link do="switch_user" icon="switch_user" params=[id=>$item->id] show_title=0}
	{/function*}
	
	{$display_fields = [
		id=>1,
		username=>1,
		name=>1,
                email=>1,
		phone=>1,
		sms_pricing_plan=>1,
		sms_allow_credit=>1,
		sms_funds=>1,
		insert_time=>0,
		update_time=>0
	]}
	
	{function dl_actions_add}
		<a href="#" onclick="addcredit({$item->id},window.prompt('Papildyti sąsk.'));return false" title="Add funds">A</a>
	{/function}
	
	{function dl_actions_balance_log}

		{gw_link relative_path="`$item->id`/balancelog"  params=[id=>$item->id] title="BL"}
	{/function}		
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	{function dl_cell_name}
                {$item->name} {$item->surname}
	{/function}
	
	{function dl_cell_insert_time}
                {$x=explode(' ',$item->insert_time)}
		{$x.0}
	{/function}
	
	{$dl_smart_fields=[name, insert_time]}
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[add, balance_log,invert_active,edit,delete]}
	
	{$dl_filters=$display_fields}
	
	{$order_enabled_fields = array_keys($display_fields)}
{/block}
