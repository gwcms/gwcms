{include "default_open.tpl"} 


{if $debug_table}
{GW_Data_to_Html_Table_Helper::doTable($debug_table)} {/if}


<form method="post"><input type="hidden" name="act"
	value="do:Import" />

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
		<td>{include file="elements/inputs/textarea.tpl" input_name=data}
		</td>
	</tr>

	<tr>
		<td style="width: 150px">Importuoti tik naujus <small>(neatnaujinti egzistuojančių)</small></td>
		<td>{include file="elements/inputs/bool.tpl" input_name=insert_only}
		</td>
	</tr>	
	<tr>
		<td style="width: 150px">Atnaujinti tik vardus pavardes</td>
		<td>{include file="elements/inputs/bool.tpl" input_name=update_name_surname_only}
		</td>
	</tr>	
</table>

<small>
<p>

<b>Atpažystami stulpeliai:</b>
{implode(array_keys($m->import_field_translations),', ')}

</p>

<p>
	Importuojamų duomenų šablonui galima panaudoti <a href="{$app_base}{$app->ln}/{implode('/',$m->module_path)}/export">Eksporto</a> veiksmą
</p>


{include "`$m->tpl_dir`imp_exp_info.tpl"}


</small> <input type="submit" /></form>
{include "default_close.tpl"}
