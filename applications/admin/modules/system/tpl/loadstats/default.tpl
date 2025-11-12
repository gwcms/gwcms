{include file="default_open.tpl"}





{if $test_actions}
	Test actions:
<ul>
{foreach $test_actions as $act}
	<li>
		<a href="{$m->buildURI(false,[act=>$act.0])}"><i class="fa fa-cog"></i> {$act.0}</a> {if $act.1.info}<i style="color:silver">({$act.1.info})</i>{/if}
	</li>
{/foreach}

</ul>
{/if}


{if $test_views}
	Test views:
<ul>
{foreach $test_views as $view}
	<li>
		<a href="{$m->buildURI($view.0)}"><i class="fa fa-file-code-o"></i> {$view.0}</a> {if $view.1.info}<i style="color:silver">({$view.1.info})</i>{/if}
	</li>
{/foreach}

</ul>
{/if}





{include file="default_close.tpl"}