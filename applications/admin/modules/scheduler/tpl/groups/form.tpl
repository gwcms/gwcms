{include file="default_form_open.tpl"}



{call e field=title i18n=4} 

{include 
	file="elements/input_select_edit.tpl" 
	name=location_id type=select 
	empty_option=1
	datasource=$app->buildUri('scheduler/locations')
}

{*
{call e field=key hidden_note="unikalus kodas be tarpu tik lotyniškos raidės ir skaičiai"}
*}

{call e field=owner_key hidden_note="Neredaguoti objektoid" default=$smarty.get.owner_key}




{include 
	file="elements/input_select_edit.tpl" 
	name=type1 type=select 
	empty_option=1
	datasource=$app->buildUri('scheduler/types',['type'=>'grouptype1'])
}

{include 
	file="elements/input_select_edit.tpl" 
	name=type2 type=select 
	empty_option=1
	datasource=$app->buildUri('scheduler/types',['type'=>'grouptype2'])
}


{*include 
	file="elements/input_select_edit.tpl" 
	name=project_id type=select 
	empty_option=1
	datasource=$app->buildUri('scheduler/types',['type'=>'grouptype2'])
*}


{call e field=table_description i18n=4}
 
{call e field=header_text type=textarea height="100px" i18n=4}
{call e field=description type=textarea height="100px" i18n=4}


{if $item->getParentObjectType() == 'ipmc_competitions'}
	{call e field=participant_list_id type=select empty_option=1 options=$item->getParentObject()->getParticipantListsOpt()}
{/if}


{call e field=active type=bool}





{include file="default_form_close.tpl"}