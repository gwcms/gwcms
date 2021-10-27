{include "default_open.tpl"} 


{if $debug_table}
{GW_Data_to_Html_Table_Helper::doTable($debug_table)} {/if}


<form method="post"><input type="hidden" name="act"
	value="do:Import" />

<table id="gwTable" style="width: 100%">
	<tr>
		<td style="width: 150px">Paveiksliuku katalogas</td>
		<td>{include file="elements/inputs/text.tpl" input_name=picture_folder value="vasara2014"}
		</td>
	</tr>
	<tr>
		<td style="width: 150px">Importuoti duomenis <small></>Atidarykite
		spreadsheet dokumentą, pažymėkite viską (Ctrl+A), nukopijuokite
		(Ctrl+C) ir gryžę čia į laukelį įklijuokite (Ctrl+V)<small></td>
		<td>{include file="elements/inputs/textarea.tpl" input_name=data}
		</td>
	</tr>

	<tr>
		<td>Aktyvuoti po įkėlimo</td>
		<td><input type="checkbox" name="activate"></td>
	</tr>
</table>

<small>
<p><b>Atpažystami stulpeliai:</b>
{implode(array_keys($m->import_field_translations),', ')}</p>

<p>Spreadsheet celėse negali būti eilutės perkėlimo simbolio, jį
pašalinti galima su "Find & Replace" pasirenkant varnele "Regular
expressions" ir į paiešką įrašant "\n" pakeisti į: " " (tarpo simbolis)
</p>

<p>Produktai atpažystami pagal produkto kodą, taigi jei importuojame
produkta su kodu 001 ir duomenų bazėje egzistuoja produktas su kodu 001
pastarojo visi laukeliai bus atnaujinami</p>


<p>Sezoniškumas galimos vertės {implode(',',$m->lang['SEZONISKUMAS_OPT'])}, TURI ATITIKTI DIDŽIOSIOS IR MAŽOSIOS RAIDĖS</p>
<p>Gamintojas galimos vertės {implode(',',$m->lang['VENDOR_OPT'])}, TURI ATITIKTI DIDŽIOSIOS IR MAŽOSIOS RAIDĖS</p>
<p>Kategorija galimos vertės {implode(',',$m->lang['CATEGORY_OPT'])}, TURI ATITIKTI DIDŽIOSIOS IR MAŽOSIOS RAIDĖS</p>

</small> <input type="submit" /></form>
{include "default_close.tpl"}
