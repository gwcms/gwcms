{include "default_form_open.tpl"}





{call e field=blocks_filter_by_site type=bool}

{call e field="additfields" type=multiselect options=GW::l('/m/OPTIONS/additfields')}


{include file="default_form_close.tpl" submit_buttons=[save]}