
{include "head.tpl"}

<style>
	.changetrack-summary-added{
		color:#264409;
		background:#e6efc2;
		display:inline-block;
		padding:1px 4px;
		border-radius:3px;
	}
	.changetrack-summary-removed{
		color:#8a1f11;
		background:#fbe3e4;
		display:inline-block;
		padding:1px 4px;
		border-radius:3px;
	}
</style>

<table class="gwTable">
	<tr>
		<th>{GW::l('/G/changetrack/VERSIONS/ID')}</th>
		<th>{GW::l('/G/changetrack/VERSIONS/TIME')}</th>
		<th>{GW::l('/G/changetrack/VERSIONS/NEW_DIFF')}</th>
		<th>{GW::l('/G/changetrack/VERSIONS/PREV')}</th>
		<th>{GW::l('/G/changetrack/VERSIONS/USER')}</th>
		<th>{GW::l('/G/changetrack/VERSIONS/NOTE')}</th>
		<th><i class="fa fa-cog"></i></th>
	</tr>
	
{foreach $changes as $key => $meta}
	{$change= $meta.0}
	{$change_user=$changes_users[$change->user_id]|default:null}
	{$change_user_title=trim(($change_user->name|default:'')|cat:' '|cat:($change_user->surname|default:''))}
	{if !$change_user_title && $change_user && $change_user->username}
		{$change_user_title=$change_user->username}
	{/if}
	<tr {if $smarty.get.changeid==$change->id}class='lastvisited'{/if}>
	
		
		
		<td>
			{$change->id}
		</td>
		<td>
			{$change->insert_time|date_format:"%Y-%m-%d %H:%M"}
		</td>
		
		<td>
			{if isset($meta.2)}
				{if GW_Change_Track_Render_Helper::shouldUseSummaryLink($meta.2, $meta.1)}
					{$human=GW_Change_Track_Render_Helper::buildHumanSummaryFromTexts($meta.2, $meta.1)}
					<strong>{$human.title|escape}</strong><br>
					{foreach $human.details as $detail}
						{$detail.text|escape}<br>
					{/foreach}
				{else}
					{$meta.1|truncate:200}
				{/if}
			{else}
				{$human=GW_Change_Track_Render_Helper::buildHumanSummaryFromPatch($meta.1)}
				<strong>{$human.title|escape}</strong><br>
				{foreach $human.details as $detail}
					{if $detail.type=="added"}
						<span class="changetrack-summary-added">{$detail.text|escape}</span><br>
					{elseif $detail.type=="removed"}
						<span class="changetrack-summary-removed">{$detail.text|escape}</span><br>
					{else}
						{$detail.text|escape}<br>
					{/if}
				{/foreach}
			{/if}
		</td>
		<td>
			{if isset($meta.2)}
				{if GW_Change_Track_Render_Helper::shouldUseSummaryLink($meta.2, $meta.1)}
					{$meta.2|truncate:120}
				{else}
					{$meta.2|truncate:200}
				{/if}
			{/if}
		</td>
		<td>
			{if $change->user_id}
				<a class="iframeopen" href="{$app->buildUri("users/usr/`$change->user_id`/form",[clean=>2])}" title="User info - {$change_user_title|default:$change->user_id|escape}">
					{if $change_user_title}
						{$change_user_title|escape}
					{elseif $change_user && $change_user->username}
						{$change_user->username|escape}
					{else}
						{$change->user_id}
					{/if}
				</a>
			{/if}
		</td>
		<td>{$change->note}</td>
		<td>
			{if $meta.1 && !$meta.2}
				
				<a href='{$m->buildUri("{$item->id}/version",[changeid=>$change->id,field=>$smarty.get.field,ln=>$smarty.get.ln,clean=>2])}'>
					<i class="fa fa-eye"></i>
				</a>
			{elseif isset($meta.2) && GW_Change_Track_Render_Helper::shouldUseSummaryLink($meta.2, $meta.1)}
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
	.lastvisited{
		background:#fcf8e3;
		color:#222;
	}
	.lastvisited td,
	.lastvisited th,
	.lastvisited a{
		color:#222 !important;
	}
</style>
