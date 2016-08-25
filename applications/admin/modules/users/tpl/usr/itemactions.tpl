

<li>
{list_item_action_m url=['messages/form',[item=>[user_id=>$item->id],[level=>1]]] iconclass="fa fa-envelope-o" caption=GW::l('/m/VIEWS/message')}
</li>


<li>
	{list_item_action_m url=[false,[act=>doSwitchUser,id=>$item->id]] iconclass="fa fa-sign-in" caption=GW::l('/m/LOGIN_AS')}
</li>

<li>
	{list_item_action_m url=["`$item->id`/iplog",[id=>$item->id]] iconclass="fa fa-history" caption=GW::l('/m/VIEWS/iplog')}
</li>