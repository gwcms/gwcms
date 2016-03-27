{block name="before_open"}
		
{/block}

{block name="open_tpl"}
		{include file="default_open.tpl"}
{/block}

{*functions*}
	{function name=dl_cell_mark}
		<input type="checkbox" class="checklist_item" class="checklist" value="{$item->id}" />
	{/function}
	{function name=dl_custom_head_mark}
		<input type="checkbox" id="checklist_toggle" />
	{/function}
	
	
	{function name=truncate_hint}
		{if mb_strlen($value) > $length}
			<span title="{$value}">{$value|truncate:$length}</span>
		{else}
			{$value|truncate:$length}
		{/if}
	{/function}	
{*/functions*}



{$dl_toolbar_buttons=[addnew,filters,info]}

<div>
	{block name="init"}
		{$dl_fields=[title,insert_time,update_time]}
		{$dl_actions=[invert_active,edit,delete]}
	{/block}
	
	{$dl_smart_fields=array_flip($dl_smart_fields|default:[])}
	{if $dl_checklist_enabled}
		{$dl_custom_head.mark=1}
		{$x=array_unshift($dl_fields, mark)}
		{$dl_smart_fields.mark=1}
	{/if}
	
	

	{include file="list/toolbar_buttons.tpl"}
	{include file="list/actions.tpl"}
	{include file="list/output_filters.tpl"}	


<table><tr><td>{*1*}

	
{block name="toolbar"}
	{if !$smarty.get.print_view}
	<table style="width:100%">
	<tr>
		<td>
			{call dl_display_toolbar_buttons}
		</td>
		
		{if $m->list_params.paging_enabled && count($list)}
		<td	align="right" width="1%">
			{include file="list/page_by.tpl"}
		</td>
		{/if}
	</tr>
	</table>
	{/if}
{/block}


</td></tr><tr><td>{*1*}

{if !$smarty.get.print_view}
<table>
	<tr>
		<td>
		{if count($views) > 1}
			{include "list/views.tpl"}
		{/if}
		</td>
		<td>
		{if count($list_orders) > 1}
			{include "list/orders.tpl"}
		{/if}
		</td>
	</tr>
</table>
{/if}


{if $dl_filters && !$smarty.get.print_view}
	{include "list/filters.tpl"}
{/if}




</td></tr><tr><td>{*1*}

{if !count($list)}
	<p>{$lang.NO_ITEMS}</p>
{else}

<table class="gwTable gwActiveTable">


<tr>	

	{$dl_order_enabled_fields=array_flip($dl_order_enabled_fields|default:[])}
	
	{foreach $dl_fields as $field}
		<th>
			{if isset($dl_custom_head.$field)}
				{call name="dl_custom_head_$field"}
			{else}
				{$title=$app->fh()->shortFieldTitle($field)}
				{if isset($dl_order_enabled_fields.$field)}
					{include file="list/order.tpl" name=$field title=$title}
				{else}
					{$title}
				{/if}
			{/if}
		</th>
	{/foreach}	
	{if count($dl_actions) && !$smarty.get.print_view}
		<th><i class="fa fa-cog"></i></th>
	{/if}
</tr>

{$list_row_id=0}

{function name=dl_prepare_item}

{/function}

{foreach from=$list item=item}
	{$id=$item->id}
	{$list_row_id=$list_row_id+1}

	
	{call name="dl_prepare_item"}
	
<tr id="list_row_{$list_row_id}" class="{if $item->row_class}{$item->row_class} {/if}{if $id && $smarty.get.id==$id}gw_active_row{/if}" 
	{if $item->list_color}style="background-color:{$item->list_color}"{/if}>
	
	{block name="item_row"}
		{foreach $dl_fields as $field}
			<td>
				{if isset($dl_smart_fields.$field)}
					{call name="dl_cell_$field"}
				{elseif isset($dl_output_filters.$field)}
					{call name="dl_output_filters_`$dl_output_filters.$field`"}
				{else}
					{$item->get($field)|escape}
				{/if}
			</td>
		{/foreach}
		
		{if count($dl_actions) && !$smarty.get.print_view}
			<td nowrap>
				{call dl_display_actions}
			</td>
		{/if}

	{/block} 
</tr>

{/foreach}

</table>

{/if}

</td></tr></table>{*1*}


{if $dl_checklist_enabled}
<br />
	<div id="checklist_actions" style="display:none">
		{$lang.CHECKLIST_SELECT_ACTION}:

		<select name="action" onchange="eval(this.value);this.selectedIndex=0">
			<option value="">{$lang.EMPTY_OPTION.0}</option>
			{foreach $dl_checklist_actions as $action}
				{$action}
			{/foreach}
			{*
			<option value="if(!confirm('Turbut šis veiksmas iššauktas per klaidą! Ar norite atšaukti užsakymų trinimą?'))gw_checklist.submit('delete')">!Trinti</option>
			*}
		</select>
	</div>


	<script type="text/javascript">
	function checked_action(action){
		
		var selected=[];
		$.each($('.checklist_item:checked'), function() {
			selected.push($(this).val());
		});
		
		gw_dialog.open(GW.ln+'/'+GW.path+'/'+action+'?ids='+selected.join(','))		
	}

	gw_checklist.init();
</script>
{/if}

{block name="after_list"}
{/block}

</div>

{block name="close_tpl"}
		{include file="default_close.tpl"}
{/block}