{if $empty_option}
	{$options=$lang.EMPTY_OPTION+$options}
{/if}


<select  id="{$id}" class="{if $class} {$class}{/if}" {if $required}required="required"{/if} name="{$input_name}" onchange="{$onchange}" 
		 {if $enable_search}data-live-search="true"{/if} {if $readonly}disabled{/if}
		 >
	{html_options  selected=$value options=$options disabled=$disabled strict=1}
</select>


