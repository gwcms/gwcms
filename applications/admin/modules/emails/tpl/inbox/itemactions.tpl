



<li>
	{list_item_action_m url=[false,[act=>doParse,id=>$item->id]] iconclass="fa fa-refresh" caption="{GW::l('/A/VIEWS/doParse')} [D]" shift_button=1}
</li>

<li>
	{list_item_action_m url=[false,[act=>doDownloadMail,id=>$item->id]] iconclass="fa fa-refresh" caption="Pakartoti laiško gavima"}
</li>


<li class="divider"></li>


<li>
	{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE') shift_button=1}
</li>




