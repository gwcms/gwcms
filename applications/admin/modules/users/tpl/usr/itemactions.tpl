{$addlitag=1}



{list_item_action_m url=['messages/form',[item=>[user_id=>$item->id],[level=>1]]] iconclass="fa fa-envelope-o" caption=GW::l('/m/VIEWS/message')}




{list_item_action_m href=$app->buildUri('datasources/sms',['number'=>$item->phone,clean=>2]) iconclass="fa fa-envelope" caption="SMS ({$item->calcSMS()})" action_addclass="iframeopen"}


{list_item_action_m url=[false,[act=>doSwitchUser,id=>$item->id]] iconclass="fa fa-sign-in" caption=GW::l('/m/LOGIN_AS')}



{list_item_action_m url=["`$item->id`/iplog",[id=>$item->id]] iconclass="fa fa-history" caption=GW::l('/m/VIEWS/iplog')}
