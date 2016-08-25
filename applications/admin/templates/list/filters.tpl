
<div class="btn-group gwFilterBtnGroup">
    <button id="gwFilterTgglBtn" class="btn btn-default" onclick="gwcms.filtersBtnClick(this);" data-toggle="button">
		{if $m->list_params.filters}
			<i class="gwico-FilterFilled"></i> 
		{else}
			<i class="gwico-Filter"></i> 
		{/if}
	</button>
    <button type="button" data-toggle="dropdown" class="btn btn-default gwtoolbarbtn dropdown-toggle dropdown-toggle-icon" aria-expanded="false">
        <i class="dropdown-caret"></i>
    </button>
    <ul class="dropdown-menu">
		
		<li class="dropdown-header">{GW::l('/g/ADD_FILTER_4_FIELD')}</li>
		{foreach $dl_filters as $key => $filter}
			<li><a href="" onclick="gwcms.addFilters('{$key}');return false" class="gwAddFilterMI" data-field="{$key}">{$app->fh()->fieldTitle($key)}</a></li>
		{/foreach}
		
        <li class="divider"></li>
        <li><a href="#" onclick="gwcms.addAllFilters();return false">{GW::l('/g/ADD_ALL_FILTERS')}</a></li>
    </ul>
</div>	
		
		<form id="gwFiltersForm" method="POST" action="{$smarty.server.REQUEST_URI}" data-filters-present="{if $m->list_params.filters}1{else}{/if}" onsubmit="gwcms.filtersSubmit()" >
	<input type="hidden" id="gwFiltersUnset" name="filters_unset" value="0">
	<input type="hidden" name="act" value="do:set_filters">


			<div id="gwDropFilters">
				{include "list/filtersajax.tpl" filters_directload=1}
			</div>

<div id="gwDropFiltersLoading" style="display:none">
	<i class="fa fa-refresh fa-spin"></i>
</div>
	
<div style="clear:both"></div>
<div id="gwFiltersActions" style="display:none" >	
		<button class="btn btn-default">{$lang.APPLY_FILTER}</button><br>
		{*<button style="margin-top:5px" onclick="this.form.elements['filters_unset'].value=1;" title="{$lang.REMOVE_FILTER}"><i class="fa fa-close"></i></button>*}
</div>

	</form>

{if $m->list_params.filters}
	{capture append=footer_hidden}
		<script>
			$(function(){ gwcms.filtersInit() })
		</script>
	{/capture}
{/if}

