{include file="default_form_open.tpl"}

{include file="elements/input.tpl" name=parent_id type=select options=$item->getParentOpt() default=$smarty.get.pid}

{include file="elements/input.tpl" name=type type=select options=$m->lang.GALLERY_ITEM_TYPE_OPT}


{include file="elements/input.tpl" name=text type=htmlarea ck_options=minimum}

{$curr=date('Y-m-d H:i:s')}
{include file="elements/input.tpl" name=time default=$curr}



{include file="default_form_close.tpl"}