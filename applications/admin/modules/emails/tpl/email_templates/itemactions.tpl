{$addlitag=1}

{list_item_action_m url=["`$item->id`/testpdfgen"] iconclass="fa fa-file-pdf-o" caption="Tikrinti pdf generavimą"}

{if $m->write_permission}	

	{list_item_action_m 
			url=[false,[act=>doSendTest,id=>$item->id]] 
			iconclass="fa fa-paper-plane" 
			query_param=["email","Nurodykite gavėjo el. pašto adresą"]
			caption="Siųsti test laišką [LT]"}

{/if}

{*
<li class="divider"></li>


{dl_actions_delete shift_button=1}

*}
