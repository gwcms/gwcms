{assign var=first_pageid value=$request->path_arr.0}

<ul id="sub-nav">
	{foreach from=$request->sitemap->map.$first_pageid.childs item=item key=key}
		<li {if $request->path == $item.path}class="current"{/if}>
			<a href="{$request->ln}/{$item.path}"><span>{$item.title}</span></a>
		</li>
	{/foreach}
</ul>