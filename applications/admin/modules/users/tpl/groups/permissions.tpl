{include file="default_open.tpl"}

<div style="max-width:600px;">


<h3>{GW::l('/m/CHANGE_GROUP_PERMISSIONS')|sprintf:$item->title}</h3>

<p>{gw_link levelup=1 title=GW::l('/g/BACK')}</p>
<br />



<form action="{$smarty.server.REQUEST_URI}" method="post">

<input type="hidden" name="act" value="do:save_permissions" />
<input type="hidden" name="item[id]" value="{$item->id}" />


<table class="gwTable gwActiveTable gwlisttable">
<tr>
	<th width="20%">{GW::l('/m/FIELDS/id')}</th>
	<th width="80%">{GW::l('/m/FIELDS/title')}</th>
	<th></th>
</tr>


{foreach from=$list item=item}
{$path=$item->get(path)}

<tr {if $item->level!=0}style="color: #888"{/if}>

	<td nowrap>{$path}</td>
	<td>{$item->get(title)}</td>

	<td nowrap>
		<input type="checkbox" name="item[paths][{$path|escape}]" value="1" {if $selected.$path}CHECKED{/if} />
	</td>
</tr>


{/foreach}



</table>
<br />

<p><input class="btn btn-primary" type="submit" value="{GW::l('/g/SAVE')}"/></p>

</form>

</div>

{include file="default_close.tpl"}