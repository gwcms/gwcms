<div class="uploadFileInput">
{*http://plugins.krajee.com/file-input*}
			{* cd /var/www/project/vendor && git submodule add git://github.com/kartik-v/bootstrap-fileinput.git *}
					
			{if !$input_image_loaded}
				<link href="{$app->sys_base}vendor/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
				<script src="{$app->sys_base}vendor/bootstrap-fileinput/js/fileinput.js?v=2" type="text/javascript"></script>

				

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
		$(function(){
			$('#{$id}').fileinput(
				$.extend(
					{
						previewClass: "UploadFilePreview",
						{if $allowedFileExtensions}allowedFileExtensions : {json_encode($allowedFileExtensions)},{/if}
						//showUpload: false,
						//showCaption: false,	

						overwriteInitial: false,
						maxFileCount: 5,	
						showUpload: false,
						showRemove: false,
						uploadUrl: '{$app->buildURI($links.file_upload_path)}',
						progress: '',
						dropZoneEnabled: false,

						uploadExtraData : { 
							image_input_remove_path : '{$app->buildURI($links.file_remove_path)}'
						}						
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
			})


		});
	</script>	
</div>