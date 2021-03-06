{include "default_open.tpl"} 


{if $debug_table}
{GW_Data_to_Html_Table_Helper::doTable($debug_table)} {/if}



<table id="gwTable" style="width: 100%">
	{*
	<tr>
		<td style="width: 150px">Paveiksliuku katalogas</td>
		<td>{include file="elements/inputs/text.tpl" input_name=picture_folder value="vasara2014"}
		</td>
	</tr>
	*}
	<tr>
		<td style="width: 150px">Exportuoti duomenis <small> <br />1. Šiame laukelyje pažymėkite viską (Ctrl+A), nukopijuokite
		(Ctrl+C)  <br />2. Atidarykite
		spreadsheet dokumentą ir įklijuokite (Ctrl+V)<small></td>
		<td>{include file="elements/inputs/textarea.tpl" input_name=data value=$data}
		</td>
	</tr>

</table>

<small>
	
<p>
	<b>Eksportuojami stulpeliai:</b>
	{implode(array_keys($m->import_field_translations),', ')}
</p>

<p>
	Pakeistus, papildytus duomenis galima
	<a href="{$app_base}{$app->ln}/{implode('/',$m->module_path)}/import">Importuotuoti</a>
</p>


{include "`$m->tpl_dir`imp_exp_info.tpl"}


</small>
{include "default_close.tpl"}
