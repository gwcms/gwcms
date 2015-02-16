{if $empty_option}
	{$options=$lang.EMPTY_OPTION+$options}
{/if}

<select {if $class}class="{$class}"{/if} {if $required}required="required"{/if} name="{$input_name}" onchange="{$onchange}">
	{html_options  selected=$value options=$options}
</select>
