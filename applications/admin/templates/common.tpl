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


	<a class="{if $indropdown}gwtoolbarbtn{else}gwtoolbarbtn btn btn-{$btnnormal|default:'default'} btn-active-{$btnactive|default:'primary'}{/if}" 
	   {if $toggle}data-toggle="button" aria-pressed="false"{/if} 
	   {if $onclick}onclick="{$onclick};return false"{/if} href="{$href|default:'#'}">
		{if $iconclass}<i class="{$iconclass}"></i>{/if} <span>{$title}</span>
	</a>


{/function}
