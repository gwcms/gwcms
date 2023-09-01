{if $type==select_ajax}
	{$maximumSelectionLength=1}
{/if}

{if $readonly}
	{foreach $value as $selected}
		{$options[$selected]}{if !$selected@last},{/if}
	{/foreach}
{else}

	
	{if $empty_option}
		{$options=[{$empty_option_title|default:GW::ln('/g/EMPTY_OPTION')}]+$options}
	{/if}	

	{if $preload && $value}
		{if is_array($value)}
			{foreach $value as $valitm}
				{$options[$valitm]="`$valitm` {GW::ln('/g/LOADING')}..."}
			{/foreach}
		{else}
			{$options[$value]="`$value` {GW::ln('/g/LOADING')}..."}
		{/if}
	{/if}
	
	{if $modpath}
		{$source_args = $source_args|default:[]}
		{$tmppath=explode('/', $modpath,2)}
		{if !$datasource}
			{$datasource=$app->buildUri("`$tmppath.0`/`$tmppath.1`/{$optionsview|default:options}", $source_args)}
		{/if}
	{/if}	
	
	<div>

	{if $addifnotexists}
		{$tag_params["data-addifnotexist"] = 1}
		{$tag_params["data-placeholder"] = GW::ln('/m/FIELD_NOTE/START_TYPE')}

		
		{if !isset($tag_params["data-formurl"])}
			{$tag_params["data-formurl"]=$app->buildUri("{$modpath}/{$formview|default:form}", $source_args)}
		{/if}
	{/if}
			 
	<select  id="{$id}" {if $maximumSelectionLength>1}multiple="multiple"{/if} class="{$addclass} {if $addifnotexists}addifnotexists{/if} form-control GWselectAjax" name="{$input_name}{if $maximumSelectionLength>1 && substr($input_name,-2)!='[]'}[]{/if}" 
		 style="width: {$width|default:"100%"}; {if $height}height:{$height};{/if}"
		 {if $preload}data-preload="{$preload}"{/if}
		 {if $value}data-value="{json_encode($value)|escape}"{/if}
		 {if $datasource}data-source="{$datasource}"{/if}
		 {if $sorting}data-sorting="1"{/if}
		 data-maximumselectionlength="{$maximumSelectionLength|default:1}"
		 data-objecttitle="{$object_title}"
		 data-urlargsaddfunc="{$urlArgsAddFunc}"  {*pasirodo data variablai gali buti tik mazosiom raidem jei nori per $(obj).data() paimt*}
		 data-dontcloseonselect="{$dontCloseOnSelect}"
		 data-onchangeFunc="{$onchangeFunc}"
		 {if $empty_option}data-emptyoption="1" data-placeholder="{$empty_option_title|default:GW::ln('/g/EMPTY_OPTION')}"{/if}
		 {if $btnselectall}data-btnselectall="1"{/if}
		 {foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
		 >
		{html_options options=$options selected=$value}
	</select>
	</div>

	
	
	{if !$gwcmssite_input_select2_loaded}
		<script type="text/javascript" src="{$app->sys_base}vendor/select2/full.js?v={$GLOBALS.version_short}"></script>
		<link rel="stylesheet" href="{$app->sys_base}vendor/select2/css.css?v={$GLOBALS.version_short}" type="text/css"/>
		
		{if $bootstrap4}<link rel="stylesheet" href="{$app->sys_base}vendor/select2/select2-bootstrap4.min.css" crossorigin="anonymous" />{/if}
		{if $ln=='lt'}<script src="{$app->sys_base}vendor/select2/lt.min.js"></script>{/if}
		{if $ln=='ru'}<script src="{$app->sys_base}vendor/select2/ru.min.js"></script>{/if}
	{/if}
	<script type="text/javascript" src="{$app->sys_base}applications/admin/static/pack/select_ajax/js.js"></script>
	
	
	
	{if !$GLOBALS.gwcms_input_select_ajax_loaded || $smarty.get.act==doGetFilters}
		{*<link rel="stylesheet" href="{$app->sys_base}vendor/select2/css.css?v={$GLOBALS.version_short}" type="text/css"/>*}
	
		<script type="text/javascript">
			translate_submit = "{GW::ln('/g/SUBMIT')|escape:javascript}";
			translate_selectall = "{GW::ln('/g/SELECT_ALL')|escape:javascript}";
			translate_foundresults = "{GW::ln('/g/TOTAL')|escape:javascript} {GW::ln('/g/FOUND')|escape:javascript}";
			translate_not_found = "{GW::ln('/g/OPTION_NOT_FOUND')|escape:javascript}"
			translate_add_new = "{GW::ln('/g/ADD_NEW_OPTION')|escape:javascript}"

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
			 
		{$GLOBALS.gwcms_input_select_ajax_loaded=1}
	{/if}	
	
	{*
	{if $addifnotexists}
			<script>
				initIfNotExists
					
					var newusrtitle= $('#item_new_user_title_');
					var usrselect = $('#item__user_id__');
					newusrtitle.val($('.select2-search__field').val())
					usrselect.select2('close');	
					$('.newuserrow').fadeIn();
					usrselect.empty().trigger('change');
					
					var new_user_trans = "";
					
					setTimeout(function(){
						usrselect.data("select2").$container.find('.select2-selection__placeholder').text(new_user_trans);
						newusrtitle.focus()
					},500)
					
				}
			</script>
	{/if}
	*}
	
{/if}