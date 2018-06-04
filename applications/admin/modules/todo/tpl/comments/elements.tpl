{function "e"}

	{if $eopt}{$tmpO=$eopt}{else}{$tmpO=$options[$field]}{/if}
	{if $efile}{$tmpF=$efile}{else}{$tmpF="elements/input.tpl"}{/if}'

	
	{include file=$tmpF options=$tmpO name=$field }
{/function}
{function name="cust_inputs"}
	{if $field==''}

	{elseif $field=="description"}
		{e type=textarea autoresize=1 style=['min-width'=>"500px"] height="150px"}
	{else}
		{e type=read}
	{/if}

{/function}


