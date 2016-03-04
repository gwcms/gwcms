{if !$input_name_pattern}
	{$input_name_pattern="filters[%s][]"}
{/if}


{foreach $params as $param}
	{assign var=$param@key value=$param}
{/foreach}

{$input_name=$input_name_pattern|sprintf:$name}

{$data=$m->list_params.filters.$name|default:[]}

{$title=$title|default:$app->fh()->fieldTitle($name)}
{$value=$data.1}
{$filter_type=$data.0|default:$filter_type}


{$inp_type=$type|default:'text'}


<tr {if !$value && $m->list_params.filters}class="dl_hidden_filter"{/if}>
	<td>{$title}</td>
	<td>
		{if strpos($type,'select')!==false}
			<input type="hidden" name="{$input_name}" value="{if $type=="multiselect"}IN{else}={/if}" />
		{else}
			{$compare_opt=['LIKE'=>'~', '='=>'=', '<'=>'<', '>'=>'>', '!='=>'&ne;']}
			{html_options name=$input_name options=$compare_opt selected=$filter_type|default:'LIKE'}
		{/if}
	</td>
	<td nowrap>
		{if $type=='multiselect'}
			{$input_name_pattern="`$input_name_pattern`[]"}
			{$value=array_splice($data, 1)}
		{elseif $type=='select'}
			{*Add empty option*}

			{$options=$lang.FILTER_EMPTY_OPTION+$options|default:[]}
		{/if}
		
		
		{include file="elements/inputs/`$inp_type`.tpl"}    
	</td>
</tr>