

{function name="df_inputs"}

	{foreach $m->list_config.dl_fields as $field}
		
		{if $field=='title'}
			{call e field=title}
		{elseif $field=='description'}
			{include file="elements/input.tpl" type=textarea name=description height=50px}
		{elseif $field=='rate'}
			{call e field=rate type=select_plain options=[0,1,2,3,4,5,6,7,8,9,10]}
		{elseif $field=='recommend'}
			{call e field=recommend}
		{elseif $field=='image'}
			{$img=$item->image1}
			<td>
			{if $img}
				<img src="{$app->sys_base}tools/imga/{$img->id}?size=50x50" align="absmiddle" vspace="2"  />
			{/if}
			</td>
		{elseif $field=='name_orig'}
			{call e field=name_orig}
		{else}
			<td></td>
		{/if}

	{/foreach}

{/function}

{$layout=inline}
{$if_actions=[save]}
{include file="default_inline_form.tpl"} 




