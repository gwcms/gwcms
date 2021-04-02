{$addlitag=1}

		

		
{$cnt=count($item->execprice)}
{list_item_action_m href=$m->buildUri("`$item->id`/execprice") caption="Execution prices ({$cnt})" iconclass="fa fa-money" }	
	
{$cnt=count($item->shipprice)}
{list_item_action_m href=$m->buildUri("`$item->id`/shipprice") caption="Shipping prices ({$cnt})" iconclass="fa fa-ship" }	


<li class="divider"></li>	
	

{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE') shift_button=1}



{list_item_action_m url=["`$item->id`", [act=>doClone3, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption="Sukurti kopiją su elementais"}

	