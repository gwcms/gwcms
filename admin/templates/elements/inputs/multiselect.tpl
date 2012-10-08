<div style="overflow-y:scroll;max-height:80px;width:auto;float:left;padding-right:5px;border:1px solid silver">
<table class="gw_clean_tbl" cellspacing="0" cellpadding="0" style="width:auto">

{if is_array($selected)}
	{$selected = array_flip($selected)}
{else}
	{$selected=[]}
{/if}

{$selecteditems=[]}

{foreach $options as $key => $val}
	{if isset($selected.$key)}
		{$selecteditems[$key]=$val}
		{gw_unassign var=$options.$key}
	{/if}
{/foreach}

{$options=$selecteditems+$options}

{foreach $options as $key => $title}
<tr>
    <td width="10"><input type="checkbox" name="{$input_name}" value="{$key}" title="{$title|escape}" {if isset($selected.$key)}CHECKED{/if} /></td>
    <td>{$title}</td>
</tr>
{/foreach}
</table>

</div>