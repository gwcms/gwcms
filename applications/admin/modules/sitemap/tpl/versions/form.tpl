

{if $item->content=='diffc'}
	
			
		{$file1=$item->getCurrentContent()}
		{$file2=GW_String_helper::applyDiff($item->uncompressDiff(),$item->getCurrentContent())}
		

		<table style='width:100%'>


		
		{diff_helper::getTableStyle()}
		

		{diff_helper::toTable(diff_helper::compare($file1,$file2),'','')}

		{diff_helper::scripts()}
				
		
		</table>
	
{else}
	{include file="default_form_open.tpl"}
	{call e type="textarea" field="content"}
	{include file="default_form_close.tpl"}
{/if}


