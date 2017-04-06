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
	after_input - turinys pridedamas po laukeliu
*}

{if $params_expand}
	{foreach $params_expand as $k => $v}
		{assign var=$k value=$v}
	{/foreach}
	{$params_expand=[]}
{/if}


{if !$hideifempty || $value || $item->$name}
	
{$title=$title|default:$m->fieldTitle($name)}


{function input_label}
	<td id="{$input_id}_inputLabel" class="input_label_td {if $m->error_fields.$name}gwErrorLabel has-error{/if}" width="{$width_title}" {if $nowrap} nowrap{/if}>
		<span style="white-space:nowrap;">
			{if !$hidden_note}
				{if $m->lang.FIELD_NOTE.$name}
					{$hidden_note=$m->lang.FIELD_NOTE.$name}
				{/if}
			{/if}
			

				<span>

				{$title}
				{if $hidden_note} 
					<a  class="fa gwAddPopover add-popover" data-content="{$hidden_note|escape}"  data-placement="right" data-container="body" data-toggle="popover" data-html="true" data-trigger="focus" href="#popover" onclick="return false"></a>
				{/if}

				{if $required} <span title="{$lang.REQUIRED}">*</span>{/if}</span>
			{if $i18n || $item->i18n_fields.$name}<sup title="International" class="i18n_tag">(Int)</sup>{/if}
		</span>

		{if $note}<br /><small class="input_note">{$note}</small>{/if}	
		
	</td>	
{/function}

{capture assign=input_content}
	{if $i18n==2}
		{foreach $langs as $ln_code}
			
			<td class="input_td col_i18n_{$ln_code}" width="{$width_input}" {if $wide}colspan="2"{/if}>
				{include file="elements/input0.tpl" name="`$name`_`$ln_code`"}  
			</td>
		{/foreach}
	{else}
		<td class="input_td" width="{$width_input}" {if $layout=='wide'}colspan="2"{/if} style="{if $nopading}padding:0{/if}" 
			{if $layout=='inline' && $hidden_note}title="{$hidden_note}"{/if}>
			
			<div class="input-group" style="width:100%">


    
			{include file="elements/input0.tpl"}
			
			{if $layout=='inline'}
				{if $m->error_fields.$name}
					<span class="error_label" style='display:block'>{GW::l($m->error_fields.$name)}</span>
				{/if}
			{/if}
			
			{$after_input}
			
			</div>
		</td>
	{/if}
{/capture}


	


{if $layout=='wide'}
	<tr class="{$rowclass}">
		{call input_label}
	</tr>
	<tr id="gw_input_{$name}"  class="{$rowclass}">
		{$input_content}
	</tr>
{elseif $layout=='inline'}
	{$input_content}
{else}
	<tr id="gw_input_{$name}"  class="{$rowclass}">
		{call input_label}
		{$input_content}
	</tr>
{/if}
{/if}

