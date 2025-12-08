{* hourly_stats = ['2025-11-12 23:00'=>48466, '2025-11-12 22:00'=>82126, ...] *}
{if $hourly_stats}
	{assign var=max value=max((array)$hourly_stats)}

	<div class="2 pull-right pad-rgt">
	<div style="position:relative;top:5px;width:40px;height:24px;display:flex;align-items:flex-end;gap:1px;padding:2px;cursor:pointer;"
	     onclick="window.location='/admin/{$ln}/system/loadstats/testViewStats'">
	    {foreach $hourly_stats as $hour => $count}
		{$barHeight=round($count/$max*24)}
		<div title="{$hour}: {$count}" 
		     style="background:limegreen;width:calc(200px/{count($hourly_stats)} - 1px);
			    height:{$barHeight}px;">
		</div>
	    {/foreach}
	</div>
	</div>
{/if}