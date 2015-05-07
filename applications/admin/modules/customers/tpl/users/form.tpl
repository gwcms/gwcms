{include file="default_form_open.tpl"}


{include file="elements/input.tpl" name=username}
{include file="elements/input.tpl" name=email}
{include file="elements/input.tpl" name=name}
{include file="elements/input.tpl" name=surname}
{include file="elements/input.tpl" name=phone}
{include file="elements/input.tpl" name=description type=textarea height=100px}
{include file="elements/input.tpl" name=active type=bool}
{include file="elements/input.tpl" name=session_validity type=select options=$m->lang.SESSION_VALIDITY_OPT}

{include file="elements/input.tpl" name=pass_new  type=pass_visible title=$m->lang.FIELDS.pass}
{include file="elements/input.tpl" name=sms_allow_credit type=bool}
{include file="elements/input.tpl" name=sms_funds}

{include file="elements/input.tpl" name=sms_pricing_plan type=select options=$opt.sms_pricing_plan empty_option=1}

{*
{include file="elements/input.tpl" name=sms_gates type="multiselect" options=[labas=>labas, routesms=>routesms]}
*}

{include file="default_form_close.tpl" extra_fields=[id,funds]}
