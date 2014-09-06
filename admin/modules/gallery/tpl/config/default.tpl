{include file="default_form_open.tpl"}

{$nowrap=1}

{include file="tools/table_header.tpl" title=$m->lang.PUBLIC}

{include file="elements/input.tpl" name=thunmbnails_size hidden_note=$lang.FIELD_NOTES.image_dimensions}
{include file="elements/input.tpl" name=display_size hidden_note=$lang.FIELD_NOTES.image_dimensions}
{include file="elements/input.tpl" name=page_by}

</table>
<br />
<table  class="gwTable">


{include file="tools/table_header.tpl" title=$m->lang.CMS}

{include file="elements/input.tpl" type=select name=adm_list_style options=$m->lang.GALLERY_ADM_STYLE_OPT}
{include file="elements/input.tpl" name=store_size hidden_note=$lang.FIELD_NOTES.image_dimensions}
{include file="elements/input.tpl" name=adm_thunmbnails_size hidden_note=$lang.FIELD_NOTES.image_dimensions}
{include file="elements/input.tpl" name=enable_description type=bool}
{include file="elements/input.tpl" name=enable_author type=bool}


{include file="default_form_close.tpl" submit_buttons=[save]}