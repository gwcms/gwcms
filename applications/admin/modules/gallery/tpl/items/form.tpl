{include file="default_form_open.tpl"  form_width="100%"}

<style>
	#gw_input_item__title__ .ln_contain_3{ width: {round(100/count(GW::$settings.LANGS),2)-0.3}% }
</style>	
{$width_title=100px}

{call e field=parent_id type=select options=$item->getParentOpt() default=$smarty.get.pid}

{call e field=type type=select options=GW::l('/m/GALLERY_ITEM_TYPE_OPT')}

{call e field=image type=image note=$app->fh()->maxUploadSize()}
{call e field=title i18n=3 i18n_expand=1 }

{if $item->config()->enable_description}
	{call e field=description type=textarea height="100px" i18n=4}
{/if}
{if $item->config()->enable_author}
	{call e field=author}
{/if}

{if $item->type==$smarty.const.GW_GALLERY_ITEM_FOLDER}

	{call e field="site_id"
		type="select_ajax"
		modpath="sitemap/sites"
		options=[]
		preload=1
	}
{/if}


{call e field=active type=bool}




{include file="default_form_close.tpl"}