{*
	type - laukelio tipas, pvz textarea,htmlarea,image,text,bool,select,image,file
	hideifempty - naudingas kai laukelio tipas read ir nenorima rodyti tuscios eilutes
	name - laukelio vardas
	value - laukelio vertÄ, jei laukelio verte nenustatyta tada imama $item->value
	default - laukelio vertÄ jei laukelis neuÅ¾pildytas
	item - duomenu objektas
	i18n - 1|0 - daugiakalbiskas laukelis
	title - laukelio antraÅ¡tÄ
	note - matoma pastaba
	hidden_note - tooltip pastaba
	width_title - antraÅ¡tÄs plotis
	nowrap - antraÅ¡tÄs turinys Ä¯ kitÄ eilutÄ neperkeliamas
	placeholder - sufleris kÄ Ä¯vesti
	readonly - laukelis tik skaitomas
	input_name_pattern - laukelio vardo formatas // pvz: input[section][%s]
	options - select,multiselect,read tipams vertÄs
	class - laukelio objektui uÅ¾dedama klasÄ
*}
{if !$hideifempty || $value || $item->$name}
	
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
	
	{if $i18n==2}
		{foreach GW::$settings.LANGS as $ln_code}
			
			<td class="input_td col_i18n_{$ln_code}" width="{$width_input}">
				{include file="elements/input0.tpl" name="`$name`_`$ln_code`"}  
			</td>
		{/foreach}
	{else}
		<td class="input_td" width="{$width_input}">
			{include file="elements/input0.tpl"}  
		</td>
	{/if}
	
	
</tr>
{/if}

