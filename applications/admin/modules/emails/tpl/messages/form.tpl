{include file="default_form_open.tpl" form_width="100%"}
{$width_title="150px"}

{$f="elements/input.tpl"}


{if !$item->id}
	{*naujam irasui pazymet dabar admino naudojama kalba*}
	{$item->set(lang,1)}
{/if}

{include file=$f name=lang type=bool i18n=3 i18n_expand=1 hidden_note=$m->lang.FIELD_NOTE.lang}

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








{include file=$f name=title note="(Nesiunčiama)"}
{include file=$f name=comments type=textarea note="(Nesiunčiama)" autoresize=1 height=40px}



{include file=$f name=sender hidden_note=$m->lang.email_note default=$m->config->default_sender i18n=4}
{include file=$f name=replyto hidden_note=$m->lang.email_note  default=$m->config->default_replyto}
{include file=$f name=subject i18n=4}


{*
{$ck_options=[toolbarStartupExpanded=>false, autoParagraph=>false, contentsCss=>'applications/admin/css/newsletter.css']}
*}

{include file=$f type=htmlarea name=body remember_size=1 i18n=4}
{include file=$f 
	name=groups type=multiselect options=$options.groups
	hidden_note=GW::l('/m/FIELD_NOTE/groups_or_recipients')
	note=GW::l('/m/FIELD_NOTE/optional_select')
}




{call "e" 
	after_input_f="editadd"
	field="recipients_ids"
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
}



{if $app->user->isRoot()}
	{include file=$f type=select name=status options=$m->lang.OPT.status}
{/if}



{include file=$f name=attachments type=attachments 
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