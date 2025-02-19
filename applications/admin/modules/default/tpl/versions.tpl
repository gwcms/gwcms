
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
	<tr>
	
		{$change= $meta.0}
		
		<td>
			{$change->id}
		</td>
		<td>
			{$change->insert_time}
		</td>
		
		<td>
			{$meta.1}
		</td>
		<td>
			{$meta.2}
		</td>
		<td>{$change->user_id}</td>
		<td>
			{if $meta.1 && !$meta.2}
				<i class="fa fa-eye"></i>
			{/if}
		</td>
		
	</tr>	
{/foreach}

</table>