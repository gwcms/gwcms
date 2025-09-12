
{include "head.tpl"}

<table class="gwTable">
	<tr>
		<th>id</th>
		<th>time</th>
		<th>new / diff</th>
		<th>prev</th>
		<th>user</th>
		<th><i class="fa fa-cog"></i></th>
	</tr>
	
{foreach $changes as $key => $meta}
	{$change= $meta.0}
	<tr {if $smarty.get.changeid==$change->id}class='lastvisited'{/if}>
	
		
		
		<td>
			{$change->id}
		</td>
		<td>
			{$change->insert_time}
		</td>
		
		<td>
			{$meta.1|truncate:200}
		</td>
		<td>
			{$meta.2|truncate:200}
		</td>
		<td>{$change->user_id}</td>
		<td>
			{if $meta.1 && !$meta.2}
				
				<a href='{$m->buildUri("{$item->id}/version",[changeid=>$change->id,field=>$smarty.get.field,ln=>$smarty.get.ln,clean=>2])}'>
					<i class="fa fa-eye"></i>
				</a>
			{/if}
		</td>
		
	</tr>	
{/foreach}

</table>

<style>
	.lastvisited{ color: #fffacd; }
</style>