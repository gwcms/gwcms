{include file="default_open.tpl"}


<iframe frameborder="0" style="border:1px solid siver;width:100%;{if $smarty.get.clean}height:100%;{else}height:500px;{/if}" src="{$app_base}{$ln}/{$m->module_path[0]}/{$m->module_path[1]}/iframe?id={$smarty.request.id}&padding=1"></iframe>

{include file="default_close.tpl"}
