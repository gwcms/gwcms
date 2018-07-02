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


{if !$input_id}
	{$input_id=$name}
	{$input_id=str_replace(["[","]"],'__',$input_id)}
	{$input_id=str_replace("/",'___',$input_id)}
{/if}


{if $i18n}
	{$langs=array_flip(GW::$settings.LANGS)}
	{$langs=[$app->ln=>1]+$langs}
	
	
	{if !isset($GLOBALS.form_18n_init_done)}
		<link type="text/css" href="{$app_root}static/css/flags.css" rel="stylesheet" />
		
		{$GLOBALS.form_18n_init_done=1}
		<script type="text/javascript">
			require(['forms'], function(){ gw_forms.initI18nForm({json_encode($langs)}) })
		</script>
	{/if}		
	
	{$langs=array_keys($langs)}
	
	
	
	


	{function name="langswitch"}
		{if $ln_code=='en'}{$flag_code='gb'}{else}{$flag_code=$ln_code}{/if}

		<span class="gwform_sw_ln" href="#" onclick="tooglei18nCol('{$ln_code}');return false">
				<img src="{$app_root}static/img/blank.gif" class="flag flag-{$flag_code}" alt="{$ln_code}" /> {if $show_ln_code_title}<span class="toggle_i18n_{$ln_code}" title="{GW::l("/g/LANG/`$ln_code`")}">{$ln_code}</span>{/if}
		</span>
	{/function}
{/if}




{function input_label}
	{if method_exists($item, 'isChangedField')}
		{$impischanged=$item->isChangedField($name)}
	{/if}
	
	<td id="{$input_id}_inputLabel" class="input_label_td {if $m->error_fields.$name}gwErrorLabel has-error{/if} {if $impischanged}gwinput-label-modified{/if} {if $layout=='wide'}inp_lab_wide{/if}" {if $layout=='wide'}colspan="2" {else}width="{$width_title}"{/if} {if $nowrap} nowrap{/if} >
		<span style="white-space:nowrap;">
			{if !$hidden_note}
				{if isset($m->lang.FIELD_NOTE.$name)}
					{$hidden_note=$m->lang.FIELD_NOTE.$name}
				{/if}
			{/if}
			

				<span>

				{$title}
				{if $hidden_note} 
					<a  class="fa gwAddPopover add-popover" data-content="{$hidden_note|escape}"  data-placement="right" data-container="body" data-toggle="popover" data-html="true" data-trigger="focus" href="#popover" onclick="return false"></a>
				{/if}

				{if $required} <span title="{$lang.REQUIRED}">*</span>{/if}</span>
			{if $i18n || $item->i18n_fields.$name}<span title="International expand" class="i18n_tag {if $i18n_expand}i18n_tag_active{/if}"><i class="fa fa-flag i18n_link"></i></span>{/if}
		</span>

		{if $note}<br /><small class="input_note">{$note}</small>{/if}	
		
		{if $impischanged}
			{$tmp=$item->getOriginal($name)}
			<i class="fa fa-floppy-o text-warning" title="{if $tmp}Orig.: {$tmp|escape}{/if}"></i>{else}
		{/if}
		
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
			
			{if $after_input}
				<div class="input-group" style="width:{$btngroup_width|default:"290px"}">
			{/if}
			

			
		{if $i18n>2}
			{foreach $langs as $ln_code}
				
				<span class="ln_contain ln_contain_{$ln_code} ln_contain_{$i18n} {if $app->ln==$ln_code}ln_cont_main{else}ln_cont_oth {if $i18n_expand}i18n_expand{/if}{/if}" title="{$ln_code}">
				{call name="langswitch"}
					{if $i18n==4 && !$width}{$width="calc(100% - 25px)"}{/if}
				
				{include file="elements/input0.tpl" name="`$name`_`$ln_code`"}  
				</span>
			{/foreach}
		{else}
			{include file="elements/input0.tpl"}
		{/if}
			
			{if $layout=='inline'}
				{if $m->error_fields.$name}
					<span class="error_label" style='display:block'>{GW::l($m->error_fields.$name)}</span>
				{/if}
			{/if}
			
			{if $after_input}
				{$after_input}
			
				</div>
			{/if}
		</td>
	{/if}
{/capture}


{if $tabs}
	{foreach $tabs as $tab}{$rowclass="`$rowclass` tabitm_`$tab` tabitm_row"}{/foreach}
{/if}	


{if $layout=='wide'}
	<tr class="{$rowclass}">
		{call input_label}
	</tr>
	<tr id="gw_input_{$input_id}"  class="{$rowclass}">
		{$input_content}
	</tr>
{elseif $layout=='inline'}
	{$input_content}
{else}
	<tr id="gw_input_{$input_id}"  class="{$rowclass}">
		{call input_label}
		{$input_content}
	</tr>
{/if}
{/if}

