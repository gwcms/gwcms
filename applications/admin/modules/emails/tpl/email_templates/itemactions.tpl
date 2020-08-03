{$addlitag=1}

{list_item_action_m url=["`$item->id`/testpdfgen"] iconclass="fa fa-file-pdf-o" caption="Tikrinti pdf generavimą"}



	{list_item_action_m 
			url=[false,[act=>doSendTest,id=>$item->id]] 
			iconclass="fa fa-paper-plane" 
			query_param=["email","Nurodykite gavėjo el. pašto adresą"]
			caption="Siųsti test laišką [LT]"}


{*
<li class="divider"></li>


{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE') shift_button=1}
*}
