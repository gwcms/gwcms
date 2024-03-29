{if !$input_name_pattern}
	{$input_name_pattern="filters[vals][%s][]"}
	{$inputct_name_pattern="filters[ct][%s][]"}
	
{/if}


{foreach $params as $param}
	{assign var=$param@key value=$param}
{/foreach}

{$input_name=sprintf($input_name_pattern,$name)}
{$inputct_name=sprintf($inputct_name_pattern,$name)}




{$title=$title|default:$app->fh()->fieldTitle($name)}
{$filter_type=$data.0|default:$filter_type}


{$inp_type=$type|default:'text'}

<div class="filterRow filterRow{$name} {if $muted}filterRowMuted{/if}">
	
	<div class="col-xs-auto gwFiltLabel" style="display: table-cell;">{$title}</div>
	<div class="col-xs-auto row">
		<div class="col-xs-auto gwFiltCT">
			
		{if strpos($type,'multiselect')!==false}
			{$compare_opt=GW::ln('/g/FILTERS_SELECT_COMPARE_TYPES')}
		{elseif strpos($type,'select')!==false}
			{$compare_opt=GW::ln('/g/FILTERS_SELECT1_COMPARE_TYPES')}
		{else}
			{$compare_opt=GW::ln('/g/FILTERS_COMPARE_TYPES')}
		{/if}	
		
		
		{if $compare_type=="IN" && !isset($compare_opt['IN'])}
			{$compare_opt=array_merge($compare_opt, ["IN"=>"IN"])}
			{if is_array($value)}
				{$value=implode(',', $value)}
			{/if}
		{/if}
		
		{if $params.ct}
			{$compare_opt=$params.ct}
		{/if}
		
		<label class="gwselect">
		
		<select name="{$inputct_name}" class="form-control filtertype">
			{html_options options=$compare_opt selected=$compare_type|default:'LIKE'}
		</select>
		</label>
				
		</div>
		<div class="col-xs-auto gwFiltInput">
			{if $type=='multiselect' || $type=='multiselect_ajax'}				
				{$value=json_decode($value, true)}
			{elseif $type=='select'}
				{*Add empty option*}

				{$options=GW::ln('/g/FILTER_EMPTY_OPTION')+$options|default:[]}
			{/if}


			{include file="elements/inputs/`$inp_type`.tpl"}    
		</div>
		<div class="col-xs-auto gwFiltActions" style="col-xs-autopadding:2px">
			<a class="gwFilterDelIco" href="#" onclick="gwcms.removeFilter(this, '{$name}');return false"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
		</div>		
		
		
	</div>
</div>

		


	
</script>