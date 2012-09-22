{block name="before_open"}
		
{/block}

{block name="open_tpl"}
		{include file="default_open.tpl"}
{/block}


{$dl_toolbar_buttons=[addnew,filters,info]}

<div>
	{block name="init"}
		{$dl_fields=[title,insert_time,update_time]}
		{$dl_actions=[invert_active,edit,delete]}
	{/block}
	
	{$dl_smart_fields=array_flip($dl_smart_fields|default:[])}

	{include file="list/toolbar_buttons.tpl"}
	{include file="list/actions.tpl"}
	{include file="list/output_filters.tpl"}
	
<table><tr><td>{*1*}

	
{block name="toolbar"}
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
{/block}


</td></tr><tr><td>{*1*}

{if count($views) > 1}
	{include "list/views.tpl"}
{/if}


{if $dl_filters}
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
			{$title=FH::shortFieldTitle($field)}
			{if isset($dl_order_enabled_fields.$field)}
				{include file="list/order.tpl" name=$field title=$title}
			{else}
				{$title}
			{/if}
		</th>
	{/foreach}	
	{if count($dl_actions)}
		<th>{$lang.ACTIONS}</th>
	{/if}
</tr>

{$list_row_id=0}

{foreach from=$list item=item}
	{$id=$item->id}
	{$list_row_id=$list_row_id+1}
<tr id="list_row_{$list_row_id}" class="{if $id && $smarty.get.id==$id}gw_active_row{/if}" 
	{if $item->list_color}style="background-color:{$item->list_color}"{/if}>
	
	{block name="item_row"}
		{foreach $dl_fields as $field}
			<td>
				{if isset($dl_smart_fields.$field)}
					{call name="dl_cell_$field"}
				{elseif isset($dl_output_filters.$field)}
					{call name="dl_output_filters_`$dl_output_filters.$field`"}
				{else}
					{$item->get($field)}
				{/if}
			</td>
		{/foreach}
		
		{if count($dl_actions)}
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

{block name="after_list"}
{/block}

</div>

{block name="close_tpl"}
		{include file="default_close.tpl"}
{/block}