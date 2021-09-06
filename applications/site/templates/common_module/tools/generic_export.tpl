{include "default_open.tpl"} 


{if $debug_table}
{GW_Data_to_Html_Table_Helper::doTable($debug_table)} {/if}

{if $smarty.get.withid}
	<a href="{$app->buildUri()}">-id</a>
{else}
	<a href="{$app->buildUri(false, [withid=>1])}">+id</a>
{/if}

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
	{implode(array_keys($fields),', ')}
</p>

<b>Neleistini simboliai užkoduojami:</b>
<ul>
	<li>[tab] - \t</li>
	<li>[eilutes perkelimo simbolis] - \n arba 2simboliu junginys \r\n</li>
</ul>

<p>
	Pakeistus, papildytus duomenis galima
	<a href="{$app_base}{$app->ln}/{implode('/',$m->module_path)}/importdata">Importuotuoti</a>
</p>



</small>
{include "default_close.tpl"}
