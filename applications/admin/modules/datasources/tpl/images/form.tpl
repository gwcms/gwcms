{include file="default_form_open.tpl"}




{if $item->id}
	{$type=read}
	{capture assign=tmp}
		{$image=$item}
		{include "tools/image_preview.tpl" width=500 height=500 fancybox=1}
	{/capture}
{/if}


{call e field=img type=read value=$tmp}

{call e field=owner}

{call e field=filename}
{call e field=width}
{call e field=height}
{call e field=size}
{call e field=original_filename type=text}

{call e field=v}
{call e field=insert_time}





{include file="default_form_close.tpl"}