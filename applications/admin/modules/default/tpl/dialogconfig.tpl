	<script type="text/javascript" src="{$app_root}js/jquery.ui.tabs.js"></script>
	<script type="text/javascript" src="{$app_root}js/jquery.ui.widget.js"></script>

<style>


#sortable { list-style-type: none; margin: 0; padding: 0; }
#sortable input{ margin-right:8px }
#sortable li { margin: 0 5px 5px 5px; padding: 2px; font-size: 0.8em; height: 1em; }
html>body #sortable li { height: 1.5em; line-height: 1em; }

.ui-state-highlight { height: 1em; line-height: 0.8em; }
.ui-state-disabled{ border:1px solid silver }
.form-field * { vertical-align: middle; }



#sortable li { cursor: row-resize }

</style>

<script>
	$(function() {
		$( "#sortable" ).sortable({
			placeholder: "ui-state-highlight"
		});
		$( "#sortable" ).disableSelection();
	});

	$(document).ready(function() {

		$('#sortable li').addClass('ui-state-disabled');
		$('#sortable li input:checked').parent().toggleClass('ui-state-disabled').toggleClass('ui-state-default');

		$('#sortable li input').change(function() {
			var $this = $(this)
			$this.parent().toggleClass('ui-state-disabled').toggleClass('ui-state-default');

			$this.next().val( $this.is(':checked') ? 1 : 0 );
		});

		$('#sortable li').hover(
			function () {
				$(this).addClass("ui-state-hover");
			},
			function () {
				$(this).removeClass("ui-state-hover");
			}
		);
	});

	$(function() {
		$( "#tabs" ).tabs();
	});
	
</script>


<div id="gw_dialog_options" style="display:none">
	<div id="gw_dialog_buttons">
		<button onclick="$('#changestatus').submit();">{$lang.SAVE}</button>
		<button onclick="gw_dialog.close()">{$lang.CANCEL}</button>		
	</div>
	<div id="title">{$lang.LIST_DISPLAY_SETTINGS}</div>
</div>


<form id="changestatus"  action="{$ln}/{$app->path_arr_parent.path}" method="post" >

<input type="hidden" name="act" value="do:dialog_config_save" />
<input type="hidden" id="defaults" name="defaults" value="0" />



<table style="width:auto;margin-top:5px" class="gwTable gwActiveTable">


<tr><th>{$lang.LDS_FIELD_PRIORITY_VISIBILITY}</th></tr>

<tr>
<td>

<ul id="sortable" class="form-field" style="width:200px;margin-top:5px">
	{foreach $fields as $id => $enabled}
		<li>
			<input type="checkbox" {if $enabled}checked{/if} />
			<input type="hidden" name="fields[{$id}]" value="{$enabled|intval}">
		{$app->fh()->fieldTitle($id)}</li>
	{/foreach}
</ul>
</td></tr>


<tr><td><center><button onclick="$('#defaults').val(1);$('#changestatus').submit();">{$lang.RESET_TO_DEFAULTS}</button></center></td></tr>

</table>


</form>