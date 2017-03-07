{function name=do_toolbar_buttons_addnew}
	<img 
		align="absmiddle" 
		title="{$lang.CREATE_NEW}" 
		src="{$app->icon_root}action_file_add.png" 
		onclick="$(this).next().trigger('click');
				location.href = $(this).next().attr('href')" vspace="3" />
	<a href="{gw_link relative_path=form  icon="action_file_add" params=[id=>0] path_only=1}">{$lang.CREATE_NEW}</a>	

	&nbsp;&nbsp;&nbsp;
{/function}

{function name=do_toolbar_buttons_filters}
	{if $dl_filters}
		<img src="{$app->icon_root}search.png"  align="absmiddle" onclick="$(this).next().click()" vspace="3" /> 
		<a href="#show_filters" onclick="$('#filters').toggle();
				return false">{$lang.SEARCH}</a>	
		&nbsp;&nbsp;&nbsp;
	{/if}
{/function}	

{function name=do_toolbar_buttons_print}
	<img src="{$app->icon_root}print.png"  align="absmiddle" onclick="$(this).next().click()" vspace="3" /> 
	<a href="{$app->buildUri(false,[print_view=>1],[carry_params=>1])}">{$lang.PRINT_VIEW}</a>
	&nbsp;&nbsp;&nbsp;
{/function}


{function name=do_toolbar_buttons_info}
	{if $page->notes}
		<img src="{$app->icon_root}action_info.png"  align="absmiddle" onclick="$(this).next().click()" vspace="3"  /> 
		<a href="#show_about" onclick="open_notes({$page->id});return false">{$lang.ABOUT}</a>	
		&nbsp;&nbsp;&nbsp;

		<div id="dialog-message" title="{$lang.ABOUT} {$page->title}" style="display:none"></div>
	{/if}
{/function}

{function name=do_toolbar_buttons_dialogconf}
	<script type="text/javascript">
		function lds_config()
		{
				gw_dialog.open('{$m->buildURI('dialogconfig')}', { width: 400 })
		}
	</script>
	<img src="{$app->icon_root}settings.png"  align="absmiddle" onclick="$(this).next().click()" vspace="3"  /> 
	<a href="#" onclick="lds_config();
			return false">{$lang.LIST_DISPLAY_SETTINGS}</a>	
	&nbsp;&nbsp;&nbsp;
{/function}	

{function name=do_toolbar_buttons_hidden}

	<div class="unhideroot" style="">
		<img class="visible unhidetrigger" align="absmiddle" src="{$app->icon_root}action_down24.png">

		<div class="dropdown">
			{foreach $do_toolbar_buttons_hidden as $button_func}
				<div class="menuitem">
					{call name="do_toolbar_buttons_`$button_func`"}
				</div>
			{/foreach}	
		</div>
	</div>


{/function}

{function name=do_toolbar_buttons_importdata} 
	{gw_link relative_path=importdata title=GW::l('/A/VIEWS/importdata') icon="action_action"} &nbsp;&nbsp;&nbsp; 
{/function}	
{function name=do_toolbar_buttons_exportdata} 
	{gw_link relative_path=exportdata title=GW::l('/A/VIEWS/exportdata') icon="action_action"} &nbsp;&nbsp;&nbsp; 
{/function}

{function name=do_toolbar_buttons_edit} 
	{gw_link relative_path=edit title=GW::l('/A/VIEWS/edit') icon="action_edit24"} &nbsp;&nbsp;&nbsp; 
{/function}


{function name=dl_display_toolbar_buttons}
	{foreach $do_toolbar_buttons as $button_func}
		<span style="white-space: nowrap;">{call name="do_toolbar_buttons_`$button_func`"}</span>
	{/foreach}	
{/function}
