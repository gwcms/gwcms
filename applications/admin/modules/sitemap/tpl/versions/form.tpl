

{if $item->content=='diffc'}
	
		{include "head.tpl"}	
		<div style="background-color:white;color:black">
		{$headversion=$item->getHeadVersion()}
		{$pastversion=$item->getRevertedContent()}
		

		<table style='width:100%'>
			<tr><td width="50%">Past version {$item->time}</td><td width="50%">Current verssion ({$item->head_version_time})</td></tr>
		</table>

		
		{diff_helper::getTableStyle()}
		
		
		{diff_helper::toTable(diff_helper::compare($pastversion, $headversion),'','')}

		{diff_helper::scripts()}
				
		
		</table>
		</div>
		
		<script>
			parent.window.$('.modal-dialog').css('width',"95%")
			parent.window.$('#gwDialogConfiFrm').css('width',"100%")
		</script>
		
		
		copyfriendly<br>
		headversion:
		<textarea>{$headversion|escape}</textarea>
		pasversion
		<textarea>{$pastversion|escape}</textarea>
	
{else}
	{include file="default_form_open.tpl"}
	{call e type="textarea" field="content"}
	{include file="default_form_close.tpl"}
{/if}


