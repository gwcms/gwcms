{$do_toolbar_buttons=[addnew,filters,info]}


{block name="init"}
	{$dl_fields=[title,insert_time,update_time]}
	{$dl_actions=[invert_active,edit,delete]}
{/block}

{$dl_smart_fields=array_flip($dl_smart_fields|default:[])}
{if $smarty.get.print_view}
	{$dl_checklist_enabled=0}
{/if}
{if $dl_checklist_enabled}
	{$dl_custom_head.mark=1}
	{$x=array_unshift($dl_fields, mark)}
	{$dl_smart_fields.mark=1}
{/if}

{include file="list/actions.tpl"}


{*functions*}
{function dl_proc_row_cell}
		{$val=$item->get($field)}
	{if isset($dl_smart_fields.$field)}
		{call name="dl_cell_$field"}
	{elseif isset($dl_output_filters.$field)}
		{call name="dl_output_filters_`$dl_output_filters.$field`"}
	{elseif is_object($val) || is_array($val)}
		{call "dl_output_filters_array"}
	{else}
		{$val|escape}		
	{/if}
	
	{if isset($dl_calc_totals[$field])}
		{$dl_calc_totals[$field]=$dl_calc_totals[$field]+$item->$field}

		{assign var=dl_calc_totals value=$dl_calc_totals scope=global}	
	{/if}		
	
{/function}

{function dl_proc_th_cell}
		{if isset($dl_custom_head.$field)}
			{call name="dl_custom_head_$field"}
		{else}
			{$coltitle=$app->fh()->shortFieldTitle($field)}
			{if isset($dl_order_enabled_fields.$field)}
				{include file="list/order.tpl" name=$field title=$coltitle}
			{else}
				{$coltitle}
			{/if}
		{/if}
{/function}


{function dl_list_proc_rows}

	{foreach from=$list item=item}		
		{$id=$item->id}
		{$list_row_id=$list_row_id+1}
		

		{call name="dl_prepare_item" ifexists=1}
		
		{if $dl_group_list_by && $last_gl_m != $item->get($dl_group_list_by[0])}
			<tr>
				<td colspan='100' class="groupedrow">{call dl_proc_row_cell field=$dl_group_list_by[0]}</td>
			</tr>
			{$last_gl_m=$item->get($dl_group_list_by[0])}
		{/if}		
		

		<tr data-id="{$item->id}" id="list_row_{$item->id}" class="list_row{if $item->row_class} {$item->row_class}{/if}{if $id && $m->acive_object_id==$id} gw_active_row{/if}" 
			{if $item->list_color}style="background-color:{$item->list_color}"{/if}>

			{block name="item_row"}
				{foreach $dl_fields as $field}				
					<td class="dl_cell_{$field}">
						{call dl_proc_row_cell}
					</td>
				{/foreach}

				{if $dl_actions && !$smarty.get.print_view}
					<td nowrap class="gw_dl_actions">
						{call dl_display_actions}
					</td>
				{/if}

			{/block} 
		</tr>

	{/foreach}	
{/function}

{function dl_actions_head}
	{if $dl_actions_head}
		{call dl_display_actions dl_actions=$dl_actions_head}
	{else}
		
	<div class="btn-group dropright gwcmsAction" style="display: inline">
		<i class="fa fa-cog dropdown-toggle dropdown-toggle-icon gwcms-ajax-dd" data-toggle="dropdown" data-url="{$m->buildURI('configuremenu',[id=>$item->id])}"></i>	
		<ul class="dropdown-menu dropdown-menu-right">
			<li><i class="fa fa-spinner fa-pulse"></i></li>
		</ul>
	</div>
		
	{$GLOBALS.dropdown_init_done=1}
		<script type="text/javascript">
			require(['gwcms'], function(){
				gwcms.initDropdowns();
			});
		</script>
	{/if}
{/function}


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


{if $ajax_rows_only}
	{call name="dl_list_proc_rows"}
{else}
	{block name="before_open"}

	{/block}

	
	{if $m->paging_enabled && $m->list_params.paging_enabled && ($list || $m->list_params.page>1)}
		{$do_display_toolbar_pull_right[]=['file',"list/page_by.tpl"]}
	{/if}	

	{block name="open_tpl"}
		{include file="default_open.tpl"}
	{/block}


	{include file="list/output_filters.tpl"}	


	{if !$smarty.get.print_view && !$smarty.get.noPViews}
		<div class="row gwViewsOrdersCont">
			<div>
				{if $views && count($views) > 1}
					{include "list/views.tpl"}
				{/if}
			</div>
			<div>
				{if $list_orders}
					{include "list/orders.tpl"}
				{/if}
			</div>
		</div>
	{/if}


	<div id="additemscontainer" style="display:none"></div>

	{block name="before_list"}
	{/block}
	
	


	{capture append=footer_hidden}
		<link href="{$app_root}static/css/list.css" rel="stylesheet" />
	{/capture}
	
	{if $dl_same_values_ontop && $list}
		{$equal_fields=[]}

		{foreach $dl_fields as $field}

			{$valuecheck=null}
			{$allequals=1}
			
			{if $dl_checklist_enabled && $field=="mark"}{$allequals=0}{*bypass*}{/if}
			
			{foreach $list as $item}
				{if $valuecheck===null}{$valuecheck=$item->$field}{/if}
				{if $valuecheck!=$item->$field}{$allequals=0}{/if}
			{/foreach}
			
			{if $allequals}
				{$equal_fields[$field]=1}
				{$dl_fields=array_flip($dl_fields)}
				{gw_unassign var=$dl_fields[$field]}
				{$dl_fields=array_flip($dl_fields)}
			{/if}
		{/foreach}
		
		{if $equal_fields}
			<table class="table-condensed table-hover table-vcenter table-bordered gwListTable">
				
			{foreach $equal_fields as $field => $tmp}
				<tr><th data-field="{$field}">{dl_proc_th_cell}</th><td>{dl_proc_row_cell item=$item field=$field}</td></tr>
			{/foreach}	
				
			</table>
			<br />
		{/if}
	{/if}


	<div class="row">


		{if $dl_filters && !$smarty.get.print_view && !$smarty.get.filterhide && (count($list) || $m->list_params.filters)}
			<div class="col-xs-auto" id="gwFiltersContainer">
				{include "list/filters.tpl"}
			</div>	
		{/if}		

		<div>
			{if !count($list)}
				<div class="gwcmsNoItems">{GW::l('/g/NO_ITEMS')}</div>
			{else}
					<table class="table-condensed table-hover table-vcenter table-bordered gwTable gwActiveTable gwListTable" data-context="{get_class(current($list))}">
						<tr>	
							{foreach $dl_fields as $field}
								<th>{dl_proc_th_cell}</th>
							{/foreach}	
							{* {if count($dl_actions) && !$smarty.get.print_view} show if actions present*}
							{if !$smarty.get.print_view} {* show even no actions present*}
								<th>{call name="dl_actions_head"}</th>
							{/if}
						</tr>
						<tr id="list_row_0" data-id="0" style="display:none"></tr>

						{call name="dl_list_proc_rows"}
					</table>
						
					{if $dl_checklist_enabled}
						{include "list/checklist.tpl"}
					{/if}
					{if $dl_inline_edit}
						<link type="text/css" href="{$app_root}static/css/flags.css" rel="stylesheet" />
						<script src="{$app_root}static/js/gwcms_inline_edit.js"></script>
						<script type="text/javascript">
							var inline_edit_form_url = '{$m->buildUri(false,[form_ajax=>1,checklist=>$dl_checklist_enabled])}';
							
							require(['gwcms'], function(){
								initActiveList();
							});
						</script>							
					{/if}
				

			{/if}
		</div>

		
		
		
		{if $dl_calc_totals}
			<table class="gwTable mar-top mar-btm gwExtraInfo clear">
				<tr><th colspan="2" class="th_h3 th_single">Totals</th></tr>

				{foreach $dl_calc_totals as $field => $total}
					<tr>
						<td nowrap align="right"><i>{$app->fh()->fieldTitle($field)}</i></td>
						<td>
							{$total}
						</td>
					</tr>		
				{/foreach}
			</table>			
		{/if}
		

		{block name="after_list"}		
		{/block}


	</div>

		
	{capture append=footer_hidden}	
		<script> 
			require(['gwcms'], function(){	
				gw_adm_sys.list_items_count = {intval($query_info.item_count)};
				gw_adm_sys.init_list(); 
			}) </script>				
	{/capture}		
		
		
	{block name="close_tpl"}
		{include file="default_close.tpl"}
	{/block}	
	
{/if}