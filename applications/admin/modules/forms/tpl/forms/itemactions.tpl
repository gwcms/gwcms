{$addlitag=1}

	
		
{$cnt=count($item->elements)}
{list_item_action_m href=$m->buildUri("`$item->id`/elements") caption="Įvestys({$cnt})" iconclass="fa fa-wpforms" }	
	
	
		
{$cnt = count($item->answers)}
{list_item_action_m href=$m->buildUri("`$item->id`/answers") caption="Užpildymai({$cnt})" iconclass="fa fa-stack-overflow"}
		

		
		


	
	
{*
<li class="divider"></li>


{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE') shift_button=1}
*}


{list_item_action_m url=["`$item->id`", [act=>doClone3, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption="Sukurti kopiją su elementais"}
{list_item_action_m url=["`$item->id`/form",[id=>$item->id]] iconclass="fa fa-pencil-square-o" caption=GW::l('/g/VIEWS/form')}	
	