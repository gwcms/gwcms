{if $app->user && $app->user->is_admin}
	{$env_switcher=$app->getEnvironmentSwitcherData()}

	{if $env_switcher}
	<li class="dropdown envselector">
		<a class="dropdown-toggle" href="#" data-toggle="dropdown" title="Environment: {$env_switcher.domain|escape}">
			<span class="label label-{if GW::s('PROJECT_ENVIRONMENT') == $smarty.const.GW_ENV_PROD}primary{elseif GW::s('PROJECT_ENVIRONMENT') == $smarty.const.GW_ENV_TEST}danger{else}warning{/if}" style="font-size:11px;">
				{$env_switcher.current|escape}
			</span>
		</a>
		<ul class="head-list dropdown-menu">
			{foreach $env_switcher.items as $env_id => $env_item}
				{if !$env_item.active}
					<li>
						<a href="{$env_item.url|escape}" title="{$env_item.url|escape}">
							<span class="label label-{if $env_item.key == 'PROD'}primary{elseif $env_item.key == 'TEST'}danger{else}warning{/if}" style="display:inline-block;width:42px;text-align:center;font-size:11px;margin-right:7px;">
								{$env_item.key|escape}
							</span>
							<span class="lang-name">
								{$env_item.display_host|escape}
							</span>
						</a>
					</li>
				{/if}
			{/foreach}
		</ul>
	</li>
	{/if}
{/if}
