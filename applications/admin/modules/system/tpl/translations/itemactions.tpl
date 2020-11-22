{$addlitag=1}
{if $item->new_size}{$newlines='+'}{else}{$newlines='-'}{/if}

{$iconclass="fa fa-cog"}
{$autocaption=1}



{list_item_action_m url=[tree,[id=>$item->id]]}	




{if $item->newsize}
	{list_item_action_m url=[false,[id=>$item->id,act=>doPushTemp]]}	
	{list_item_action_m url=[false,[id=>$item->id,act=>doResetTemp]]}	
	
	{list_item_action_m url=[xmlmodifications,[id=>$item->id]]}	
{else}
	{list_item_action_m url=[false,[id=>$item->id,act=>doCreateTemp]]}	
{/if}

{list_item_action_m url=[autotranslate,[id=>$item->id]]}	




<li class="divider"></li>
{list_item_action_m url=[false,[act=>doClean,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1}	
