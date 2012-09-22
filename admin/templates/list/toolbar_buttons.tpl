{function name=dl_toolbar_buttons_addnew}
	{gw_link relative_path=form title=$lang.CREATE_NEW icon="action_file_add" params=[id=>0]}
	&nbsp;&nbsp;&nbsp;
{/function}

{function name=dl_toolbar_buttons_filters}
	{if $dl_filters}
		<img src="img/icons/search.png"  align="absmiddle" onclick="$(this).next().click()" /> 
		<a href="#show_filters" onclick="$('#filters').toggle();return false">{$lang.SEARCH}</a>	
		&nbsp;&nbsp;&nbsp;
	{/if}
{/function}	


{function name=dl_toolbar_buttons_info}
	{if $page->notes}
		<img src="img/icons/action_info.png"  align="absmiddle" onclick="$(this).next().click()" /> 
		<a href="#show_about" onclick="open_notes({$page->id});return false">{$lang.ABOUT}</a>	
		&nbsp;&nbsp;&nbsp;
		
		<div id="dialog-message" title="{$lang.ABOUT} {$page->title}" style="display:none"></div>
	{/if}
{/function}
	
{function name=dl_toolbar_buttons_dialogconf}
	<script>
		function lds_config()
		{
			gw_dialog.open('{$ln}/{GW::$request->path}/dialogconfig', { width:400 })
		}
	</script>
	<img src="img/icons/settings.png"  align="absmiddle" onclick="$(this).next().click()" /> 
	<a href="#" onclick="lds_config();return false">{$lang.LIST_DISPLAY_SETTINGS}</a>	
	&nbsp;&nbsp;&nbsp;
{/function}	
	
{function name=dl_display_toolbar_buttons}
	{foreach $dl_toolbar_buttons as $button_func}
		{call name="dl_toolbar_buttons_`$button_func`"}
	{/foreach}	
{/function}
