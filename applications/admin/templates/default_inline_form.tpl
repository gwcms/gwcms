{function df_actions_save}
	<a href="#" onclick="$('#inlineForm').submit();return false" title="{GW::l('/g/SAVE')}">
		<img align="absmiddle" src="{$app->icon_root}action_save.png">
	</a>
{/function}
{include "elements/input_func.tpl"}

<form id="inlineForm" action="{$smarty.server.REQUEST_URI}" method="post"  enctype="multipart/form-data">

<input type="hidden" name="act" value="do:{$action|default:"save"}" />
<input type="hidden" name="item[id]" value="{$item->id}" />
<input type="hidden" name="ajax" value="1" />

{*
{if $item->id && $item->update_time}
	<input class="gwSysFields" type="hidden" name="item[update_time_check]" value="{if $item->update_time_check}{$item->update_time_check}{else}{$item->update_time}{/if}" />
{/if}
*}
	{*block name="inputs"}
		{$if_actions=[save]}
	{/block*}
	
	{$layout='inline'}
	{call "df_inputs"}
	
	{function name="df_actions"}
		<td class="dl_inlineform_actions">
			
		{foreach $if_actions as $if_action}
			{call name="df_actions_`$if_action`"}
		{/foreach}
		</td>
	{/function}
	
	{call name="df_actions"}
	
</form>
	
{include "includes.tpl"}
	
<!--AJAX-NOERR-DONT-REMOVE-->

