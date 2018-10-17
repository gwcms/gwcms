<div id="gw_dialog_options" style="display:none">
	<div id="gw_dialog_buttons">
		<button onclick="$('#changestatus').submit();">{GW::l('/g/EXECUTE')}</button>
		<button onclick="gw_dialog.close()">{GW::l('/g/CANCEL')}</button>		
	</div>
	<div id="title">{$tmp=basename($app->path)}{GW::l("/A/VIEWS/`$tmp`")}</div>
</div>


<form id="changestatus"  action="{$smarty.server.REQUEST_URI}" method="post" >

<input type="hidden" name="act" value="doDialogMoveItemsSubmit" />
<input type="hidden" name="ids" value="{implode(',', $ids)}" />


<table class="gwTable">

<tr><td>{GW::l('/m/SELECTED_FILES_FOLDERS')}</td><td>{count($ids)}</td></tr>
<tr><td>{GW::l('/m/SELECT_DEST_FOLDER')}</td><td>
	<select name="destination">
		<option value="">{GW::l('/g/EMPTY_OPTION/0')}</option>
		<option value="/">--{GW::l('/m/ROOT_DIR')}--</option>
		{html_options options=$destination_list}
	</select>
</td></tr>



</form>
