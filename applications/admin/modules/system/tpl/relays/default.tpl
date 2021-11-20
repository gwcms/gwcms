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


{for $i=1; $i<=(int)$m->cfg->relays_toogle_to;$i++}
	<table class="gwTable">
		<tr>
			<td>{$i}</td>
			<td>{(int)$states[$i]}</td>
			<td>
				{if $states[$i]}{$otherstate=0}{else}{$otherstate=1}{/if}
				
				<a class="btn {if $states[$i]}btn btn-success{else}btn btn-default{/if}" href="{$m->buildUri('', [act=>doSet, id=>$i, state=>$otherstate])}">
					{if $states[$i]}on{else}off{/if}
				</a>
			</td>
		</tr>
	</table>
{/for}



{include file="default_close.tpl"}