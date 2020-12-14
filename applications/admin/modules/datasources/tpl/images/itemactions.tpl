{$addlitag=1}{$autocaption=1}

{if $smarty.get.frompreview}
	{$tag_params=[target=>"_blank"]}
{/if}



{list_item_action_m url=["{$item->id}/form",[id=>$item->id]] iconclass="fa fa-pencil-square-o"}
{list_item_action_m url=["`$item->id`/crop"] iconclass="fa fa-crop"}

{*action_addclass="ajax-link"*}
{list_item_action_m url=["{$item->id}/form",[act=>doRotate,id=>$item->id]]  caption=GW::l('/m/ROTATE_CLOCKWISE') iconclass="fa fa-rotate-right"}

{*
{if $item->encrypted}
	
	{list_item_action_m url=[false,[act=>doDecrypt,id=>$item->id]] iconclass="fa fa-unlock" caption="Decrypt"}
	
{/if}
*}

{if !$smarty.get.frompreview}
<li class="divider"></li>


{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE')}
{/if}
