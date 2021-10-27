{include file="common.tpl"}
		
{$dlgCfg2MWdth=300}
{$do_toolbar_buttons_hidden=[dialogconf2]}		
{$do_toolbar_buttons[]=hidden}
	
{include file="default_open.tpl"}


	<div style="padding:50px;background-color:#F5F5F5;border:silver;">

{if $test_actions}
	Test actions:<br><br>
<ul>
{foreach $test_actions as $act}
	<li>
		<a href="{$m->buildURI(false,[act=>$act.0])}"><i class="fa fa-cog"></i> {$act.0}</a> {if $act.1.info}<i style="color:silver">({$act.1.info})</i>{/if}
	</li>
{/foreach}

</ul>
{/if}

</div>




{include file="default_close.tpl"}