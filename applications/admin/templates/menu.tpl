<!--MAIN NAVIGATION-->
<!--===================================================-->
<nav id="mainnav-container">
	<div id="mainnav">

		<!--Menu-->
		<!--================================-->
		<div id="mainnav-menu-wrap">
			<div class="nano">
				<div class="nano-content">

					<!--Profile Widget-->
					<!--================================-->




					<ul id="mainnav-menu" class="list-group">


						{function menudisplayicon}
							{$item->getIcon()}
						{/function}

						{$listpages=$app->getPages(['childs'=>1])}
						
						{foreach $listpages as $key => $item}

							{$active=($app->path_arr.0.path_clean == $item->pathname)}
							{$childs=$item->childs}

							{if $item->path=='separator'}
								<li class="list-divider"></li>

								<!--Category name-->
								<li class="list-header">{$item->get(title,$ln)}</li>

							{else}
								{*{$GLOBALS.adm_sys_menu[$item->path]=1}*}
								
								<li class="{if $active} active-sub active{/if}">

									{if count($childs)==1}
										{$fitem=current($childs)}
										{$link = $app->buildUri($fitem->path)}
									{else}
										{$link = $app->buildUri($item->path)}
									{/if}
									
									<a href="{$link}">
										{call "menudisplayicon"}
										<span class="menu-title">{$item->get(title,$ln)}</span>
										{if count($childs) >1}<i class="arrow"></i>{/if}
									</a>

									{if count($childs) > 1}
										{if $active}<!--active-->{/if}
										<ul class="collapse {if $active}in{/if}">
											{foreach from=$childs item=sitem}
												{*$GLOBALS.adm_sys_menu[$sitem->path]=1*}
												
												<li {if $app->path_arr.1.path_clean == $sitem->path}class="active-link"{/if}>
													<a href="{$app->buildUri($sitem->path)}">{call "menudisplayicon" item=$sitem} {$sitem->get(title,$ln)}</a>
												</li>
											{/foreach}
										</ul>
									{/if}

								</li>
							{/if}

						{/foreach}

						<!--Category name-->
						

					</ul>

					{$app->processHook('AFTER_MENU')}
					{include "tools/running_tasks.tpl"}

				</div>
			</div>
		</div>
		<!--================================-->
		<!--End menu-->

	</div>
</nav>
<!--===================================================-->
<!--END MAIN NAVIGATION-->






