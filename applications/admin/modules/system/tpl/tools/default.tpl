{include file="common.tpl"}
	{function name=do_toolbar_buttons_systemact} 		
		{toolbar_button title="Install" iconclass="material install_desktop"  href=$m->buildUri(false,[act=>doInstall]) }	
		{toolbar_button title="Compatability & Info" iconclass="material collapse_all"  href=$m->buildUri(compatability) }			
	{/function}		
{$dlgCfg2MWdth=300}
{$do_toolbar_buttons_hidden=[dialogconf2,systemact]}		
{$do_toolbar_buttons[]=hidden}


	
{include file="default_open.tpl"}


<div class="panel">
	<div class="panel-body">

<style>
	.btn{ margin-bottom: 2px }
</style>




ADMIN
{toolbar_button title="Debug mode"  href=$m->buildUri(false,[act=>doDebugModeToggle,app=>ADMIN]) pressed=$app->sess.debug}	
{toolbar_button title="JumpDebug"  href=$m->buildUri(false,[act=>doJumpDebugModeToggle,app=>ADMIN]) pressed=$app->sess.jumpdebug}


{toolbar_button title="Confidentialmode"  href=$m->buildUri(false,[act=>doJumpConfidentialModeToggle,app=>ADMIN]) pressed=$app->sess.confidentialmodel}




<br>

SITE
{toolbar_button title="Debug"  href=$m->buildUri(false,[act=>doDebugModeToggle,app=>SITE]) pressed=$smarty.session.SITE.debug}	
{toolbar_button title="JumpDebug" href=$m->buildUri(false,[act=>doJumpDebugModeToggle,app=>SITE]) pressed=$smarty.session.SITE.jumpdebug}	

<br>





<a class="btn btn-default" href="{$m->buildUri(false,[act=>doimportSqlUpdates])}"><i class="fa fa-cog"></i> 
	Import SQL Updates {if $lastupdates}Last update time: <b>{$lastupdates}</b>{/if}
	{if $updatefiles}<span style="color:green">Found updates: <b>{count($updatefiles)}</b></span>{else}<span style="color:blue">No updates</span>{/if}
</a>
<br/>

	</div>
</div>

<div class="panel">
	<div class="panel-body">
	

{if $test_actions}
	Test actions:
<ul>
{foreach $test_actions as $act}
	<li>
		<a href="{$m->buildURI(false,[act=>$act.0])}"><i class="fa fa-cog"></i> {$act.0}</a> {if $act.1.info}<i style="color:silver">({$act.1.info})</i>{/if}
	</li>
{/foreach}

</ul>
{/if}


{if $test_views}
	Test views:
<ul>
{foreach $test_views as $view}
	<li>
		<a href="{$m->buildURI($view.0)}"><i class="fa fa-file-code-o"></i> {$view.0}</a> {if $view.1.info}<i style="color:silver">({$view.1.info})</i>{/if}
	</li>
{/foreach}

</ul>
{/if}


<p>
	FOR WGET AUTH:
	GWSESSID={session_id()}
	Max script execution time result: {intval($m->modconfig->maxExecResult)} secs
</p>

Hot Keys:
<ul>
	<li>Ctrl+1 - system/tools?act=doSwitchEnvironment</li>
	<li>Ctrl+2 - system/tools?act=doPullProductionDB light</li>
	<li>Ctrl+3 - system/tools?act=doPullProductionDB full</li>
	<li>Ctrl+4 - system/tools?act=doDebugModeToggle</li>
</ul>

	</div>
</div>

<div class='panel'>
	<div class='panel-body'>
		
<script src="https://code.highcharts.com/4.2.2/highcharts.js"></script>


<div id="hichcontainer" style="min-width: 310px; height: 300px; max-width: 600px;"></div>

<script>
	diskusagedata = {json_encode($diskusagedata)};
	dutotal= {intval($diskusagedata_total)};
	{literal}
function initGraph(){		
Highcharts.chart('hichcontainer', {
  chart: {
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false,
    type: 'pie'
  },
  title: {
    text: 'Repository disk usage. Total: '+dutotal+' MB'
  },
  tooltip: {
    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
  },
  plotOptions: {
    pie: {
      allowPointSelect: true,
      cursor: 'pointer',
      dataLabels: {
        enabled: true,
        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
      }
    }
  },
  series: [{
    name: 'Disk use',
    colorByPoint: true,
    data: diskusagedata
    }]
});	
}

window.onload = (event) => {
  console.log('page is fully loaded');
};

window.onload = (event) => {
  console.log('page is fully loaded2');
};

window.onload = (event) => {
		initGraph();
};
{/literal}
</script>

	</div>
</div>




{include file="default_close.tpl"}