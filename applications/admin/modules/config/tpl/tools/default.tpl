{include file="default_open.tpl"}


<p>{gw_link do="install" icon="action_action" title="Install"}</p>

<p>
	{if $smarty.session.debug}{$state="off"}{else}{$state="on"}{/if}
	{gw_link do="debug_mode_toggle" icon="action_action" title="Debug mode `$state`"}
</p>
<p>{gw_link icon="action_action" relative_path="phpinfo" title="phpinfo"}</p>
<p>{gw_link icon="action_action" relative_path="compatability" title="Compatability & Info"}</p>


<p>{gw_link do="importSqlUpdates" icon="action_action" title="Import SQL Updates"} 
	{if $lastupdates}Last update time: <b>{$lastupdates}</b>{/if}
	{if $updatefiles}<span style="color:green">Found updates: <b>{count($updatefiles)}</b></span>{else}<span style="color:blue">No updates</span>{/if}
</p>



{include file="default_close.tpl"}