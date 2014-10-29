<link rel="stylesheet" href="css/datepicker.css" type="text/css" />
<script type="text/javascript" src="js/datepicker.js"></script>
<script type="text/javascript" src="lang/datepicker.php?ln={$request->ln}"></script>

{*$value=$value|default:'Y-m-d'|date*}
{$GLOBALS.input_date_index++}

<input name="{$input_name}" type="text" value="{$value|escape}" id="datepicker_{$GLOBALS.input_date_index}_b" {if $hidden_note}title="{$hidden_note}"{/if} /> 
<img align="top" class="datepicker" id="datepicker_{$GLOBALS.input_date_index}" src="{$app_root}img/calendar.png" />

