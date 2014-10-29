
<div style="float:left">
	{$lang.SERVER_TIME}: <span id="server_time">{'H:i:s'|date}</span> 
	
	<br />
	{$lang.YOUR_IP}: {$smarty.server.REMOTE_ADDR}
</div>
<div style="float:left;margin-left:10px">
	{if $session_exp!=-1}<span class="session_exp_t">{$lang.SESSION_VALIDITY}:</span> 
	<span id="session_exp_t" class="session_exp_t">-</span>
	{/if}
</div>

<div style="float:right;text-align: right;">
	{*{'Y'|date} &copy; sms.gw.lt*}
	{str_replace('%year%',date('Y'), $lang.FOOTER)}
	
</div>