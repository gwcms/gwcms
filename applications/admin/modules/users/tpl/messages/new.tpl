{include file="default_open.tpl" no_standart_cms_frame=1}


<br />

{if $item}
<table class="gwTable" cellpadding="3">
	
	
	{if $sender}<tr><th>{$app->fh()->fieldTitle('sender')}</th><td>{$sender->title}</td></tr>{/if}
	<tr><th>{$app->fh()->fieldTitle('time')}</th><td>{$item->insert_time}</td></tr>
	{if $item->subject}<tr><th>{$app->fh()->fieldTitle('subject')}</th><td>{$item->subject} {if $item->group_cnt}({$item->group_cnt+1}){/if}</td></tr>{/if}
	<tr><td colspan="2" class="msg" style="padding: 10px">{$item->message}</td></tr>
</table>


<br /><br />

<button onclick="location.href='{gw_path do='SetSeen' params=[id=>$item->id]}'">{$m->lang.MARK_AS_READ}</button>


{else}
	{$lang.NO_NEW_MESSAGES}
{/if}


<script>
	$(function(){
		parent.window.gw_session.ping();
	})
	
	function markAsRead()
	{
		jQuery.ajax({
			url:'{gw_path do='SetSeen' params=[id=>$item->id]}', 
			async:false
		}); 
	}
	
</script>

{include file="default_close.tpl" no_standart_cms_frame=1}