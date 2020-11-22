{include "common.tpl"}

{function name=do_toolbar_buttons_langedit}
	{toolbar_button title=GW::l('/A/VIEWS/langedit') iconclass='gwico-Create-New' href=$m->buildUri(langedit)}
{/function}

{$do_toolbar_buttons[] = langedit}


{include file="default_form_open.tpl" action=Scaffold form_width="100%"}



{call e field=config type=code_json height=700px nopading=1 layout=wide}  




{include file="default_form_close.tpl"}