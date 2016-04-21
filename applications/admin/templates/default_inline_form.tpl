{function df_actions_save}
	<a href="#" onclick="submitInlineForm(this); return false" title="{GW::l('/g/SAVE')}">
		<img align="absmiddle" src="{$app_root}img/icons/action_save.png">
	</a>
{/function}

<form id="inlineForm" action="{$smarty.server.REQUEST_URI}" method="post"  enctype="multipart/form-data" >

<input type="hidden" name="act" value="do:{$action|default:"save"}" />
<input type="hidden" name="ajax" value="1" />
<input type="hidden" name="item[id]" value="{$item->id}" />


	{*block name="inputs"}
		{$if_actions=[save]}
	{/block*}
	
	{call "df_inputs"}
	
	{function name="df_actions"}
		<td>
			
		{foreach $if_actions as $if_action}
			{call name="df_actions_`$if_action`"}
		{/foreach}
		</td>
	{/function}
	
	{call name="df_actions"}
	
</form>
	

	
<!--AJAX-NOERR-DONT-REMOVE-->

