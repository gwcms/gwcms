


{function name="cust_inputs"}
	
	{$i="elements/input.tpl"}	

	{if $field==''}
	
	{elseif $field=="site_id"}
		{if GW::s('MULTISITE')}
			{include file=$i name=$field type=select options=$options.site_id}
		{/if}
	{elseif $field=="name"}
		{include file=$i name=$field type=text}
	{elseif $field=="path_filter"}
		{include file=$i name=$field type=text}
	{elseif $field=="contents_type"}
		{include file=$i name=$field type=select options=GW::l('/m/OPTIONS/block_types')}
	{elseif $field=="ln"}
		{include file=$i name=$field type=select options=array_merge(['*'=>GW::l('/m/ALL_LANGS')],GW::s("LANGS"))}
	{elseif $field=="contents"}
		{*
			<i id="1">Text line</i>
			<i id="2">Text area</i>
			<i id="3">Formated text</i>
			<i id="4">Html code</i>
			<i id="5">Template file</i>
			<i id="6">Color</i>
			<i id="7">Image</i>
		*}
		{$typemap=[1=>text,2=>textarea,3=>code_smarty,4=>htmlarea,5=>text,6=>color,7=>text]}
		{if $item->contents_type==3}
			{$params_expand=['height'=>"150px",layout=>wide]}
		{/if}
		
		{include file=$i name=$field type=$typemap[$item->contents_type]}
	{elseif $field=="active"}
		{include file=$i name=$field type=bool}
	{elseif $field=="preload"}
		{include file=$i name=$field type=select options=GW::l('/m/OPTIONS/preload')}		
	{else}
		{include file=$i name=$field type=read}
	{/if}
		
{/function}