{assign var=first_pageid value=$app->path_arr.0}

<ul id="sub-nav">
	{foreach from=$app->sitemap->map.$first_pageid.childs item=item key=key}
		<li {if $app->path == $item.path}class="current"{/if}>
			<a href="{$app->ln}/{$item.path}"><span>{$item.title}</span></a>
		</li>
	{/foreach}
</ul>