{include file="default_form_open.tpl" form_width="1000px"}

{if $m->filters.type}
	{call e field=type type=select options=$options.classtypes empty_option=1 value=$m->filters.type readonly=1}
{else}
	{call e field=type type=select options=$options.classtypes empty_option=1}
{/if}

{call e field=key type=text hidden_note="text identificator, for system use, dont use spaces tabs, example if city type: Paris: paris,San José: san_jose St. George’s: st_georges "}
{call e field=title type=text i18n=4}
{call e field=text type=textarea height="100px" i18n=4 autoresize=1}
{call e field=active type=bool}


{if $app->user->isRoot()}
	{call e field="user_id" type=select_ajax modpath="customers/users"  preload=1 options=[]  empty_option=1}
{/if}




{include file="default_form_close.tpl" extra_fields=[id,insert_time,update_time,priority,user_title]}