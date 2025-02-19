
		{include "head.tpl"}	
		
		
		<a href='{$m->buildUri("{$item->id}/versions",[field=>$smarty.get.field,ln=>$smarty.get.ln,changeid=>$smarty.get.changeid,clean=>2])}'>Back to list</a>
		<br>
		Changes was done by user <b>{$changes_user->title}</b><br>
		
		<div style="background-color:white;color:black">
		

		<table style='width:100%'>
			<tr><td width="50%">Past version {$changesitm->insert_time}</td><td width="50%">Current verssion ({$item->update_time})</td></tr>
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
	



