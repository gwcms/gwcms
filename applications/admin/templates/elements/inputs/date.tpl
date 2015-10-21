<link rel="stylesheet" href="{$app_root}css/datepicker.css" type="text/css" />
<script type="text/javascript" src="{$app_root}js/datepicker.js"></script>
<script type="text/javascript" src="{$app_root}lang/datepicker.php?ln={$app->ln}"></script>

{*$value=$value|default:'Y-m-d'|date*}
{$GLOBALS.input_date_index++}

<input  name="{$input_name}" type="text" value="{$value|escape}" id="datepicker_{$GLOBALS.input_date_index}_b" {if $hidden_note}title="{$hidden_note}"{/if} /> 
<img align="top" class="datepicker_elm" id="datepicker_{$GLOBALS.input_date_index}" src="{$app_root}img/calendar.png" />
