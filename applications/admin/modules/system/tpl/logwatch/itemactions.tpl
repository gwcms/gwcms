{$addlitag=1}
{if $item->new_size}{$newlines='+'}{else}{$newlines='-'}{/if}

{$iconclass="fa fa-cog"}
{$autocaption=1}


{list_item_action_m url=[newlines,[id=>$item->id]] caption="{GW::l('/m/VIEWS/newlines')} ({$newlines})"}
{list_item_action_m url=[realtime,[id=>$item->id]]}


{list_item_action_m onclick="gwcms.open_rtlogview('`$item->id`');" x=RTM caption="Realtime in modal window"}

{list_item_action_m url=[entire,[id=>$item->id]]}	

<li class="divider"></li>
{list_item_action_m url=[false,[act=>doClean,id=>$item->id]] x=Cl iconclass="fa fa-trash-o text-danger" confirm=1}	
