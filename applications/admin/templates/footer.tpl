	



<!-- Visible when footer positions are static -->
<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
<div class="2 pull-right pad-rgt">
	{str_replace('%year%',date('Y'), GW::l('/g/FOOTER'))}
</div>

{if $session_exp!=-1}
<div class="pull-left pad-rgt pad-lft">
	<span class="session_exp_t">{GW::l('/g/SESSION_VALIDITY')}:</span> 
	<span id="session_exp_t" class="session_exp_t">-</span>
</div>
{/if}

<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
<!-- Remove the class "show-fixed" and "hide-fixed" to make the content always appears. -->
<!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

<p class="pad-lft">
	{GW::l('/g/SERVER_TIME')}: <span id="server_time">{'H:i:s'|date}</span> 
	<br />
	{GW::l('/g/YOUR_IP')}: {$smarty.server.REMOTE_ADDR} | {if GW::$globals.proc_timer}{$GLOBALS.proc_timer->stop()}{/if}
	
</p>


{*

<div style="float:left">
	
	
	<br />
	
</div>
<div style="float:left;margin-left:10px">

</div>

<div style="float:right;text-align: right;">
	
	
</div>
*}
