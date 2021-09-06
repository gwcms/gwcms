<div id="gw_dialog_options" style="display:none">
	<div id="gw_dialog_buttons">
		<button onclick="$('#changestatus').submit();">Vykdyti</button>
		<button onclick="gw_dialog.close()">Atšaukti</button>		
	</div>
	<div id="title">{$tmp=basename($app->path)}{GW::ln("/A/VIEWS/`$tmp`")}</div>
</div>


<form id="changestatus"  action="{$smarty.server.REQUEST_URI}" method="post" >

<input type="hidden" name="act" value="do:dialogremove" />
<input type="hidden" name="ids" value="{$smarty.get.ids}" />

Pasirinkta {$items_count}, patvirtinkite pasirinkto/ų įrašo/ų šalininimą
</form>