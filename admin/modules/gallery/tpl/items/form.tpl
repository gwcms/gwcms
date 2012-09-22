{include file="default_form_open.tpl"}

{include file="elements/input.tpl" name=parent_id type=select options=$item->getParentOpt() default=$smarty.get.pid}

{include file="elements/input.tpl" name=type type=select options=$m->lang.GALLERY_ITEM_TYPE_OPT}

{include file="elements/input.tpl" name=image type=image note=FH::maxUploadSize()}
{include file="elements/input.tpl" name=title}

{if $item->config()->enable_description}
	{include file="elements/input.tpl" name=description type=textarea height="100px"}
{/if}
{if $item->config()->enable_author}
	{include file="elements/input.tpl" name=author}
{/if}


{include file="elements/input.tpl" type=bool name=active}

</table>

	{include file="tools/form_submit_buttons.tpl"}

</form>
{if $update}
	{include file="extra_info.tpl" extra_fields=[insert_time,update_time]}
{/if}

</div>

{include file="default_close.tpl"}