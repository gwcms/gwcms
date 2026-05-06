{$group_undo_check = $m->getGroupUndoCheck($item)}

{if $group_undo_check.ok}
	{list_item_action_m url=[false,[act=>doGroupUndo,id=>$item->id]] iconclass="fa fa-undo" confirm=1 caption="Group Undo"}
{/if}
