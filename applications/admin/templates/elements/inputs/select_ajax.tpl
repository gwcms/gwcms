{if $readonly}
	{foreach $value as $selected}
		{$options[$selected]}{if !$selected@last},{/if}
	{/foreach}
{else}

	
	{if $empty_option}
		{$options=GW::l('/g/EMPTY_OPTION')+$options}
	{/if}	

	{if $preload && $value}
		{if is_array($value)}
			{foreach $value as $valitm}
				{$options[$valitm]="`$valitm` {GW::l('/g/LOADING')}..."}
			{/foreach}
		{else}
			{$options[$value]="`$value` {GW::l('/g/LOADING')}..."}
		{/if}
	{/if}
	
	{if $modpath}
		{$tmppath=explode('/', $modpath,2)}
		{if !$datasource}
			{$datasource=$app->buildUri("`$tmppath.0`/`$tmppath.1`/options", $source_args)}
		{/if}
	{/if}	
	

	<select  id="{$id}" {if $maximumSelectionLength>1}multiple="multiple"{/if} class="form-control GWselectAjax" name="{$input_name}{if $maximumSelectionLength>1 && substr($input_name,-2)!='[]'}[]{/if}" 
		 style="width: {$width|default:"100%"}; {if $height}height:{$height};{/if}"
		 {if $preload}data-preload="{$preload}"{/if}
		 {if $value}data-value="{json_encode($value)|escape}"{/if}
		 {if $datasource}data-source="{$datasource}"{/if}
		 data-maximumselectionlength="{$maximumSelectionLength|default:1}"
		 data-objecttitle="{$object_title}"
		 data-urlargsaddfunc="{$urlArgsAddFunc}"  {*pasirodo data variablai gali buti tik mazosiom raidem jei nori per $(obj).data() paimt*}
		 data-dontcloseonselect="{$dontCloseOnSelect}"
		 data-onchangeFunc="{$onchangeFunc}"
		 {if $empty_option}data-emptyoption="1" data-placeholder="{GW::l('/g/EMPTY_OPTION/0')}"{/if}
		 {if $btnselectall}data-btnselectall="1"{/if}
		 >
		{html_options options=$options selected=$value}
	</select>


	{if !$gwcms_input_select2_loaded}
		{$m->addIncludes("bs/select2css", 'css', "`$app_root`static/vendor/select2/css.css")}
		{assign var=gwcms_input_select2_loaded value=1 scope=global}
	{/if}
	
	{if !$gwcms_input_select_ajax_loaded}
		<script type="text/javascript">
			translate_submit = "{GW::l('/g/SUBMIT')}";
			translate_selectall = "{GW::l('/g/SELECT_ALL')}";
			translate_foundresults = "{GW::l('/g/TOTAL')} {GW::l('/g/FOUND')}";

			//this will allow open dialog in root window, if this window is iframed
			require(['gwcms'], function(){  require(['pack/select_ajax/js'], function(){ 
					initSelect2Inputs();
			}) });
			
		</script>
		{assign var=gwcms_input_select_ajax_loaded value=1 scope=global}
	{/if}	
	
	
{/if}