{$addlitag=1}

{*
{list_item_action_m url=[false,[act=>doSomthing,id=>$item->id]] iconclass="fa fa-cog" confirm=1 caption="Human action title"}
*}


{capture assign=code}{literal}{{/literal}GW::ln("/{$item->module}/{$item->key}"){literal}}{/literal}{/capture}
{list_item_action_m onclick="copyTextToClipboard('`$code`');"|escape iconclass="fa fa-link" caption="Copy url to clipboard code"}



{if $app->user->isRoot()}
	{list_item_action_m url=[false,[act=>doTransShare,id=>$item->id]] iconclass="fa fa-cog" confirm=1 caption="Send to central translations DB"}
{/if}



