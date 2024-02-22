{if !$input_name_pattern}
	{$input_name_pattern="filters[vals][%s][]"}
	{$inputct_name_pattern="filters[ct][%s][]"}
	
{/if}


{foreach $params as $param}
	{assign var=$param@key value=$param}
{/foreach}

{$input_name=$input_name_pattern|sprintf:$name}
{$inputct_name=$inputct_name_pattern|sprintf:$name}




{$title=$title|default:$app->fh()->fieldTitle($name)}
{$filter_type=$data.0|default:$filter_type}


{$inp_type=$type|default:'text'}

<div class="filterRow filterRow{$name}">
	
	<div class="col-xs-auto gwFiltLabel" style="display: table-cell;">{$title}</div>
	<div class="col-xs-auto row">
		<div class="col-xs-auto gwFiltCT">
			
		{if strpos($type,'select')!==false}
			{$compare_opt=GW::l('/g/FILTERS_SELECT_COMPARE_TYPES')}
		{else}
			{$compare_opt=GW::l('/g/FILTERS_COMPARE_TYPES')}
		{/if}	
		
		<label class="gwselect">
		
		<select name="{$inputct_name}" class="form-control"  >
			{html_options options=$compare_opt selected=$compare_type|default:'LIKE'}
		</select>
		</label>
		
		
		</div>
		<div class="col-xs-auto gwFiltInput">
			{if $type=='multiselect'}				
				{$value=json_decode($value, true)}
			{elseif $type=='select'}
				{*Add empty option*}

				{$options=$lang.FILTER_EMPTY_OPTION+$options|default:[]}
			{/if}


			{include file="elements/inputs/`$inp_type`.tpl"}    
		</div>
		<div class="col-xs-auto gwFiltActions" style="col-xs-autopadding:2px">
			<a class="gwFilterDelIco" href="#" onclick="gwcms.removeFilter(this, '{$name}');return false"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
		</div>		
		
		
	</div>
</div>