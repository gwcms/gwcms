

<input id="{$id}" name="{$input_name}" type="hidden" value="{$value|escape}"  	{foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach} />
