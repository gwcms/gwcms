{include file="default_form_open.tpl" form_width="1000px"}

{$debug_edit_add_permission=1}

{$demooptions=[1=>'option1', '2'=>"option2", '3'=>"option3", '4'=>"option4"]}
{$demooptionsfix=['option1','option2','option3']}

{call e field=demo_select type=select options=[1=>'Random option1', '2'=>"Random option2"] empty_option=1}
{call e field=demo_multiselect 
	type=multiselect 
	options=$demooptions
	empty_option=1 value=json_decode($item->demo_multiselect)
	sorting=1
}


{call e field=demo_bool type=bool}
{call e field=demo_number type=number min=1 max=10}
{call e field=demo_email type=email}
{call e field=demo_date type=date}
{call e field=demo_datetime type=datetime}

{call e field=demo_daterage type=daterange}

{call e field=demo_color type=color}

{call e field=demo_text type=text}
{call e field=demo_pass type=password}

{include file="elements/input_transkey.tpl" name=demo_select_rtans_key}



{$opts=[vars_hint=>'{$link1} - link to somewhere {$email} - will be replaced to email address',format_texts_ro=>1,vals=>[format_texts=>2]]}
{$owner=['owner_type'=>'system/demoinputs']}


{include file="elements/input_select_mailtemplate.tpl" field=demo_select_mailtemplate}
{call e field=demo_htmlarea type=htmlarea height="100px"}
{call e field=demo_textarea type=textarea  height="100px"}

{call e field=demo_attachments 
	type=attachments 
	valid=[image=>[storewh=>'2000x1500',minwh=>'1x1',maxwh=>'6000x6000'],limit=>5]
	preview=[thumb=>'50x50']
}



{call e field=demo_tags type=tags}

{*
{include 
	file="elements/input_select_edit.tpl" 
	name=demo_select_ajax_load type=select
	empty_option=1
	datasource=$app->buildUri('datasources/languages',['native'=>'1'])
}
*}



{call e field="demo_btnselectall"
	type="multiselect_ajax"
	options=['a1'=>'a','b1'=>'b','c1'=>'c','d1'=>'d',e1=>e,f1=>f]
	value=json_decode($item->demo_btnselectall)
	btnselectall=1
	
}

{call e field="demo_select_ajax_load"
	type="select_ajax"
	after_input_f="editadd"
	object_title=GW::l('/M/datasources/MAP/childs/languages/title')
	form_url=$app->buildUri('datasources/languages/form',['native'=>'1',clean=>2,dialog=>1])
	list_url=$app->buildUri('datasources/languages',[clean=>2])
	empty_option=1
	datasource=$app->buildUri('datasources/languages/search') 
	preload=1
	minimuminputlength=0
	options=[]
}


{call e field="demo_select_country_id_multi"
	type="multiselect_ajax"
	object_title=GW::l('/M/datasources/MAP/childs/countries/title')
	modpath="datasources/countries"
	options=[]
	value=json_decode($item->demo_select_country_id_multi)
	preload=1
}


{call e field="demo_select_country_id_multi_sorting"
	type="multiselect_ajax"
	modpath="datasources/countries"
	options=[]
	value=json_decode($item->demo_select_country_id_multi_sorting)
	preload=1
	sorting=1
	btnselectall=1
}


{call e field="multiselect_users"
	type="multiselect_ajax"
	modpath="users/usr"
	options=[]
	preload=1
	sorting=1
	value_format=json1

}

{*adds automaticaly object_title=GW::l('/M/datasources/MAP/childs/countries/title')*}
{call e field="demo_select_country_id_single"
	type="select_ajax"
	after_input_f="editadd"
	modpath="datasources/countries"
	options=[]
	preload=1
}

{call e field=state_toogle_demo type=bool stateToggleRows="smtpdetails"}

{capture assign=tmp}
	<table>
{call e field=mail_smtp_host type=text}
{call e field=mail_smtp_user type=text}

	</table>
{/capture}
{call e field=smtp_config type=read value=$tmp rowclass="smtpdetails"}

{call e field=rodomas_jei_state_toogle_isjungtas type=text rowclass="smtpdetails_inv"}
 
{call e_group_open label="Example of multiple inputs per row"}
	{call e field=mail_smtp_host1 notr=1 type=text}
	{call e field=mail_smtp_user1 notr=1 type=text}
{call e_group_close}	

{capture assign=tmp}
	<small class="text-muted">type</small> {call e0 title=false field=body_editor type=select options=GW::l('/M/todo/OPTIONS/body_editor') readonly=isset($custom_cfg.body_editor_ro)}
	<small class="text-muted">height</small> {call e0 title=false field=body_editor_height type=select options=GW::l('/M/todo/OPTIONS/body_editor_height') readonly=isset($custom_cfg.body_editor_height_ro)}	
{/capture}
{call e field="demo_multiinput" type=read value=$tmp hidden_note=GW::l('/g/FIELDS_NOTE/PUSH_APPLY_TO_TAKE_EFFECT')}

{call e field="country_code"
	type="select_ajax"
	object_title=GW::l('/M/datasources/MAP/childs/countries/title')
	modpath="datasources/countries"
	source_args=[byCode=>1]
	options=[]
	preload=1
}


{*
	after_input=$addnew 
	urlArgsAddFunc="setUpCompositionPartSearchArgs()"
*}

{call e field="languages_by_code" type=multiselect_ajax sorting=1 modpath="datasources/languages" source_args=[byTranslCode=>1]  value_format=json1}
	
{call e field=demo_code_smarty type=code_smarty  height="100px"}
{call e field=demo_code_json type=code_json  height="100px" layout=wide}

{call e field=demo_multilang_text3 type=text i18n=3}
{call e field=demo_multilang_text4 type=text i18n=4}


	
{call e field="flags/askhide" type=bool  
	hidden_note="mysql-fieldtype: tinyint;
	dataobject conf: public $flags_conf=['flags'=>[0=>'succmail',1=>'isadult',2=>'isman',3=>'paid',4=>'paymconfirmed',5=>'askhide',6=>'sept',7=>'ast']];
	public $encode_fields=['flags'=>'flags']
"}

{call e field="flags2" type=multiselect options=[askhide=>'askhide', succmail=>'succmail'] note=" 
	 mysql field:  `flags2` set('askhide','succmail') NOT NULL
	 dataobject: public $encode_fields=['flags2'=>'comma']
	"}
	
{call e field="flags3" type=multiselect options=[option1=>'option1title', option2=>'option2title'] value_format=json1 note="value_format=json1 - json read"}
		

{call e field=class_ids type=multiselect_ajax modpath="datasources/classificators" source_args=[group=>demoinputs] after_input_f=editadd preload=1 options=[]}

{call e field=demo_splittext type=splittext parts=3}


{call e field=demo_inputmask type="inputmask" regex='^[0-9A-Za-z]{1,10}$' hidden_note="Unikalus raktas tik lotyniškos raidės ir skaičiai A-Z 0-9 1-10simboliu"}

{call e field=demo_divedit hidden_note="Gerai kai ivestis gali buti ir daug ir mazai, kai daug apriboti aukscio prasipletima iki kazkokio limito" maxheight="200px" type="divedit"}

{call e field=demo_after_input_textops after_input_f=textopts options=$demooptionsfix options_fix=1}

{call e field=select_ajax_but_not_ajax note="preloaded options with search" 
	type=select_ajax options=DateTimeZone::listIdentifiers() empty_option=1 options_fix=1}
   





{include file="default_form_close.tpl" submit_buttons=[save]}