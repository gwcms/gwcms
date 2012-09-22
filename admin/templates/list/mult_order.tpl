<table>
<tr>
	<td>{$lang.ORDER_BY}:</td>
	<td id="order-container">
		<select class="order-sel" onchange="gw_adm_sys.order_change()">
			<option value="">{$lang.EMPTY_OPTION.0}</option>
		{foreach $fields as $name => $labelid}
			<option value="{$name} ASC">{FH::fieldTitle($labelid)} (123)</option>
			<option value="{$name} DESC">{FH::fieldTitle($labelid)} (321)</option>
		{/foreach}
		</select>
	</td>
	<td>
		<a href="#add_level" onclick="gw_adm_sys.order_add_level();return false" >
			<img src="img/icons/action_add.png" />
		</a>
	</td>	
</tr>


<script>
	gw_adm_sys.order_init({FH::out(json_encode(explode(',',$m->list_params.order)))});
</script>