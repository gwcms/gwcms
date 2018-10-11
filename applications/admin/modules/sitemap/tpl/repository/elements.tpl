
{function "e"}

	{if $eopt}{$tmpO=$eopt}{else}{$tmpO=$options[$field]}{/if}
	{if $efile}{$tmpF=$efile}{else}{$tmpF="elements/input.tpl"}{/if}'

	
	{include file=$tmpF options=$tmpO name=$field}
{/function}
{function name="cust_inputs"}
	{if $field==''}

	{elseif $field=="path"}
		
		{e type="text"}
	{elseif $field==filename}
		{e type="text"}
	{/if}

{/function}

