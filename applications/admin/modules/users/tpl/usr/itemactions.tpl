{$addlitag=1}


{dl_actions_edit}

{list_item_action_m url=['messages/form',[item=>[user_id=>$item->id],[level=>1]]] iconclass="fa fa-envelope-o" caption=GW::l('/m/VIEWS/message')}

{list_item_action_m href=$m->buildUri("`$item->id`/pushmessage",[id=>$item->id,clean=>2]) iconclass="fa fa-bell-o" caption="Push notification" action_addclass="iframeopen"}
{list_item_action_m href=$m->buildUri("users/userspushsubscriptions",[user_id=>$item->id,clean=>2]) iconclass="fa fa-list-alt" caption="Push subscriptions" action_addclass="iframe-under-tr"}
{* 
{list_item_action_m url=[false,[act=>doTestPushNotification,id=>$item->id]] iconclass="fa fa-bell" caption="Test push notification"}
*}



{list_item_action_m href=$app->buildUri('datasources/sms',['number'=>$item->phone,clean=>2]) iconclass="fa fa-envelope" caption="SMS ({$item->calcSMS()})" action_addclass="iframeopen"}
{list_item_action_m href=$app->buildUri('datasources/changetrack',[user_id=>$item->id,clean=>2]) iconclass="fa fa-pencil" caption="Pokyčių registras ({$item->calcChangeTrack()})" action_addclass="iframe-under-tr"}


{list_item_action_m url=[false,[act=>doSwitchUser,id=>$item->id]] iconclass="fa fa-sign-in" caption=GW::l('/m/LOGIN_AS')}



{list_item_action_m url=["`$item->id`/iplog",[id=>$item->id]] iconclass="fa fa-history" caption=GW::l('/m/VIEWS/iplog')}


{if $app->user->isRoot()}
	{$last_request_uri=$item->get('keyval/last_request_uri')}
	{if $last_request_uri && strpos($last_request_uri, '/') === 0 && strpos($last_request_uri, '//') !== 0}
		{list_item_action_m href=$last_request_uri|escape iconclass="fa fa-location-arrow text-danger" caption="Jump to last visited uri" action_addclass="text-danger" tag_params=[target=>"_blank"]}
	{/if}
	{list_item_action_m onclick="copyTextToClipboard('`$item->api_key`');return false" iconclass="fa fa-user-secret" confirm=1 caption="Copy api_key to clipboard (root only)"}
{/if}

{list_item_action_m 
	href=$app->buildUri("emails/email_queue",[to=>$item->email,clean=>2]) 
	iconclass="fa fa-envelope-square" action_addclass="iframe-under-tr"  
	caption="išsiųsti laiškai"}
