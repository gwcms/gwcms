{include file="default_form_open.tpl"}

{include file="elements/input.tpl" name=title}

{include file="elements/input.tpl" name=sender hidden_note="Sender title &lt;email@address.com&gt;"}
{include file="elements/input.tpl" name=subject_lt}
{include file="elements/input.tpl" name=subject_en}
{include file="elements/input.tpl" name=subject_ru}

{$ck_options=[toolbarStartupExpanded=>false]}
{include file="elements/input.tpl" type=htmlarea name=body_lt}
{include file="elements/input.tpl" type=htmlarea name=body_en}
{include file="elements/input.tpl" type=htmlarea name=body_ru}


{$recipients_note="Formatas 1 gavėjas per eilutę: Vardas Pavardė;El pašto adresas<br>Pvz: <br/>Jonas Jonaitis;jonas.jonaitis@gmail.com<br/>Antanas Antanaitis;antanas@yahoo.com"}
{include file="elements/input.tpl" type=textarea name=recipients_lt hidden_note=$recipients_note}
{include file="elements/input.tpl" type=textarea name=recipients_en hidden_note=$recipients_note}
{include file="elements/input.tpl" type=textarea name=recipients_ru hidden_note=$recipients_note}



{include file="default_form_close.tpl"}