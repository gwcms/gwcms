{$addlitag=1}
<li class="divider"></li>

{list_item_action_m url=["`$item->id`/form", [act=>doClone, id=>$item->id]]  iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}

{list_item_action_m 
	url=[false, [act=>doCopySlots, id=>$item->id]] 
	query_param=[source,"Nurodykite konkurso id iš kurio kopijuoti"] 
	iconclass="fa fa-files-o text-mint" caption=GW::l('/m/VIEWS/doCopySlots')}



{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE') shift_button=1}


