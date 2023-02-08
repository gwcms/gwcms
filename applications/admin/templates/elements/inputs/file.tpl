{$GLOBALS._input_file_n=GW::$globals._input_file_n+1}
{$suffix=GW::$globals._input_file_n}

{$file=$value}

{$inp_file_id="input_file_`$name`_`$suffix`"}


{if is_array($value)}
	{$files=$value}
{elseif $value}
	{$files=[$value]}
{else}
	{$files=[]}
{/if}


	

{foreach $files as $file}
	<div style="margin-top: 6px;margin-bottom:6px">

	{$filename=pathinfo($file->original_filename)}
	{$title=$filename.filename|truncate:40}
	{if $filename.extension}
		{$title="`$title`.`$filename.extension`"}
	{/if}


	{gw_link fullpath="`$app->sys_base`tools/download/`$file->key`" icon="file" title=$title} ({$file->size_human})

	{include "elements/zz_remove_composite.tpl" id=$file->id}

	</div>	
{/foreach}


{if !$readonly}
	<input id="{$inp_file_id}" type="file" name="{$name}" class="inp-file" />
{/if}
