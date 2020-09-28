{$addlitag=1}

{list_item_action_m url=["`$item->id`/testpdfgen",[id=>$item->id]] iconclass="fa fa-file-pdf-o" caption="Tikrinti pdf generavimą"}



{list_item_action_m 
		url=[false,[act=>doOpenInSite,id=>$item->id]] 
		iconclass="fa fa-link" 
		tag_params=[target=>'_blank']
		caption="Dokumento nuoroda"}
		
{$cnt = $item->countAnswers()}
{list_item_action_m href=$app->buildUri("forms/forms/{$item->form->id}/answers",["doc_id"=>$item->id]) iconclass="fa fa-wpforms" caption="Atsakymai ({$cnt})"}



{*
<li class="divider"></li>


{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE') shift_button=1}
*}
