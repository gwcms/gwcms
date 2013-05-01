{* for i18n form request when requesting not default language. show only i18n fields*}
{if !isset($smarty.request.lang) || GW::$request->ln == $smarty.request.lang || $i18n || $item->isI18NField($name)}


{if !$input_name_pattern}
	{$input_name_pattern="item[%s]"}
	{if $type=='multiselect'}{$input_name_pattern="`$input_name_pattern`[]"}{/if}
{/if}
{$input_name=$input_name_pattern|sprintf:$name}
{$title=$title|default:FH::fieldTitle($name)}


{if !$value}
	{$value=$item->get($name)}
		
	{if $data_type=='numeric' && !$value}
		{$value=$default}
	{else}
		{$value=$value|default:$default}
	{/if}
{/if}

{if is_array($value)}
	{$value=GW_Json_Format_Helper::f($value)}
{/if}



<tr id="gw_input_{$name}">
	<td class="input_label_td {if $m->error_fields.$name}error_label{/if}" width="{$width_title}" {if $nowrap} nowrap{/if}>
		<span style="white-space:nowrap;">
			{if $hidden_note}<span class="tooltip" title="{$hidden_note}">{$title}</span>{else}{$title}{/if}
			{if $i18n || $item->i18n_fields.$name}<sup title="International" class="i18n_tag">(Int)</sup>{/if}
		</span>

		{if $note}<br /><small class="input_note">{$note}</small>{/if}	
		
	</td>
	<td class="input_td" width="{$width_input}">
	{if $did_note}<small>{$did_note}</small>{/if}  
	{$inp_type=$type|default:'text'}
	
	{if $type=='password'}{$inp_type='text'}{/if}
	{include file="elements/inputs/`$inp_type`.tpl"}  
	
	</td>
	
</tr>

{/if}