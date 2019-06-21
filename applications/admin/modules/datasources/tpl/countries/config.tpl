


{call e field="eu_countries"
	type="multiselect_ajax"
	object_title=GW::l('/M/datasources/MAP/childs/countries/title')
	modpath="datasources/countries"
	source_args=[byCode=>1]
	options=[]
	preload=1
	value=json_decode($item->eu_countries,true)
}
