<div id="firstpane" class="menu_list"> <!--Code for menu starts here-->




{foreach from=$app->getPages() item=item key=key}

	{$active=($app->path_arr.0.path_clean == $item->pathname)}


	<p class="menu_head{if $active} menu_head_active{/if} {if !count($childs)}no_childs{/if}">
		<a href="{$app->buildUri($item->path)}" onclick="return false">
			{$item->info.icon}
			{$item->get(title,$ln)}
		</a>
	</p>

	
	{$childs=$app->getPages([parent_id=>$item->id])}
	
	{if count($childs)}
	<div class="menu_body {if $active} menu_body_active{/if}">
			{foreach from=$childs item=sitem}
					
					<a {if $app->path_arr.1.path_clean == $sitem->path}class="current"{/if} href="{$app->buildUri($sitem->path)}">{$sitem->info.icon}  {$sitem->get(title,$ln)}</a>
			{/foreach}
	</div>
	{/if}

{/foreach}

</div>

