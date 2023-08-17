{$addlitag=1}

{list_item_action_m url=["`$item->id`/testpdfgen",[id=>$item->id]] iconclass="fa fa-file-pdf-o" caption="Tikrinti pdf generavimą"}



{list_item_action_m 
		url=[false,[act=>doOpenInSite,id=>$item->id]] 
		iconclass="fa fa-link" 
		tag_params=[target=>'_blank']
		caption="Dokumento nuoroda"}
		
{$cnt = $item->countAnswers()}
{list_item_action_m href=$app->buildUri("forms/forms/{$item->form->id}/answers",["doc_id"=>$item->id]) iconclass="fa fa-wpforms" caption="Atsakymai ({$cnt})"}






{if $item->get('itax_status_ex/purchase')==7}
	{list_item_action_m url=[false,[act=>doItaxCancel,id=>$item->id]] iconclass="fa fa-window-close text-danger" confirm=1 caption="Šalinti iš itax(Pirkimo sąsk.)"}
{/if}


{if $m->feat(itax)}
	{list_item_action_m url=[false,[act=>doItaxSync,id=>$item->id]] iconclass="fa fa-cloud-upload" confirm=1 caption="Suvesti į Itax"}
{/if}

{list_item_action_m url=[false,[act=>doSendInvitations,id=>$item->id]] iconclass="fa fa-envelope-o" caption="Siųsti kvietimus pasirašyti"}

{list_item_action_m url=[false,[act=>doSendInvitationsCreateUsers,id=>$item->id]] iconclass="fa fa-envelope-o" caption="Siųsti kvietimus pasirašyti pagal el pašto adresą"}

{list_item_action_m 
	href=$app->buildUri("emails/email_queue",[searchbycontent=>$item->key,clean=>2]) 
	iconclass="fa fa-envelope-square" action_addclass="iframe-under-tr"  
	caption="išsiųsti laiškai"}
{*
<li class="divider"></li>


{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE') shift_button=1}
*}


{dl_actions_delete shift_button=1}
