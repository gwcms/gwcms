

<table class="bordered fullwidth">
	<thead>
		<tr>
		    <th scope="col">Prekės tipas</th>
			<th scope="col">Prekės pavadinimas</th>
			<th scope="col">Mato vnt.</th>
			<th scope="col">Kiekis</th>
			<th scope="col">Vieneto kaina</th>
			<th scope="col">Viso</th>
		</tr>
	</thead>
	<tbody>
	    {foreach $ITEMS as $subitem}
		<tr>
		    <td>{$subitem.type}</td>
			<td>{$subitem.title}</td>
			<td>vnt.</td>
			<td>{$subitem.qty}</td>
			<td>{$subitem.unit_price} EUR</td>
			<td>{$subitem.total} EUR</td>
		</tr>
		{/foreach}
	</tbody>
</table>

<p>Iš viso mokėti: <b>{$PRICE} EUR</b> <i>({$PRICE_TEXT})</i><br />

	<style>
		.bordered{  border-collapse: collapse;}
		.bordered td, .bordered th{  border:1px solid #555; padding: 5px 10px 5px 10px  }
	</style>