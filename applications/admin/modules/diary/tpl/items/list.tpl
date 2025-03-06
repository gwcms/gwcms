{extends file="default_list.tpl"}
{include file="`$m->tpl_dir`/default_list_ajax_edit.tpl"}

{block name="init"}

	{capture append=footer_hidden}
	<style>
		.editable{
			max-width: 1400px;
			word-wrap: break-word;			
		}
	</style>
	{/capture}
	

	{function name=dl_cell_text}


		{if $item->type==0}
			{$weekdays=['','Pirmadienis','Antradienis','Trečiadinis','Ketvirtadienis','Penktadienis','Šeštadienis','Sekmadienis']}
			<b>{$app->fh()->shortTime($item->time)} {$weekdays[date('N',strtotime($item->time))]}</b>
		{/if}

		<div class="editable" ajaxsaveargs="{ name: 'text', vals: {  id: {$item->id} } }">
		
		{if $item->type!=0}
			<img align="absmiddle" onclick="$(this).next().click()" src="{$app->icon_root}folder.png">
			<a href="{gw_link params=[pid=>$id] path_only=1}">{$item->text} ({$item->child_count})</a>
		{else}
			{GW_Link_Helper::parse($item->text)}
		{/if}
		</div>
		
	{/function}
	
	

	{$dl_smart_fields=[text]}
	{$dl_output_filters=[time=>short_time]}
	
	
	
	{$do_toolbar_buttons[] = hidden}	
	{$do_toolbar_buttons[] = unhide}	
	{$do_toolbar_buttons[] = search}
	
	{$do_toolbar_buttons_hidden=[dialogconf,dialogconf2]}	
	
	{$dl_actions=[edit,delete]}
	
	{$dl_filters=[text=>1, time=>1]}
	
	{function name=do_toolbar_buttons_unhide}
		{*{toolbar_button title=Encrypt iconclass='fa fa-lock' href=$m->buildUri(false,[act=>doEncrypt,pw=>'']) query_param="Enter encryption key"}*}

		{if !$m->modconfig->unlocked}
			{toolbar_button title="Unlock" iconclass='fa fa-unlock' href=$m->buildUri(false, [act=>doUnlock])}
		{/if}
		{*{list_item_action_m url=[false,[id=>$item->id,act=>doSwitchSim,simid=>'']] query_param="Enter sim id 0-`$tmp`" caption="Sw" title="Switch sim"}*}
	{/function}	

	
	{*$order_enabled_fields=[text,insert_time,update_time]*}
{/block}


{block name="after_list"}
	<br />
	{if $m->modconfig->unlocked}
		<small style="color:silver" >Auto hide in <span id='minutes_seconds_remaining_ah'></span>
		<small style="color:silver" >Auto reload in <span id='minutes_seconds_remaining_ar'></span>
	</small>
	
	<script>
		
		{literal}
		require(['gwcms'], function(){
			let timeRemainingAH = 10 * 60; // 10 minutes in seconds
			let timeRemainingReload = 30 * 60; // 30 minutes in seconds (10 min + 20 min)

			function updateTimer() {
			    let minutesAH = Math.floor(timeRemainingAH / 60);
			    let secondsAH = timeRemainingAH % 60;
			    let minutesReload = Math.floor(timeRemainingReload / 60);
			    let secondsReload = timeRemainingReload % 60;

			    // Format seconds to always have two digits
			    let formattedTimeAH = `${minutesAH}:${secondsAH.toString().padStart(2, '0')}`;
			    let formattedTimeReload = `${minutesReload}:${secondsReload.toString().padStart(2, '0')}`;

			    $('#minutes_seconds_remaining_ah').text(formattedTimeAH);
			    $('#minutes_seconds_remaining_ar').text(formattedTimeReload);

			    if (timeRemainingReload <= 0) {
				clearInterval(countdown);
				location.reload();
			    } else {
				if (timeRemainingAH > 0) timeRemainingAH--;
				timeRemainingReload--;
			    }
			}

			// Initial update and start interval
			updateTimer();
			let countdown = setInterval(updateTimer, 1000);
		});	
		{/literal}
	</script>
	{/if}
{/block}
