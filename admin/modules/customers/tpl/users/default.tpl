{include file="default_open.tpl"}

<div style="max-width:600px;">

<p>
{gw_link relative_path=form title=$lang.CREATE_NEW icon="action_file_add" params=[id=>0]}
</p>
<br />

{if !count($list)}
	<p>{$lang.NO_ITEMS}</p>
{else}

<table class="gwTable gwActiveTable">
<tr>
	<th width="40%">{$m->lang.FIELDS.second_name}, {$m->lang.FIELDS.first_name}</th>
	<th width="20%">{$m->lang.FIELDS.email}</th>
	<th width="1%">{$lang.ACTIONS}</th>
</tr>



{foreach from=$list item=item}
	{$id=$item->id}

	
<tr {if $smarty.get.id==$item->id}class="gw_active_row"{/if}>
	<td>{$item->second_name}, {$item->first_name}</td>
	<td><a href="mailto:{$item->email}">{$item->email}</a></td>

	<td nowrap>
		{include file="tools/list_actions.tpl" actions=[invert_active,invert_banned,edit,delete]}
	</td>
</tr>

{/foreach}

</table>

{/if}

</div>

{include file="default_close.tpl"}