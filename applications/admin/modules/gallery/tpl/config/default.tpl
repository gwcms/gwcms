{include file="default_form_open.tpl"}

{$nowrap=1}

{include file="tools/table_header.tpl" title=GW::l('/m/PUBLIC')}

{call e field=thunmbnails_size hidden_note=GW::l('/g/FIELD_NOTES/image_dimensions')}
{call e field=display_size hidden_note=GW::l('/g/FIELD_NOTES/image_dimensions')}
{call e field=page_by}

</table>
<br />
<table  class="gwTable">


{include file="tools/table_header.tpl" title=GW::l('/m/CMS')}

{call e field=adm_list_style type=select options=GW::l('/m/GALLERY_ADM_STYLE_OPT')}
{call e field=store_size hidden_note=GW::l('/g/FIELD_NOTES/image_dimensions')}
{call e field=adm_thunmbnails_size hidden_note=GW::l('/g/FIELD_NOTES/image_dimensions')}
{call e field=enable_description type=bool}
{call e field=enable_author type=bool}


{include file="default_form_close.tpl" submit_buttons=[save]}