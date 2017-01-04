{include file="default_form_open.tpl"}

{include file="tools/lang_select.tpl"}

{include file="elements/input.tpl" name=parent_id type=select options=$item->getParentOpt() default=$smarty.get.pid}

{include file="elements/input.tpl" name=type type=select options=$m->lang.GALLERY_ITEM_TYPE_OPT}

{include file="elements/input.tpl" name=image type=image note=$app->fh()->maxUploadSize()}
{include file="elements/input.tpl" name=title i18n=1 }

{if $item->config()->enable_description}
	{include file="elements/input.tpl" name=description type=textarea height="100px" i18n=1}
{/if}
{if $item->config()->enable_author}
	{include file="elements/input.tpl" name=author}
{/if}


{include file="elements/input.tpl" type=bool name=active}




{include file="default_form_close.tpl"}