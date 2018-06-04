{function "e"}

	{if $eopt}{$tmpO=$eopt}{else}{$tmpO=$options[$field]}{/if}
	{if $efile}{$tmpF=$efile}{else}{$tmpF="elements/input.tpl"}{/if}'

	
	{include file=$tmpF options=$tmpO name=$field}
{/function}
{function name="cust_inputs"}
	{if $field==''}

	{elseif $field=="project_id"}
		{*
		{e efile="elements/input_select_edit.tpl" type=select empty_option=1 datasource=$app->buildUri('todo/projects')}
		*}
		
		{e type=select_plain empty_option=1}
	{elseif $field==title}
		{e}
	{elseif $field==time_have}
		{e value=gw_math_helper::uptime($item->time_have)}
	{elseif $field==description}
		{e type=textarea height="50px"}
	{else}
		{e type=read}
	{/if}

{/function}


