{$title=$title|default:$app->fh()->fieldTitle($name)}

<tr id="gw_input_{$name}">
	<td class="input_label_td {if $m->error_fields.$name}error_label{/if}" width="{$width_title}" {if $nowrap} nowrap{/if}>
		<span style="white-space:nowrap;">
			{if !$hidden_note}
				{if $m->lang.FIELD_NOTE.$name}
					{$hidden_note=$m->lang.FIELD_NOTE.$name}
				{/if}
			{/if}
			{if $hidden_note}
				<span class="tooltip" title="{$hidden_note|escape}">
			{else}
				<span>
			{/if}
				{$title}{if $required} <span title="{$lang.REQUIRED}">*</span>{/if}</span>
			{if $i18n || $item->i18n_fields.$name}<sup title="International" class="i18n_tag">(Int)</sup>{/if}
		</span>

		{if $note}<br /><small class="input_note">{$note}</small>{/if}	
		
	</td>
	<td class="input_td" width="{$width_input}">
		
		
		
		{include file="elements/input0.tpl"}  
	
	</td>
	
</tr>

