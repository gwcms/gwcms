{include file="common.tpl"}


	{function name=do_toolbar_buttons_edituser} 
		{if $item->id}
			{toolbar_button title="Kiti nustatymai" iconclass='fa fa-user' href=$app->buildUri("users/usr/`$item->id`/form") tag_params=[target=>_blank]}
		{/if}
		
		{capture append=footer_hidden}	
			<script>
				require(['gwcms'], function(){	gw_adm_sys.init_iframe_open(); })
			</script>		
		{/capture}		
	{/function}	

	{$do_toolbar_buttons[]=edituser}
	

{include file="default_form_open.tpl"}



{call e field=username}
{call e field=email}
{call e field=name}
{call e field=surname}
{call e field=phone}

{*
{call e field=group_ids type=multiselect_ajax options=[] preload=1 modpath="users/groups"}
*}

{if !$item->id && $smarty.get.quickinsert} 
	{$item->set(active,1)}
	{$item->set(country,LT)}
{/if}
{call e field=active type=bool}




{call e field="country"
	type="select_ajax"
	modpath="datasources/countries"
	source_args=[byCode=>1]
	options=[]
	preload=1
}

{call e field=city}
{call e field=birthdate type=date}

{call e field=pass_new  type=pass_visible title=$m->lang.FIELDS.pass}

{*
{call e field=sms_gates type="multiselect" options=[labas=>labas, routesms=>routesms]}
*}

{call e field=gender type=radio options=GW::l('/m/OPTIONS/gender') empty_option=1 tabs=[participant] separator="&nbsp;&nbsp;&nbsp;"}





{if $item->id || !$smarty.get.quickinsert}
	{call e field=image type=image}
	


	
	{call e field=description type=textarea height=100px}	
{/if}







{if $item->id}
	{call e field=session_validity type=select options=$m->lang.SESSION_VALIDITY_OPT}	
{/if}

{if $app->user->isRoot() && $item->id}
	{call e field=fbid type=number}
	{call e field=ggid type=number}
{/if}

{if !$item->id}
	{call e type="read" title="<span class='text-muted'>Daugiau laukelių</span>" value="<span class='text-muted'>Spauskite  '{GW::l('/g/APPLY')}'</span>"}
{/if}


{if $m->rootadmin}
	{call e field=parent_user_id type=select_ajax modpath="users/usr"  preload=1 options=[] default=$app->user->id}
{/if}


{*

{call e field="keyval/organisation_division" type=multiselect_ajax modpath='datasources/classificators' source_args=[type=>organisation_division] options=[] preload=1 after_input_f=editadd value_format=json1 empty_option=1}
{call e field="keyval/organisation_role" type=select_ajax modpath='datasources/classificators' source_args=[type=>organisation_role] options=[] preload=1 after_input_f=editadd empty_option=1}
{call e field="keyval/public_email" type=email}
{call e field="keyval/public_phone" type=inputmask mask='"mask": "+370 6 9{7}"'}
{call e field="keyval/contacts_priority" type=number}
{call e field="contacts_photo" type=image}

*}


{include file="default_form_close.tpl" extra_fields=[id,funds,fbid,ggid,"parent_user/title",'age',"ageGroupObj/title",'pass_change']}
