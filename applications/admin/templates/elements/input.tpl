{*
	type - laukelio tipas, pvz textarea,htmlarea,image,text,bool,select,image,file
	hideifempty - naudingas kai laukelio tipas read ir nenorima rodyti tuscios eilutes
	name - laukelio vardas
	value - laukelio vertė, jei laukelio verte nenustatyta tada imama $item->value
	default - laukelio vertė jei laukelis neužpildytas
	item - duomenu objektas
	i18n - 1|0 - daugiakalbiskas laukelis
	title - laukelio antraštė
	note - matoma pastaba
	hidden_note - tooltip pastaba
	width_title - antraštės plotis
	nowrap - antraštės turinys į kitą eilutę neperkeliamas
	placeholder - sufleris ką įvesti
	readonly - laukelis tik skaitomas
	input_name_pattern - laukelio vardo formatas // pvz: input[section][%s]
	options - select,multiselect,read tipams vertės
	class - laukelio objektui uždedama klasė
*}

{if $params_expand}
	{foreach $params_expand as $k => $v}
		{assign var=$k value=$v}
	{/foreach}
	{$params_expand=[]}
{/if}


{if !$hideifempty || $value || $item->$name}
	
{$title=$title|default:$app->fh()->fieldTitle($name)}


{function input_label}
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
{/function}

{function input_content}
	{if $i18n==2}
		{foreach GW::$settings.LANGS as $ln_code}
			
			<td class="input_td col_i18n_{$ln_code}" width="{$width_input}" {if $wide}colspan="2"{/if}>
				{include file="elements/input0.tpl" name="`$name`_`$ln_code`"}  
			</td>
		{/foreach}
	{else}
		<td class="input_td" width="{$width_input}" {if $wide}colspan="2"{/if} style="{if $nopading}padding:0{/if}">
			{include file="elements/input0.tpl"}  
		</td>
	{/if}
{/function}

{if $wide}
	<tr>
		{call input_label}
	</tr>
	<tr id="gw_input_{$name}">
		{call input_content}
	</tr>	
{else}
	<tr id="gw_input_{$name}">
		{call input_label}
		{call input_content}
	</tr>
{/if}
{/if}

