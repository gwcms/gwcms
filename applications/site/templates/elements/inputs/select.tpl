{if $empty_option}
	{$options=$lang.EMPTY_OPTION+$options}
{/if}

{html_options name=$input_name selected=$value options=$options onchange=$onchange}
