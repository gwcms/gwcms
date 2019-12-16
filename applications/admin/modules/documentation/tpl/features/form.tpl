{include file="default_form_open.tpl" form_width="99%" changes_track=1}

{*
{call e field=parent_id type=select options=$item->getParentOpt() default=$smarty.get.pid}
*}

{call e field=type type=select_ajax modpath="documentation/types" after_input_f=editadd preload=1 options=[]}


{call e field=title}


{call e field=text type=htmlarea  layout=wide}

{$curr=date('Y-m-d H:i:s')}
{call e field=time default=$curr}





{include file="default_form_close.tpl" submit_buttons=[apply]}