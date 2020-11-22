{include file="default_form_open.tpl" form_width="100%"}
{$width_title="150px"}

{$f="elements/input.tpl"}


{if !$item->id}
	{*naujam irasui pazymet dabar admino naudojama kalba*}
	{$item->set(lang,1)}
{/if}

{call e field=lang type=bool i18n=3 i18n_expand=1 hidden_note=GW::l('/m/FIELD_NOTE/lang')}

{*disablinti atzymetas kalbas*}
{if $item->id}
	{$langs=[]}
	{*show only active langs*}
	{foreach GW::$settings.LANGS as $ln}
		{if $item->get(lang, $ln)}
			{$langs[$ln]=1}
		{/if}
	{/foreach}
{/if}








{call e field=title note="(Nesiunčiama)"}
{call e field=comments type=textarea note="(Nesiunčiama)" autoresize=1 height=40px}



{call e field=sender hidden_note=GW::l('/m/email_note') default=$m->config->default_sender i18n=4}
{call e field=replyto hidden_note=GW::l('/m/email_note')  default=$m->config->default_replyto}
{call e field=subject i18n=4}


{*
{$ck_options=[toolbarStartupExpanded=>false, autoParagraph=>false, contentsCss=>'applications/admin/css/newsletter.css']}
*}

{call e field=body type=htmlarea remember_size=1 i18n=4 hidden_note=GW::l('/m/FIELD_NOTE/message_variables') }
{call e field=groups type=multiselect options=$options.groups
	hidden_note=GW::l('/m/FIELD_NOTE/groups_or_recipients')
	note=GW::l('/m/FIELD_NOTE/optional_select')
}




{call e field="recipients_ids"
	after_input_f="editadd"
	type="multiselect_ajax"
	object_title=GW::l('/m/MAP/childs/subscribers/title')
	form_url=$app->buildUri('emails/subscribers/form',[clean=>2,dialog=>1])
	import_url=$app->buildUri('emails/subscribers/importsimple')
	export_url=$app->buildUri('emails/subscribers/exportsimple')
	datasource=$app->buildUri('emails/subscribers/search') 
	preload=1
	btngroup_width="100%"
	rowclass="recipients"
	hidden_note=GW::l('/m/FIELD_NOTE/groups_or_recipients')
	note=GW::l('/m/FIELD_NOTE/optional_select')
	options=[]
	btnselectall=1
}



{if $app->user->isRoot()}
	{call e field=status type=select options=GW::l('/m/OPT/status')}
{/if}



{call e field=attachments type=attachments 
	valid=[image=>[storewh=>'2000x1500',minwh=>'1x1',maxwh=>'6000x6000'],limit=>5]
	preview=[thumb=>'50x50']
	i18n=4
}


{capture append=footer_hidden}
	<style>
		.recipients.select2, .recipients select
		{
			width: calc(100%-100px);
			margin: 0 auto;
			border-radius: 0 !important;
			display: block;
		}
		.recipients .select2-container .select2-selection {
			max-height: 100px;

			overflow-y: scroll;
		} 

	
	</style>
	
	{*
	<script>
	require(['gwcms'], function(){
		
	
			
			$('#item__details_enabled__').change(function(){
				if($(this).val()==1) {
					$('.detailsCapt').fadeIn();
				}else{
					$('.detailsCapt').hide();
				}				
			}).change();
			
		       
			
	})
</script>
*}
{/capture}


{include file="default_form_close.tpl"}