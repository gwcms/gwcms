{include "default_form_open.tpl" action=MailS1}

{call e field="recipient" type="text"}

{$opts=[vars_hint=>'/M/PRODUCTS/FIELDS_HELP/invoice',format_texts_ro=>1,vals=>[format_texts=>2]]}
{$owner=['owner_type'=>'competitions/participants','owner_field'=>'participant_communication']}


{include file="elements/input_select_mailtemplate.tpl" field="template_id" 
	default_vals=[admin_title=>"",idname=>client_communication]}


{call e field="use_lang" type=radio options=['en'=>'en','lt'=>'lt','ru'=>'ru'] separator="&nbsp;&nbsp;"}


{function name=df_submit_button_submit}
	<button class="btn btn-primary pull-right"> {$lang.NEXT} {$lang.STEP} <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
{/function}


{include file="default_form_close.tpl" extra_fields=false submit_buttons=[submit]}