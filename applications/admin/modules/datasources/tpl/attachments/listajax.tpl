{include "messages.tpl"}

	{$thumb = explode('x',$smarty.get.preview.thumb)}
	{$img_preview_height = $thumb.0|default:200}
	{$img_preview_width = $thumb.1|default:200}

{if !$list}
	
{/if}

<style>
	.attach_block_in{ 
	      max-height: {$img_preview_height-1}px;
	      max-width: {$img_preview_width-1}px;			
	}
</style>

{foreach $list as $item}
	{$ids[]=$item->id}
{/foreach}

<input  id="{$id}" type="hidden" name="{$input_name}" value="{json_encode($ids)}" />

<ul class="sortContainer">

{$total_size=0}
{foreach $list as $item}
	<li class="sortItm" data-id="{$item->id}">
	<div class="attach_block">
	
{if $item->content_cat=='file'}	
	
	{*** FILE ***}
	
	{$file=$item->file}
	

	{$filename=pathinfo($file->original_filename)}
	{$title=$filename.filename|truncate:40}
	{if $filename.extension}
		{$title="`$title`.`$filename.extension`"}
	{/if}

	<div class="attach_block_in">
		<a href="{$app->sys_base}tools/download/{$file->key}" title="{$title|escape} ({$file->size_human}) {$item->content_type}">
			<i class="fa {$m->icon($title)}"></i>
			{$title|truncate:80} ({$file->size_human}) </a> 
	</div>	
	{$total_size=$total_size+$file->size}
{else}
	{$image=$item->image}
	{$total_size=$total_size+$image->size}
		
		{if $image}
			{$title=$image->original_filename}
			{include 
				file="tools/image_preview.tpl" 
				image=$image border=1 width=$img_preview_height height=$img_preview_width show_filename=1}
		{/if}


{/if}
		<div class="attach_actions" style="display:none">
			<i data-id="{$item->id}" class="attach_action fa fa-trash-o link btn-remove" title='{GW::l('/g/REMOVE')} "{$title}" {$image->size}'></i>
		</div>

	</div>


	</li>
	
{/foreach}

</ul>

{$hidden_note=json_encode($validation)}

{capture assign=hidden_note}
	<i class="fa fa-picture-o"></i><br />
	{foreach $validation.image as $valpar => $valval}
		{GW::l("/m/VALIDATION_PARAMS/{$valpar}")}: {$valval}<br />
	{/foreach}
	<br />
	
	<i class="fa fa-file"></i><br />
		{GW::l("/m/VALIDATION_PARAMS/size_max")}: {GW_File_Helper::cFileSize($validation.file.size_max)}<br/>
		
	{if $validation.general.limit}
		{GW::l("/m/VALIDATION_PARAMS/limit")}: {$validation.general.limit} <br/>
	{/if}
	
{/capture}

<a class="fa gwAddPopover add-popover" 
    data-content="{$hidden_note|escape}"  
    data-placement="right" 
    data-container="body" 
    data-toggle="popover" 
    data-html="true" 
    data-trigger="focus" 
    href="#popover" onclick="return false"></a>


{if $total_size}
{GW_file_helper::cfilesize($total_size)}
{/if}
