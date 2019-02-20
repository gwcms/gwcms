{include file="default_form_open.tpl"}

{include file="tools/lang_select.tpl"}

{call e field=parent_id type=select options=$item->getParentOpt() default=$smarty.get.pid}

{call e field=type type=select options=$m->lang.GALLERY_ITEM_TYPE_OPT}

{call e field=image type=image note=$app->fh()->maxUploadSize()}
{call e field=title i18n=1 }

{if $item->config()->enable_description}
	{call e field=description type=textarea height="100px" i18n=1}
{/if}
{if $item->config()->enable_author}
	{call e field=author}
{/if}


{call e field=active type=bool}




{include file="default_form_close.tpl"}