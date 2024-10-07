<table>
<tr>
	<td>{GW::l('/g/ORDER_BY')}:</td>
	<td id="order-container">
		<select class="order-sel" onchange="gw_adm_sys.order_change()">
			<option value="">{GW::l('/g/EMPTY_OPTION/0')}</option>
		{foreach $fields as $name => $labelid}
			<option value="{$name} ASC">{$m->fieldTitle($labelid)} (123)</option>
			<option value="{$name} DESC">{$m->fieldTitle($labelid)} (321)</option>
		{/foreach}
		</select>
	</td>
	<td>
		<a href="#add_level" onclick="gw_adm_sys.order_add_level();return false" >
			<img src="{$app->icon_root}action_add.png" />
		</a>
	</td>	
</tr>


<script type="text/javascript">
	gw_adm_sys.order_init({$app->fh()->out(json_encode(explode(',',$m->list_params.order)))});
</script>