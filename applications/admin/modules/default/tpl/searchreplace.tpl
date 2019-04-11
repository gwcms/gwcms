{include "elements/input_func.tpl"}


<form action="" method="post">
	<input type="hidden" name="act" value="doSearchReplace">

{$searchrepl=$m->list_params['searchreplace']}


<table class="table-bordered gwTable  gwListTable">
	<tr>
		<th>
			{GW::l('/g/FIELD')}
		</th>
		<th>
			{GW::l('/G/common_module/SEARCH_PHRASE')}
		</th>
		<th>
			{GW::l('/G/common_module/REPLACE_PHRASE')}
		</th>
		<th>
		</th>
	</tr>	
	<tr>
		{if $smarty.get.searchreplace && $smarty.get.items_count}
			{$readonly=1}
		{/if}
		<td>
			{call e0 field="fieldname" type=select options=$options.fields force_init_select2=1 value=$searchrepl.fieldname}
			
		</td>
		<td>
			{call e0 field="searchval" value=$searchrepl.searchval required=1}
		</td>
		<td>
			{*do not fill on second come back*}
			{if $smarty.get.searchreplace<1}{$tmp=""}{else}{$tmp=$searchrepl.replaceval}{/if}
			
			{call e0 field="replaceval" value=$tmp required=1}
		</td>
		<td>
		{if $smarty.get.searchreplace==1 && $smarty.get.items_count}
			{call e0 field="confirm" value=1 type=hidden}
			{*if it is select2 input select2 makes real input disabled & browser does not sends its value	*}
			{call e0 field="fieldname" value=$searchrepl.fieldname type=hidden}
			{call e0 field="items_count" value=$smarty.get.items_count type=hidden}
			
			<button class="btn btn-primary">{GW::l('/G/common_module/CONFIRM_REPLACE')} ({GW::l('/g/FILTERED')}: {$smarty.get.items_count} {GW::l('/g/ITEMS')}) <i class="fa fa-arrow-circle-right"></i></button>
			<a class="btn btn-warning" href="{$m->buildUri(false,[act=>doCancelSearchReplace])}"><i class="fa fa-close"></i> {GW::l('/g/CANCEL')}</a>
		{elseif $smarty.get.searchreplace==2}
			<span class='bg-success' style="padding:5px;">{GW::l('/G/common_module/SEARCH_REPLACE_OK')} &raquo; </span> &nbsp;
			
			<a class="btn btn-success" href="{$m->buildUri(false,[act=>doCancelSearchReplace])}"><i class="fa fa-close"></i> {GW::l('/g/CLOSE')}</a>
		{else}
			<button class="btn btn-primary">{GW::l('/g/SUBMIT')} <i class="fa fa-arrow-circle-right"></i></button>
			
			{if $smarty.get.searchreplace==1}
				<a class="btn btn-warning" href="{$m->buildUri(false,[act=>doCancelSearchReplace])}"><i class="fa fa-close"></i> {GW::l('/g/CANCEL')}</a>
			{else}
				<button onclick="gwSearchReplaceClose();return false" class="btn btn-warning" onclick=""><i class="fa fa-close"></i> {GW::l('/g/CLOSE')}</button>
			{/if}
		{/if}
			
			
			
			
		</td>		
	</tr>
</table>
</form>			
			
<br /><br />

{include "includes.tpl"}