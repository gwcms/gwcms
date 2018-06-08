<p>
	Spreadsheet celėse negali būti eilutės perkėlimo simbolio, jį
	pašalinti galima su "Find & Replace" pasirenkant varnele "Regular
	expressions" ir į paiešką įrašant "\n" pakeisti į: " " (tarpo simbolis)
</p>

<p>	
	Prenumeratoriai atpažystami pagal El. pašto adresą
</p>

<p>
	Naujienlaiškių grupių identifikaciniai numeriai:
	
	<table>
	{foreach $options.groups as $id => $groupname}	
		<tr><td>{$id}</td><td>-</td><td>{$groupname}</td></tr>
	{/foreach}
	</table>
</p>