{include file="head.tpl"}


<br />

{if $item}
<table class="gwTable" cellpadding="3">
	
	
	{if $sender}<tr><th>{GW::l("/A/FIELDS/sender")}</th><td>{$sender->title}</td></tr>{/if}
	<tr><th>{GW::l("/A/FIELDS/time")}</th><td>{$item->insert_time}</td></tr>
	{if $item->subject}<tr><th>{GW::l("/A/FIELDS/subject")}</th><td>{$item->subject} {if $item->group_cnt}({$item->group_cnt+1}){/if}</td></tr>{/if}
	<tr><td colspan="2" class="msg" style="padding: 10px">{$item->message}</td></tr>
</table>


<br /><br />

<button onclick="location.href='{gw_path do='SetSeen' params=[id=>$item->id]}'">{GW::l('/m/MARK_AS_READ')}</button>
<button onclick="location.href='{$m->buildUri('', [clean=>1])}'">{GW::l('/m/ALL_MESSAGES')}</button>


{else}
	{GW::l('/g/NO_NEW_MESSAGES')}
{/if}


<script>
	function markAsRead()
	{
		jQuery.ajax({
			url:'{gw_path do='SetSeen' params=[id=>$item->id]}', 
			async:false
		}); 
	}
	
		require(['gwcms'],function(){
			parent.window.gw_session.ping();
		})	
	
</script>
<style>
	body{ background-color: white; }
</style>

