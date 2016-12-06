
{if $min}
	{capture append=input_extra_params} min="{$min}" {/capture}
{/if}

{if $max}
	{capture append=input_extra_params} max="{$max}" {/capture}
{/if}

{if $step}
	{*step=any - alow any precision. step=0.01 - currency*}
	{capture append=input_extra_params} step="{$step}" {/capture}
{/if}

{if $input_extra_params}
	{$input_extra_params=implode(' ',$input_extra_params)}
{/if}

{include file="elements/inputs/text.tpl"}