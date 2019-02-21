{include file="default_form_open.tpl"}

{call e field=parent_id type=select options=$item->getParentOpt() default=$smarty.get.pid}

{call e field=type type=select options=$m->lang.GALLERY_ITEM_TYPE_OPT}

{call e field=title}


{call e field=text type=htmlarea}

{$curr=date('Y-m-d H:i:s')}
{call e field=time default=$curr}



{include file="default_form_close.tpl" submit_buttons=[apply]}