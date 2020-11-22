
{include file="default_form_open.tpl" form_width="900px" action=translatetest}





{call e field="fromln" type=radio options=GW::l('/g/LANG')  separator="&nbsp;&nbsp;&nbsp;"}
{call e field="toln" type=radio options=GW::l('/g/LANG') separator="&nbsp;&nbsp;&nbsp;"}

{call e field="text" type=textarea }
{call e field="result" type=textarea }





{$servopts =[]}
{foreach explode(',',$m->modconfig->service_url_list) as $url}
	{$servopts[$url]=$url}
{/foreach}

{call e field="service_url" type=select options=$servopts value=$m->modconfig->main_service_url empty_option=1 hidden_note="See configuration for list/default change"}






{include file="default_form_close.tpl" submit_buttons=[submit]}