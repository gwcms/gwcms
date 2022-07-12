
{*module_blocks.class getListConfig (inputs) *}

{function name="cust_inputs"}
	
	{$i="elements/input.tpl"}	

	{if $field==''}
	
	{elseif $field=="site_id"}
		{if GW::s('MULTISITE')}
			{call "e" field=$field type=select options=$options.site_id}
		{/if}
	{elseif in_array($field, ["name", 'admnote'])}
		{call "e" field=$field type=text}
	{elseif $field=="path_filter"}
		{call "e" field=$field type=text}
	{elseif $field=="contents_type"}
		{call "e" field=$field type=select options=GW::l('/m/OPTIONS/block_types')}
	{elseif $field=="ln"}
		{call "e" field=$field type=select options=array_merge(['*'=>GW::l('/m/ALL_LANGS')],GW::s("LANGS"))}
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
		{$typemap=[1=>text,2=>textarea,3=>htmlarea,4=>code_smarty,5=>text,6=>color,7=>text]}
		{if $item->contents_type==4}
			{if $smarty.get.form_ajax}
				{$params_expand=['height'=>"400px","width"=>'400px']}
			{else}
				{$params_expand=['height'=>"400px",layout=>wide]}
			{/if}
		{/if}
		
		{call "e" field=$field type=$typemap[$item->contents_type]}
	{elseif $field=="active"}
		{call "e" field=$field type=bool}
	{elseif $field=="preload"}
		{call "e" field=$field type=select options=GW::l('/m/OPTIONS/preload')}		
	{else}
		{call "e" field=$field type=read}
	{/if}
		
{/function}