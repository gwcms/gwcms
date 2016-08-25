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





						{foreach from=$app->getPages() item=item key=key}

							{$active=($app->path_arr.0.path_clean == $item->pathname)}
							{$childs=$app->getPages([parent_id=>$item->id])}

							{if $item->path=='separator'}
								<li class="list-divider"></li>

								<!--Category name-->
								<li class="list-header">{$item->get(title,$ln)}</li>

							{else}

								<li class="{if $active} active-sub active{/if}">

									<a href="{$app->buildUri($item->path)}">
										{$item->info.icon}
										<span class="menu-title">{$item->get(title,$ln)}</span>
										{if count($childs)}<i class="arrow"></i>{/if}
									</a>

									{if count($childs)}
										{if $active}<!--active-->{/if}
										<ul class="collapse {if $active}in{/if}">
											{foreach from=$childs item=sitem}

												<li {if $app->path_arr.1.path_clean == $sitem->path}class="active-link"{/if}>
													<a href="{$app->buildUri($sitem->path)}">{$sitem->info.icon}  {$sitem->get(title,$ln)}</a>
												</li>
											{/foreach}
										</ul>
									{/if}

								</li>
							{/if}

						{/foreach}

						<!--Category name-->


					</ul>




				</div>
			</div>
		</div>
		<!--================================-->
		<!--End menu-->

	</div>
</nav>
<!--===================================================-->
<!--END MAIN NAVIGATION-->






