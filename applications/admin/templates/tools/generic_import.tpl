{include "default_open.tpl"} 


{if $debug_table}
{GW_Data_to_Html_Table_Helper::doTable($debug_table)} {/if}


<form method="post"><input type="hidden" name="act"
	value="do:ImportData" />

<table id="gwTable" style="width: 100%">
	{*
	<tr>
		<td style="width: 150px">Paveiksliuku katalogas</td>
		<td>{include file="elements/inputs/text.tpl" input_name=picture_folder value="vasara2014"}
		</td>
	</tr>
	*}
	<tr>
		<td style="width: 150px">Importuoti duomenis <small></>Atidarykite
		spreadsheet dokumentą, pažymėkite viską (Ctrl+A), nukopijuokite
		(Ctrl+C) ir gryžę čia į laukelį įklijuokite (Ctrl+V)<small></td>
		<td>{include file="elements/inputs/textarea.tpl" input_name=data value=$data}
		</td>
	</tr>

</table>

<small>
<p>

<b>Atpažystami stulpeliai:</b>
{implode(array_keys($fields),', ')}

</p>

<p>
	Importuojamų duomenų šablonui galima panaudoti <a href="{$app_base}{$app->ln}/{implode('/',$m->module_path)}/exportdata">Eksporto</a> veiksmą
</p>


</small> <input type="submit" /></form>
{include "default_close.tpl"}
