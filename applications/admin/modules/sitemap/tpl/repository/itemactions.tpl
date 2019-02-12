{$addlitag=1}

{*
{list_item_action_m url=[false,[act=>doSomthing,id=>$item->id]] iconclass="fa fa-cog" confirm=1 caption="Human action title"}
*}


{list_item_action_m onclick="copyTextToClipboard('`$item->url`')" iconclass="fa fa-link" confirm=1 caption="Copy url to clipboard"}






