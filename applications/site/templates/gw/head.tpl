{if $app->user}	
	{assign var="session_exp" value=$app->user->remainingSessionTime() scope=parent}
{/if}

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>{$title|default:$request->page->get(title)}{if
$lang.SITE_TITLE} - {$lang.SITE_TITLE}{/if}</title>

<script type="text/javascript" src="/scripts/gw.js"></script>

<script type="text/javascript">
	$.extend(GW, { base:'{$request->base}', ln:'{$request->ln}', path:'{$request->path}', session_exp:{$session_exp}, server_time:'{"F d, Y H:i:s"|date}'});
</script>