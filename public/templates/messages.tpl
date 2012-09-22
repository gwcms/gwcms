{if $smarty.session.messages}
	
	{$classes=[0=>'success', 1=>'warning', 2=>'error', 3=>'info']}
	
	
	{foreach $smarty.session.messages as $msg}
		{$msg_type_id=$msg.0}
		<div class="{$classes.$msg_type_id}_msg">
			{GW_Public_Error_Message::read($msg.1)}
		</div>
	{/foreach}
	{GW::$request->removeMessages()}
{/if}