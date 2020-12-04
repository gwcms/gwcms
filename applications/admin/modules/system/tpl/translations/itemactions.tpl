{$addlitag=1}
{if $item->new_size}{$newlines='+'}{else}{$newlines='-'}{/if}

{$iconclass="fa fa-cog"}
{$autocaption=1}


{*
{list_item_action_m url=[tree,[id=>$item->id]]}	
*}

{list_item_action_m url=[flatedit,[id=>$item->id]] iconclass="fa fa-pencil-square-o"}	
{list_item_action_m url=[autotranslate,[id=>$item->id]] iconclass="fa fa-language"}



{if $item->newsize}
	<li class="divider"></li>
	{list_item_action_m url=[false,[id=>$item->id,act=>doPushTemp]] iconclass="fa fa-upload"}	
	{list_item_action_m url=[false,[id=>$item->id,act=>doResetTemp]] confirm=1 iconclass="fa fa-trash-o text-danger"}	
	
	{list_item_action_m url=[xmlmodifications,[id=>$item->id]]}	
{else}
	{list_item_action_m url=[false,[id=>$item->id,act=>doCreateTemp]]}	
{/if}

	



