{$addlitag=1}

		


<li class="divider"></li>	
	

{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE') shift_button=1}



{list_item_action_m url=["`$item->id`", [act=>doClone3, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption="Create copy"}

	