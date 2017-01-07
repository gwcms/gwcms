{include file="default_form_open.tpl" form_width="100%"}
{$width_title=100px}


{include file="elements/input.tpl" name=title}


{include file="elements/input.tpl" type=textarea name=description autoresize=1 height=50px}
{include file="elements/input.tpl" name=rate type=select options=[0,1,2,3,4,5,6,7,8,9,10]}

{if $item->id}
	{include file="elements/input.tpl" name=recommend}

	{include file="elements/input.tpl" name=image1 type=image}
	{include file="elements/input.tpl" name=name_orig}
	{include file="elements/input.tpl"  name=imdb type=code_json height=200px nopading=1 hidden_note="Clean area, and it will be updated"}  
{/if}

{include file="default_form_close.tpl"}