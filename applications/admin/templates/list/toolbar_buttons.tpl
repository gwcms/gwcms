{function name=dl_toolbar_buttons_addnew}
	<img 
		align="absmiddle" 
		title="{$lang.CREATE_NEW}" 
	     src="{$app_root}img/icons/action_file_add.png" 
	     onclick="$(this).next().trigger('click');location.href=$(this).next().attr('href')" vspace="3" />
	<a href="{gw_link relative_path=form  icon="action_file_add" params=[id=>0] path_only=1}">{$lang.CREATE_NEW}</a>	
	
	&nbsp;&nbsp;&nbsp;
{/function}

{function name=dl_toolbar_buttons_filters}
	{if $dl_filters}
		<img src="{$app_root}img/icons/search.png"  align="absmiddle" onclick="$(this).next().click()" vspace="3" /> 
		<a href="#show_filters" onclick="$('#filters').toggle();return false">{$lang.SEARCH}</a>	
		&nbsp;&nbsp;&nbsp;
	{/if}
{/function}	


{function name=dl_toolbar_buttons_info}
	{if $page->notes}
		<img src="{$app_root}img/icons/action_info.png"  align="absmiddle" onclick="$(this).next().click()" vspace="3"  /> 
		<a href="#show_about" onclick="open_notes({$page->id});return false">{$lang.ABOUT}</a>	
		&nbsp;&nbsp;&nbsp;
		
		<div id="dialog-message" title="{$lang.ABOUT} {$page->title}" style="display:none"></div>
	{/if}
{/function}
	
{function name=dl_toolbar_buttons_dialogconf}
	<script type="text/javascript">
		
		function lds_config()
		{
			gw_dialog.open('{$app->buildUri("`$app->path`/dialogconfig")}', { width:400 })
		}
		
	</script>
	<img src="{$app_root}img/icons/settings.png"  align="absmiddle" onclick="$(this).next().click()" vspace="3"  /> 
	<a href="#" onclick="lds_config();return false">{$lang.LIST_DISPLAY_SETTINGS}</a>	
	&nbsp;&nbsp;&nbsp;
{/function}	

{function name=dl_toolbar_buttons_hidden}

<div class="unhideroot" style="">
	<img class="visible" align="absmiddle" src="{$app_root}img/icons/action_down24.png" onmouseover="$(this).next().offset({ left: $(this).offset().left})">
	
	<div class="dropdown">
		{foreach $dl_toolbar_buttons_hidden as $button_func}
			<div class="menuitem">
				{call name="dl_toolbar_buttons_`$button_func`"}
			</div>
		{/foreach}	
	</div>
</div>


{/function}

{function name=dl_toolbar_buttons_importdata} 
	{gw_link relative_path=importdata title=GW::l('/A/VIEWS/importdata') icon="action_action"} &nbsp;&nbsp;&nbsp; 
{/function}	
{function name=dl_toolbar_buttons_exportdata} 
	{gw_link relative_path=exportdata title=GW::l('/A/VIEWS/exportdata') icon="action_action"} &nbsp;&nbsp;&nbsp; 
{/function}



{function name=dl_display_toolbar_buttons}
	{foreach $dl_toolbar_buttons as $button_func}
		<span style="white-space: nowrap;">{call name="dl_toolbar_buttons_`$button_func`"}</span>
	{/foreach}	
{/function}
