{function gwinclude}
	{if $include.0=='function'}
		{call name=$include.1}
	{elseif $include.0=='file'}
		{include file=$include.1}
	{else}
		{$include.1}
	{/if}
{/function}


{function toolbar_button}
	<a class="{if $indropdown}gwtoolbarbtn{else}gwtoolbarbtn btn btn-{$btnnormal|default:'default'} btn-active-{$btnactive|default:'primary'}{/if} {$btnclass}" 
	   {if $toggle}data-toggle="button" aria-pressed="false"{/if} 
	   {*2018-10 outdate if $query_param}onclick="var ss=window.prompt('{$query_param}');if(ss)location.href=this.href+ss;return false;"{/if*}
	   {if $query_param}onclick="var ss=window.prompt('{$query_param.1}');if(ss)location.href=gw_navigator.url(this.href, { '{$query_param.0}': ss  });return false;"{/if}
	   {*dar gali but toks: this.href=gw_navigator.url*}
	   {foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
	   {if $confirm}{$app->fh()->gw_link_confirm()}{/if}
	   {if $onclick}onclick="{$onclick};return false"{/if} href="{$href|default:'#'}"
	   {if $shift_button} onclick="if(event.shiftKey){ location.href=gw_navigator.url(this.href,{ 'shift_key':1 });return false}"; {/if}
	   {if $newwindow}target="_blank"{/if}>{if $iconhtml}{$iconhtml}{/if}{if $iconclass}{if strpos($iconclass,'material ')!==false}<span style="font-size:20px !important" class="materialico material-symbols-outlined">{str_replace(material,'',$iconclass)}</span>{else}<i class="{$iconclass}"></i>{/if}{/if} <span class='alabel'>{$title}</span>
		
	</a>

	{if strpos($btnclass, 'iframeopen')!==false}
		<script>require(['gwcms'], function(){ gw_adm_sys.init_iframe_open(); }) </script>
	{/if}
{/function}

