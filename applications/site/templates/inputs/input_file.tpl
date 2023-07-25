{$maxFilecount = $maxFileCount|default:1}
<div class="uploadFileInput">
{*http://plugins.krajee.com/file-input*}
			{* cd /var/www/project/vendor && git submodule add git://github.com/kartik-v/bootstrap-fileinput.git *}
					
			{if !$input_image_loaded}
				<link href="{$app->sys_base}vendor/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
				<script src="{$app->sys_base}vendor/bootstrap-fileinput/js/fileinput.js?v=2" type="text/javascript"></script>
				<script src="{$app->sys_base}vendor/bootstrap-fileinput//js/fileinput_locale_{$ln}.js" type="text/javascript"></script>
				

				{*
				<script src="../js/fileinput_locale_fr.js" type="text/javascript"></script>
				<script src="../js/fileinput_locale_es.js" type="text/javascript"></script>			
				*}	
				{assign scope=global var=input_image_loaded value=1}
			{/if}
			
			<input id="{$id}" name="{$field}" type="file"  multiple >

			
			{if is_array($value)}
				{$files=$value}
			{else}
				{$file=$value}
			{/if}	
<style>
	.UploadFilePreview {
		border:0;
		padding:0;
	}
	.uploadFileInput .fileinput-remove{ display:none }
</style>
			
	
	<script>
		{if !$endpoint}{$endpoint=implode('/',$m->module_path)}{/if}
		{$endpoint = $m->buildDirectUri($endpoint,[],[level=>0])}
		
		
		$(function(){
			$('#{$id}').fileinput(
				$.extend(
					{
						language: "{$ln}",
						previewClass: "UploadFilePreview",
						{if $allowedFileExtensions}allowedFileExtensions : {json_encode($allowedFileExtensions)},{/if}
						//showUpload: false,
						//showCaption: false,
						{if $maxFilecount==1}
						autoReplace: true,
						{/if}
							
						overwriteInitial: true,
						maxFileCount: {},	
						showUpload: false,
						//uploadUrl: '{$app->buildURI($links.file_upload_path)}',
										{*uploadExtraData : {json_encode($extra_params)},*}
						uploadUrl: '{$file_upload_path|default:"{$endpoint}/uploadfile?id={$item->id}"}',						
						progress: '',
						dropZoneEnabled: false,
						browseClass: "btn btn-default",
						removeClass: 'fa fa-trash',
						showRemove: false,
						 browseIcon: '<i class="fa fa-folder-open-o" aria-hidden="true"></i>',
						fileActionSettings: {
							removeIcon: '<i class="fa fa-trash text-danger"></i>'
						}
						
						
						//uploadExtraData : { 
						//	image_input_remove_path : '{$app->buildURI($links.file_remove_path)}'
						//}						
					}, 
					{$m->inputFilePreview($field)}
				)
			).on('fileerror', function(event, data) {

			}).on("filebatchselected", function(event, files) {

				if(files.length)
					setTimeout("$('#{$id}').fileinput('upload');",500);



			}).on('filepreremove', function(event, id, index) {
				if (!window.confirm('Do you wish to remove?')) {
				    event.preventDefault();
				}
			}).on('fileuploaded', function(event, id, index) {
				{$onupload}
			})


		});
	</script>	
</div>
					
<style>
	.singleUploadFilePreview {
		border:0;
		padding:0;
	}
	.singleUploadFilePreview .close.fileinput-remove{
		display:none;
	}
	.singleUploadFilePreview .file-actions{
		display:none;
	}	
	.hide{ display:none }
	
	{if $maxFileCount < 2}
		.kv-fileinput-caption{ display:none }
	{/if}
	{if $noremove}
		.kv-file-remove{ display:none }
	{/if}
</style>