<table class="gw_clean_tbl" cellspacing="0" cellpadding="0">

{if is_array($selected)}
	{$selected = array_flip($selected)}
{else}
	{$selected=[]}
{/if}

{foreach $options as $key => $title}
<tr>
    <td width="10"><input type="checkbox" name="{$input_name}" value="{$key}" title="{$title|escape}" {if isset($selected.$key)}CHECKED{/if} /></td>
    <td>{$title}</td>
</tr>
{/foreach}
</table>