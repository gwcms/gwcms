{$addlitag=1}

{*
{list_item_action_m url=[false,[act=>doSomthing,id=>$item->id]] iconclass="fa fa-cog" confirm=1 caption="Human action title"}
*}
{list_item_action_m onclick="copyTextToClipboard('`$item->url`');return false"|escape iconclass="fa fa-link" confirm=1 caption="Copy url to clipboard"}


{if $item->isdir}
	{list_item_action_m url=[false,[act=>doDownloadZiped,id=>$item->id]] iconclass="fa fa-file-archive-o" confirm=1 caption="Download zipped"}
	
	{list_item_action_m url=[false,[act=>doConvert2webpDir,id=>$item->id]] iconclass="material inbox_customize" confirm=1 caption="Convert images to .WEBP"}
	
{else}
	{if in_array($item->extension_lc,[jpg,jpeg,png])}
		{list_item_action_m url=[false,[act=>doConvert2webp,id=>$item->id]] iconclass="material inbox_customize" confirm=1 caption="Convert image to .WEBP"}
	{/if}
		
	
	{if $item->type==image}
		{list_item_action_m url=["crop", [id=>$item->id]] iconclass="fa fa-crop" caption=GW::l('/m/VIEWS/crop')}

		{*action_addclass="ajax-link"*}
		{list_item_action_m url=[false,[act=>doRotate,id=>$item->id]]  caption=GW::l('/m/ROTATE_CLOCKWISE') iconclass="fa fa-rotate-right"}	
	{/if}
	
	{if $item->extension==pdf}
		{list_item_action_m url=[false,[act=>doPdfToImage,id=>$item->id]]  caption="pdf to image" iconclass="fa fa-cog"}	
	{/if}	
	
	
{/if}







