
{$selected=$value}
{if is_array($selected)}
	{$selected=array_flip($selected)}
{/if}

<div class="row">
{foreach $options as $key => $opttitle}
	<div class="{if $newline}col-md-12{else}col-md-4{/if}">
	 <label class="checkbox-inline"><input style="opacity:1" type="checkbox" name="{$input_name}" value="{$key|escape}" {if isset($selected[$key])}checked="checked"{/if} {if $readonly}readonly disabled{/if}> {$opttitle}</label>
	</div>

{/foreach}
</div>	
