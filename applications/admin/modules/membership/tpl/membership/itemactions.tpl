

{$addlitag=true}

{*is meniu*}
{*		
{$menu=$m->getSubMenu($item)}


{foreach $menu as $menuid => $menuitmR}
	
	{if $menuitmR.childs}
		<li>
			&nbsp;&nbsp;{$menuitmR.caption}
			<ul>
		{foreach $menuitmR.childs as $menuid => $menuitm}
			
			{if $menuitm.count}{$menuitm.caption="`$menuitm.caption` (`$menuitm.count`)"}{/if}
			<li>{list_item_action_m caption=$menuitm.caption href=$menuitm.url  iconclass=$menuitm.iconclass}</li>
		{/foreach}
			</ul>
		</li>
	{else}
		{if $menuitmR.count}{$menuitmR.caption="`$menuitmR.caption` (`$menuitmR.count`)"}{/if}
		<li>{list_item_action_m caption=$menuitmR.caption href=$menuitmR.url  iconclass=$menuitmR.iconclass}</li>
	{/if}
{/foreach}
*}	



{*
<li>
	{list_item_action_m url=["`$item->id`/emails",[competition_id=>$item->id]] iconclass="fa fa-at" caption=GW::l('/m/VIEWS/emails')}
</li>
*}

<li class="divider"></li>


{*{list_item_action_m url=["`$item->id`/createduplicate", [id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}*}
{list_item_action_m url=["`$item->id`/form", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}
{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE') shift_button=1}




