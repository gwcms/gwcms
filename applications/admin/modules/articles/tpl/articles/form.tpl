{include file="default_form_open.tpl" form_width="1000px"}


{call e field="group_id" type="select_ajax" modpath="articles/groups"  options=[] after_input_f="editadd" preload=1}

{call e field=image  type=image title=GW::l('/g/IMAGE')}
{call e field=title i18n=4}
{call e field=short type=textarea height=70px  i18n=4}

{call e field=text type=htmlarea layout=wide  i18n=4}
{call e field=active type=bool}



{if $m->modconfig->attachmentsi18n}
	{$atti18n=4}
{/if}

{call e field=attachments type=attachments 
	valid=[image=>[storewh=>'2000x1500',minwh=>'1x1',maxwh=>'6000x6000'],limit=>99]
	preview=[thumb=>'50x50']
	i18n=$atti18n
}



{call e field=priority type=number}



{include file="default_form_close.tpl"}