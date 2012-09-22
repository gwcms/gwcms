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

{function name=dl_actions_invert_active}
	{gw_link do="invert_active" icon="active_`$item->active`" params=[id=>$item->id] show_title=0}
{/function}

{function name=dl_display_actions}
	{foreach $dl_actions as $button_func}
		{call name="dl_actions_`$button_func`"}
	{/foreach}	
{/function}
