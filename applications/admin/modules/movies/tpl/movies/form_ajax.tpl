

{function name="df_inputs"}

	{foreach $m->list_config.dl_fields as $field}
		
		{if $field=='title'}
			{include file="elements/input.tpl" name=title}
		{elseif $field=='description'}
			{include file="elements/input.tpl" type=textarea name=description}
		{elseif $field=='rate'}
			{include file="elements/input.tpl" name=rate type=select_plain options=[0,1,2,3,4,5,6,7,8,9,10]}
		{elseif $field=='recommend'}
			{include file="elements/input.tpl" name=recommend}
		{elseif $field=='image'}
			{$img=$item->image1}
			<td>
			{if $img}
				<img src="{$app->sys_base}tools/imga/{$img->id}?size=50x50" align="absmiddle" vspace="2"  />
			{/if}
			</td>
		{elseif $field=='name_orig'}
			{include file="elements/input.tpl" name=name_orig}
		{else}
			<td></td>
		{/if}

	{/foreach}

{/function}

{$layout=inline}
{$if_actions=[save]}
{include file="default_inline_form.tpl"} 




