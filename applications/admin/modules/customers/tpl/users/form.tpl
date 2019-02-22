{include file="default_form_open.tpl"}


{call e field=username}
{call e field=email}
{call e field=name}
{call e field=surname}
{call e field=phone}
{call e field=description type=textarea height=100px}
{call e field=active type=bool}
{call e field=session_validity type=select options=$m->lang.SESSION_VALIDITY_OPT}

{call e field=pass_new  type=pass_visible title=$m->lang.FIELDS.pass}
{call e field=sms_allow_credit type=bool}
{call e field=sms_funds}

{call e field=sms_pricing_plan type=select options=$options.sms_pricing_plan empty_option=1}

{*
{call e field=sms_gates type="multiselect" options=[labas=>labas, routesms=>routesms]}
*}

{include file="default_form_close.tpl" extra_fields=[id,funds]}
