{include file="default_form_open.tpl"}

{$nowrap=1}

{include file="tools/table_header.tpl" title=$m->lang.PUBLIC}

{call e field=thunmbnails_size hidden_note=$lang.FIELD_NOTES.image_dimensions}
{call e field=display_size hidden_note=$lang.FIELD_NOTES.image_dimensions}
{call e field=page_by}

</table>
<br />
<table  class="gwTable">


{include file="tools/table_header.tpl" title=$m->lang.CMS}

{call e field=adm_list_style type=select options=$m->lang.GALLERY_ADM_STYLE_OPT}
{call e field=store_size hidden_note=$lang.FIELD_NOTES.image_dimensions}
{call e field=adm_thunmbnails_size hidden_note=$lang.FIELD_NOTES.image_dimensions}
{call e field=enable_description type=bool}
{call e field=enable_author type=bool}


{include file="default_form_close.tpl" submit_buttons=[save]}