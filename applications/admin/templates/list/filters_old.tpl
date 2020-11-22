		
			
<div id="filters" class="panel gwlistpanel panel-body" {if !$m->list_params.filters}style="display:none"{/if}>


<form method="POST" action="{$smarty.server.REQUEST_URI}">
	<input type="hidden" name="filters_unset" value="0">
	<input type="hidden" name="act" value="do:set_filters">

	<table>
		<tr>
			<td>
	
	<table class="gwTable" cellspacing="" cellpadding="1">
		<tr>
			<th>{GW::l('/g/FIELD')}</th>
			<th title="{GW::l('/g/COMPARE_TYPE/FULL')}">{GW::l('/g/COMPARE_TYPE/SHORT')}</th>
			<th>{GW::l('/g/FILTER_VALUE')}</th>
		</tr>
	

		
		{foreach $dl_filters as $filter}
			{if $filter}
				{include file="elements/input_filter.tpl" name=$filter@key params=$filter}
			{/if}
		{/foreach}
		
	</table>
	
	</td><td valign="top">
	
		<button >{GW::l('/g/APPLY_FILTER')}</button><br>
		<button style="margin-top:5px" onclick="this.form.elements['filters_unset'].value=1;" title="{GW::l('/g/REMOVE_FILTER')}"><i class="fa fa-close"></i></button>
		<button style="margin-top:5px" onclick="$('.dl_hidden_filter').fadeIn();$(this).fadeOut();return false" title="{GW::l('/g/UNHIDE_FILTER')}"><i class="fa fa-search-plus"></i></button>
	
	</td></tr>
	
	</table>
	</form>


</div>		
		