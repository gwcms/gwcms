{function name=dl_actions_move}
	{gw_link do="move" icon="action_move_up" params=[id=>$item->id,where=>up] show_title=0}
	{gw_link do="move" icon="action_move_down" params=[id=>$item->id,where=>down] show_title=0}
{/function}

{function name=dl_actions_delete}
	{gw_link do="delete" icon="action_file_delete" params=[id=>$item->id] show_title=0 confirm=1}
{/function}

{function name=dl_actions_edit}
	{gw_link relative_path="`$item->id`/form" icon="action_edit" show_title=0}
{/function}

{function name=dl_actions_editshift}
	{gw_link relative_path="`$item->id`/form" icon="action_edit_shift" show_title=0 shift_button=1}
{/function}

{function name=dl_actions_invert_active}
	{gw_link do="invert_active" icon="active_`$item->active`" params=[id=>$item->id] show_title=0}
{/function}

{function name=dl_display_actions}
	{foreach $dl_actions as $button_func}
		{call name="dl_actions_`$button_func`"}
	{/foreach}	
{/function}


{function name=dl_actions_clone}
	{gw_link relative_path="form" do="clone" icon="copy" params=[id=>$item->id] show_title=0}
{/function}

{function name=dl_actions_ext_actions}
<div class="unhideroot2">
	<img class="dropdown-trigger ajax-fill-dd" align="absmiddle" src="{$app_root}img/icons/action_expand.png" data-url="{$m->buildURI('itemactions',[id=>$item->id])}" data-id="{$item->id}">
	
	<div class="dropdown" id="dropdown-{$item->id}"><i class="fa fa-spinner fa-pulse"></i></div>
</div>
		
	{if !isset($GLOBALS.dropdown_init_done)}
		{$GLOBALS.dropdown_init_done=1}
		<script type="text/javascript">
			initDropdowns();
		</script>
	{/if}
{/function}



{function dl_actions_inlineedit}
	<a class="inline_edit_trigger" data-id="{$item->id}" data-url="{$m->buildUri("`$item->id`/form",[ajax=>1])}"><img 
			align="absmiddle" src="{$app_root}img/icons/action_edit.png"></a>
{/function}
