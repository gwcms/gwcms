{$addlitag=1}

		


<li class="divider"></li>	
	

{dl_actions_delete}

{if $m->write_permission}
	{list_item_action_m url=["`$item->id`", [act=>doClone3, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption="Create copy"}
{/if}

	