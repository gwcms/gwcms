{$addlitag=1}

{*
{list_item_action_m url=[false,[act=>doSomthing,id=>$item->id]] iconclass="fa fa-cog" confirm=1 caption="Human action title"}
*}


{list_item_action_m onclick="copyTextToClipboard('`$payurl``$item->key`')" iconclass="fa fa-link" caption="Copy payment url"}






