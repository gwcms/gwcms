{include file="default_form_open.tpl"}

{*$nowrap=1*}


{call e field=notify_mail type=text}

{call e field=recapPublicKey hidden_note="leave this field empty to disable feature <a href='https://www.google.com/u/3/recaptcha/admin/site/437873903'>[hit here to add site]</a> vidmantas.work@gmail.com" hidden_note_copy=1}
{call e field=recapPrivateKey}

{call e field=appointment_topic_ids type=multiselect_ajax modpath="datasources/classificators" source_args=[group=>appointment_topic] after_input_f=editadd preload=1 sorting=1 options=[] value_format=json1}


{include file="default_form_close.tpl" submit_buttons=[save]}