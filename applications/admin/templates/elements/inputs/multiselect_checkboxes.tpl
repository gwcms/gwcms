{if $readonly}
	{foreach $value as $selected}
		{$options[$selected]}{if !$selected@last},{/if}
	{/foreach}
{else}
	
<div style="overflow-y:scroll;max-height:{$height|default:"80px"};width:auto;float:left;padding-right:5px;border:1px solid silver">
<table class="inputmultiselectcheckboxes gw_clean_tbl" cellspacing="0" cellpadding="0" style="width:auto">
	
{capture assign=footer_hidden}
{/capture}	
	<style>
		.inputmultiselectcheckboxes > tbody > tr > td:nth-child(1) {
			 padding: 0 5px;
		}
	</style>


{if is_array($value)}
	{$value = array_flip($value)}
{else}
	{$value=[]}
{/if}
	
	
{if $selected_ontop}
	
	{$selecteditems=[]}

	{foreach $options as $key => $val}
		{if isset($value.$key)}
			{$selecteditems[$key]=$val}
			{gw_unassign var=$options.$key}
{/if}
	{/foreach}
	
	{$optionss=$selecteditems+$options}
{else}
	{$optionss=$options}{*kad nenumustu aukstesniuose templeituose options kintamojo*}
{/if}

{foreach $optionss as $key => $title}
<tr>
    <td width="10" ><input type="checkbox" name="{$input_name}" value="{$key}" title="{$title|escape}" {if isset($value.$key)}CHECKED{/if} /></td>
    <td>{$title}</td>
</tr>
{/foreach}
</table>


</div>

{/if}