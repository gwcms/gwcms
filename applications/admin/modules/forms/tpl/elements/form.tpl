{include file="default_form_open.tpl" form_width="1000px"}
{$width_title=100px}


{*todo: dropdownas is vertimu perrasymu*}
{$editinsite="Vertimai susikurs saite CTLR+Q - redaguoti"}

{*
{call e field="owner_id"}
*}

{call e field="fieldset" hidden_note=$editinsite}
{call e field="fieldname" note="Unik."}
{call e field="title" i18n=4}

{call e field="required" type=bool}
{call e field="type" type=select options=$item->getTypes() empty_option=1 options_fix=1}

{call e field=size type=number default=2}
{call e field=config type=code_json height=200px nopading=1}  

{call e field="hidden_note" i18n=4}
{call e field="note"  i18n=4}
{call e field="placeholder"  i18n=4}

{call e field="active" type=bool}




{include file="default_form_close.tpl"}