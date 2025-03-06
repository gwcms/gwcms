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
	{$do_toolbar_buttons[] = search}
	
	{$do_toolbar_buttons_hidden=[dialogconf,dialogconf2]}	
	
	{$dl_actions=[edit,delete]}
	
	{$dl_filters=[text=>1, time=>1]}
	


	
	{*$order_enabled_fields=[text,insert_time,update_time]*}
{/block}


{block name="after_list"}
	<br />
	<small style="color:silver" >Auto hide <span id='minutes_seconds_remaining'></span></small>
	
	<script>
		{literal}
		require(['gwcms'], function(){
		    let timeRemaining = 10 * 60; // 10 minutes in seconds

		    function updateTimer() {
			let minutes = Math.floor(timeRemaining / 60);
			let seconds = timeRemaining % 60;

			// Format seconds to always have two digits
			let formattedTime = `${minutes}:${seconds.toString().padStart(2, '0')}`;

			$('#minutes_seconds_remaining').text(formattedTime);

			if (timeRemaining <= 0) {
			    clearInterval(countdown);
			    location.reload();
			} else {
			    timeRemaining--;
			}
		    }

		    // Initial update and start interval
		    updateTimer();
		    let countdown = setInterval(updateTimer, 1000);
		});	
		{/literal}
	</script>
{/block}
