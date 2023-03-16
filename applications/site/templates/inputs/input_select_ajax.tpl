
	{if $empty_option}
		{$options=[GW::ln('/g/EMPTY_OPTION')]+$options}
	{/if}	
	

	{if $preload && $value}
		{if is_array($value)}
			{foreach $value as $valitm}
				{$options[$valitm]="`$valitm` {GW::l('/g/LOADING')}..."}
			{/foreach}
		{else}
			{$options[$value]="`$value` {GW::ln('/g/LOADING')}..."}
		{/if}
	{/if}
	
	{if $modpath}
		{$tmppath=explode('/', $modpath,2)}
		{if !$datasource}
			{$datasource=$app->buildUri("`$tmppath.0`/`$tmppath.1`/{$optionsview|default:options}", $source_args)}
		{/if}
	{/if}	
	
	<div>
	<select  id="{$id}" {if $maximumSelectionLength>1}multiple="multiple"{/if} class="{$addclass} form-control GWselectAjax" name="{$input_name}{if $maximumSelectionLength>1 && substr($input_name,-2)!='[]'}[]{/if}" 
		 style="width: {$width|default:"100%"}; {if $height}height:{$height};{/if}"
		 {if $preload}data-preload="{$preload}"{/if}
		 {if $value}data-value="{json_encode($value)|escape}"{/if}
		 {if $datasource}data-source="{$datasource}"{/if}
		 {if $sorting}data-sorting="1"{/if}
		 {if $readonly}data-disabled="1"{/if}
		 data-maximumselectionlength="{$maximumSelectionLength|default:1}"
		 data-objecttitle="{$object_title}"
		 data-urlargsaddfunc="{$urlArgsAddFunc}"  {*pasirodo data variablai gali buti tik mazosiom raidem jei nori per $(obj).data() paimt*}
		 data-dontcloseonselect="{$dontCloseOnSelect}"
		 data-onchangeFunc="{$onchangeFunc}"
		{if $empty_option}data-emptyoption="1" data-placeholder="{GW::ln('/g/EMPTY_OPTION')}"{/if}
		 {if $btnselectall}data-btnselectall="1"{/if}
		 {foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
		 data-empty-option="{GW::ln('/g/EMPTY_OPTION_TITLE')}"
		 {if $required}required="required"{/if}
		 >
		{html_options options=$options selected=$value}
		
		
	</select>
	</div>

	
	
	{if !$gwcmssite_input_select2_loaded}
		<script type="text/javascript" src="{$app->sys_base}vendor/select2/full.js?v={GW::$globals.version_short}"></script>
		<link rel="stylesheet" href="{$app->sys_base}vendor/select2/css.css?v={GW::$globals.version_short}" type="text/css"/>
		
		{if $bootstrap4}<link rel="stylesheet" href="{$app->sys_base}vendor/select2/select2-bootstrap4.min.css" crossorigin="anonymous" />{/if}
		{if $ln=='lt'}<script src="{$app->sys_base}vendor/select2/lt.min.js"></script>{/if}
		{if $ln=='ru'}<script src="{$app->sys_base}vendor/select2/ru.min.js"></script>{/if}
	{/if}
	<script type="text/javascript" src="{$app->sys_base}applications/admin/static/pack/select_ajax/js.js"></script>
	
	
	
	{if !$gwcms_input_select_ajax_loaded || $smarty.get.act==doGetFilters}
		{*<link rel="stylesheet" href="{$app->sys_base}vendor/select2/css.css?v={GW::$globals.version_short}" type="text/css"/>*}
	
		<script type="text/javascript">
			translate_submit = "{GW::l('/g/SUBMIT')}";
			translate_selectall = "{GW::l('/g/SELECT_ALL')}";
			translate_foundresults = "{GW::l('/g/TOTAL')} {GW::l('/g/FOUND')}";

			//this will allow open dialog in root window, if this window is iframed
			{if $bootstrap4}bootstrap4 = true;{/if}
				
			{*	
					select2_lang ={ "noResults": function(){ return "Rezultatai nerasti | pradėkite vesti vardą/pavardę"; } }				
			*}
			{if $ln=='lt'}select2_lang =  "lt-LT"	;{/if}
			{if $ln=='ru'}select2_lang =  "ru-RU"	;{/if}
		
			
			$(document).ready(function(){
				initSelect2Inputs();
		        });
			
			
		</script>
		
			<style>
			 .ui-state-highlight { height: 1.5em; line-height: 1.2em;background-color: yellow; margin-top: 5px; margin-right: 5px; width: 100px;}
			 .sortstarted .select2-selection__choice{ 
				 display:block !important; float:none !important; 
			 }
			 </style>		
		{assign var=gwcms_input_select_ajax_loaded value=1 scope=global}
	{/if}	
	

	
