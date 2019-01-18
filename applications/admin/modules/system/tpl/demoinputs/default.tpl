{include file="default_form_open.tpl"}


{include file="elements/input.tpl" name=demo_select type=select options=[1=>'Random option1', '2'=>"Random option2"] empty_option=1}
{include file="elements/input.tpl" 
	name=demo_multiselect type=multiselect 
	options=[1=>'option1', '2'=>"option2", '3'=>"option3", '4'=>"option4"] 
	empty_option=1 value=json_decode($item->demo_multiselect)
	sorting=1
}


{include file="elements/input.tpl" name=demo_bool type=bool}
{include file="elements/input.tpl" name=demo_number type=number min=1 max=10}
{include file="elements/input.tpl" name=demo_email type=email}
{include file="elements/input.tpl" name=demo_date type=date}
{include file="elements/input.tpl" name=demo_color type=color}

{include file="elements/input.tpl" name=demo_text type=text}
{include file="elements/input.tpl" name=demo_pass type=password}

{include file="elements/input_transkey.tpl" name=demo_select_rtans_key}



{$opts=[vars_hint=>'{$link1} - link to somewhere {$email} - will be replaced to email address',format_texts_ro=>1,vals=>[format_texts=>2]]}
{$owner=['owner_type'=>'system/demoinputs']}


{include file="elements/input_select_mailtemplate.tpl" name=demo_select_mailtemplate}
{include file="elements/input.tpl" name=demo_htmlarea type=htmlarea height="100px"}
{include file="elements/input.tpl" name=demo_textarea type=textarea  height="100px"}

{include file="elements/input.tpl" 
	name=demo_attachments 
	type=attachments 
	valid=[image=>[storewh=>'2000x1500',minwh=>'1x1',maxwh=>'6000x6000'],limit=>5]
	preview=[thumb=>'50x50']
}


{include file="elements/input.tpl" name=demo_tags type=tags}

{*
{include 
	file="elements/input_select_edit.tpl" 
	name=demo_select_ajax_load type=select
	empty_option=1
	datasource=$app->buildUri('datasources/languages',['native'=>'1'])
}
*}
		{include file="elements/input.tpl"
			name="demo_select_ajax_load"
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




{*
	after_input=$addnew 
	urlArgsAddFunc="setUpCompositionPartSearchArgs()"
*}

{include file="elements/input.tpl" 
	name="demo_search_ajax" 
	type=select_ajax 
	maximumSelectionLength=1
	options=[]
	preload=1
	datasource=$app->buildUri('datasources/languages/search') 
	emptyoption=1
}
	
{include file="elements/input.tpl" name=demo_code_smarty type=code_smarty  height="100px"}
{include file="elements/input.tpl" name=demo_code_json type=code_json  height="100px" layout=wide}

{include file="elements/input.tpl" name=demo_multilang_text3 type=text i18n=3}
{include file="elements/input.tpl" name=demo_multilang_text4 type=text i18n=4}

{include file="default_form_close.tpl" submit_buttons=[save]}