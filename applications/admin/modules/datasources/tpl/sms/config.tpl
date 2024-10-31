{if $app->user->isRoot()}

	{call e field=gateway type=select options=["gwlt","tele2"] options_fix=1 required=1 empty_option=1}

	{if $item->gateway=="tele2"}
		{call e field=tele2_apikey}
		{call e field=tele2_sender}
	{elseif $item->gateway=="gwlt"}

		{call e field=gwlt_host default="gw.lt"}
		{call e field=gwlt_username}
		{call e field=gwlt_user_id}
		{call e field=gwlt_api_key type=password}


		{call e field=gwlt_route note="leave empty for defaut, 6 - (r1fast) for instant queue"}

	{/if}

{/if}


{if $item->gateway=="gwlt" || $item->gateway==""} 
	{call e field=host default="gw.lt" note="MOVE MANUALY TO gwlt"}
	{call e field=username note="MOVE MANUALY TO gwlt"}
	{call e field=user_id note="MOVE MANUALY TO gwlt"}
	{call e field=api_key type=password note="MOVE MANUALY TO gwlt"}
	{call e field=route note="leave empty for defaut, 6 - (r1fast) for instant queue" note="MOVE MANUALY TO gwlt"}
{/if}