{function calcElmName}{$input_name_pattern=$app->fh()->calcInputNamePattern($input_name_pattern, $type)}{$input_name_pattern|sprintf:$field}{/function}
{function calcElmId}{str_replace(['[',']','/'],'__',$input_name)}{/function}

{function input_label}
	{if method_exists($item, 'isChangedField')}
		{$impischanged=$item->isChangedField($name)}
	{/if}
	{if $type!=hidden && $title!==false}<label class="control-label" for="{$id}" 
		{if $hidden_note} data-original-title="{$hidden_note|escape:'html'}" rel="tooltip" class="btn btn-default" data-toggle="tooltip" data-placement="top" title=""{/if}
	>{$title} {if $required}<sup title="{GW::ln('/G/validation/REQUIRED')}">*</sup>{/if} 
	{if $hidden_note}<i class="fa fa-question-circle primary-color"></i>{/if} 
	{if $note}<small style="font-weight: normal;font-style: italic">{$note|escape}</small>{/if}
	{if $note_raw}{$note_raw}{/if}
	       </label>{/if}
{/function}

{function name=input_content}

	{if $i18n==2}
		{foreach $langs as $ln_code}
			
			<td class="input_td col_i18n_{$ln_code} {$inputContainClass} {$rowclass}" width="{$width_input}" {if $wide}colspan="2"{/if} data-type="inputc">
				{call e0 field="`$name`_`$ln_code`"}
			</td>
		{/foreach}
	{else}
		<div class="form-group {if $m->error_fields.$field}u-has-error-v1 has-feedback{/if} {$rowclass}" {if $type==hidden}style='display:none'{/if}>
			
			
					
			
			{if $after_input_f}
				{capture assign="after_input"}
					{include "common_module/elements/afterinput/`$after_input_f`.tpl"}
				{/capture}
			{/if}
			
			{if $after_input}
				<div class="input-group" style="width:{$btngroup_width|default:"290px"}">
			{/if}		

			
		{if $i18n>2}
			{foreach $langs as $ln_code}
				
				<span class="ln_contain ln_contain_{$ln_code} ln_contain_{$i18n} {if $app->ln==$ln_code}ln_cont_main{else}ln_cont_oth {if $i18n_expand}i18n_expand{/if}{/if}" title="{$ln_code}">
				{call name="langswitch"}
					{if $i18n==4 && !$width}{$width="calc(100% - 25px)"}{/if}
				
				{call e0 field="`$name`_`$ln_code`"}  
				</span>
			{/foreach}
		{else}
			{call e0}
		{/if}
			
			{if $layout=='inline'}
				{if $m->error_fields.$name}
					<span class="error_label" style='display:block'>{GW::ln($m->error_fields.$name)}</span>
				{/if}
			{/if}
			
			{if $after_input}
				{$after_input}
			
				</div>
			{/if}
		</div>
	{/if}
{/function}

{function name="langswitch"}
	{if $ln_code=='en'}{$flag_code='gb'}{else}{$flag_code=$ln_code}{/if}

	<span class="gwform_sw_ln" href="#" onclick="tooglei18nCol('{$ln_code}');return false">
			<img src="{$app_root}static/img/blank.gif" class="flag flag-{$flag_code}" alt="{$ln_code}" /> {if $show_ln_code_title}<span class="toggle_i18n_{$ln_code}" title="{GW::ln("/g/LANG/`$ln_code`")}">{$ln_code}</span>{/if}
	</span>
{/function}

{function name=e}
	{$name=$field}
	
	{*
	type - laukelio tipas, pvz textarea,htmlarea,image,text,bool,select,image,file
	hideifempty - naudingas kai laukelio tipas read ir nenorima rodyti tuscios eilutes
	name - laukelio vardas
	value - laukelio vertė, jei laukelio verte nenustatyta tada imama $item->value
	$value_format - dekodavimo funkcija
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


    {if !$hideifempty || isset($value) || $item->$name}

    {$title=$title|default:$m->fieldTitle($name)}


    {*copy to {function e0}*}

    {call calcElmName assign=input_name}
    {call calcElmId assign=id}
    {*copy to {function e0}*}

    {$inputContainClass="gw_input_`$id`"}

    {if $i18n}
            {if !$langs}
                    {$langs=array_flip(GW::$settings.LANGS)}
		    
		
		{if $app->i18next && !$item->skip_i18next}
			{$langs=$langs+$app->i18next}
			
		{/if}
		
                {$langs=[$app->ln=>1]+$langs}
            {/if}


            {if !isset($GLOBALS.form_18n_init_done)}
                    <link type="text/css" href="{$app_root}static/css/flags.css" rel="stylesheet" />

                    {$GLOBALS.form_18n_init_done=1}
                    <script type="text/javascript">
                            require(['forms'], function(){ gw_forms.initI18nForm({json_encode($langs)}) })
                    </script>
            {/if}		

            {$langs=array_keys($langs)}

    {/if}



    {if $tabs}
            {foreach $tabs as $tab}{$rowclass="`$rowclass` tabitm_`$tab` tabitm_row"}{/foreach}
    {/if}	


    {if $notr}
        {if $layout!='wide'}
                {call input_label}
        {else}
                {$rotatedlabel=1}
        {/if}

        {call input_content}	
    {elseif $layout=='wide'}
            <tr class="{$rowclass}">
                    {call input_label}
            </tr>
            <tr id="gw_input_{$id}"  class="{$rowclass}">
                    {call input_content}
            </tr>
    {elseif $layout=='inline'}
            {call input_content}
    {else}
            <tr id="gw_input_{$id}"  class="{$rowclass}">
                    {call input_label}
                    {call input_content}
            </tr>
    {/if}
    {/if}
{/function}




{function name=e0}
	{$name=$field}
	{if $params_expand}
		{foreach $params_expand as $k => $v}
			{assign var=$k value=$v}
		{/foreach}
	{/if}

	{*copy to {function e}*}
	{call calcElmName assign=input_name}
	{call calcElmId assign=id}
	{*copy to {function e}*}
	
	{if !isset($value) || $value===null}
		{if strpos($name, '/')!==false && get_class($item)!='stdClass'}	
			
			{$valget_func=$valget_func|default:'get'}
			{$value=$item->$valget_func($name)}
			
		{else}	
			{if $valget_func}
				{$value=$item->$valget_func($name)}
			{else}
				{$value=$item->$name}
			{/if}
		{/if}
	
		{if $value!=='0' && !$value && $default}
			{$value=$default}
		{/if}
	{/if}

	{if is_array($value) && $type=='textarea'}
		{$value=GW_Json_Format_Helper::f($value)}
	{/if}
	{if $value_format}
		{if $value_format=="json1"}
			{$value=json_decode($value, true)}	
		{else}
			{d::ldump("Format `$value_format` not implemented")}
		{/if}
		
	{/if}

	{if $options_fix}
		{$tmp=[]}
		{foreach $options as $opt}
			{$tmp[$opt]=$opt}
		{/foreach}
		{$options=$tmp}
	{/if}

	{$inp_type=$type|default:'text'}
	{include file="common_module/elements/inputs/`$inp_type`.tpl"}


	{*
	{if !in_array($type,["read",'image','attachments','file']) && $readonly != 1}
		{$tmppattern = str_replace('item[','fields[', $input_name_pattern)}
		<input name="{$tmppattern|sprintf:$name}" type="hidden" value="1" />
	{/if}
	*}
{/function}


{function name="e_group_open"}
	<tr class="{$rowclass} e_horizontal_group">
		{if $label!==false}<td style="{if $labelright}text-align:right{/if}">{$label}</td>{/if}
		<td {if $label===false}colspan="2"{/if}>
		<table><tr>
{/function}
{function name="e_group_close"}
		</tr></table>
</td>
</tr>
{/function}



	{if $hidden_note && !$input_help_loaded}

		<script type="text/javascript">
		    $(function(){
		       $('[rel="tooltip"]').tooltip();
		       $('[rel="popover"]').popover();
		    });
		</script>

		{assign scope=global var=$input_help_loaded value=1}

			
	{/if}