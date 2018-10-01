{foreach $dirs as $fullfile => $file}
	{$name=basename($file)}
	<div class="folder" data-dir="{$file}/"><span class="folderlink"><i class="fa fa-folder-o"></i>  {$name}</span></div>
	
{/foreach}

{foreach $files as $fullfile => $file}
	{$name=basename($file)}
	{$dir=dirname($file)}
	
	
	{if $smarty.get.ftype==image}
		<img class="file" data-file="{$file}" src="{$app->sys_base}tools/img_resize?file={urlencode($file)}&dirid=repository&size=100x100" title="{$dir} {$name}" alt="{$name}" />
	{else}
		<div class="file" data-file="{$file}"><i class="{Mime_Type_Helper::icon($name)}"></i> {$name}</div>
	{/if}
{/foreach}



{if $dir !='/'} {* do not allow put files on root dir*}
<i class="fa fa-plus-circle addfiles" data-dir="{$smarty.get.dir}"></i>
{/if}
