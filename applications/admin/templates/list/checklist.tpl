

<br />
<div id="checklist_actions" style="display:none">

	<select name="action" onchange="eval(this.value);
						this.selectedIndex = 0">
		<option value="">{$lang.CHECKLIST_SELECT_ACTION}</option>
		{foreach $dl_checklist_actions as $action}
			{$action}
		{/foreach}
		{*
		<option value="if(!confirm('Turbut šis veiksmas iššauktas per klaidą! Ar norite atšaukti užsakymų trinimą?'))gw_checklist.submit('delete')">!Trinti</option>
		*}
	</select>
</div>

{capture append="footer_hidden"}
	<script type="text/javascript">
		gw_checklist.init();
	</script>
{/capture}
