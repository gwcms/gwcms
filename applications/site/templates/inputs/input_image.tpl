{*http://plugins.krajee.com/file-input*}
{* cd /var/www/project/vendor && git submodule add git://github.com/kartik-v/bootstrap-fileinput.git *}

{if !$input_image_loaded}
	<link href="{$app->sys_base}vendor/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
	<script src="{$app->sys_base}vendor/bootstrap-fileinput/js/fileinput.js?v=2" type="text/javascript"></script>



	{*
	<script src="../js/fileinput_locale_fr.js" type="text/javascript"></script>
	<script src="../js/fileinput_locale_es.js" type="text/javascript"></script>			
	*}	
	<script src="{$app->sys_base}vendor/bootstrap-fileinput//js/fileinput_locale_{$ln}.js" type="text/javascript"></script>	
	{assign scope=global var=input_image_loaded value=1}
{/if}

<input id="{$id}" name="{$field}" type="file"  >

{$image=$value}
{if !$thumbsize}
	{$thumbsize=[120,120]}
{/if}

{if !$minres}
	{$minres=$item->getImageMinSize($field)}
{/if}
{if !$maxres}
	{$maxres=$item->getImageMaxSize($field)}
{/if}	
{if !$extra_params}
	{$extra_params=[]}
{/if}

{$extra_params.image_input_remove_path=$m->buildURI(removefile)}

{capture assign=tmp}
	<img style='height:{$thumbsize.0}px' src='{$app_base}tools/img/%s?size={$thumbsize.0}x{$thumbsize.1}'>
{/capture}
{$extra_params.image_replace_key_html=$tmp}

{*
{if !$imresize}
{$imresize=$item->getImageResize($field)}
{$maxres=$imresize}
{/if}
*}

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
	

</style>

<script>
	{if !$endpoint}{$endpoint=implode('/',$m->module_path)}{/if}
	{$endpoint = $m->buildDirectUri($endpoint,[],[level=>0])}
		
	
	
	
function initImageInput{$id}(initialPreview)
{
		$('#{$id}').fileinput($.extend({
			previewClass: "singleUploadFilePreview",
			allowedFileExtensions : ['jpg', 'jpeg', 'png', 'gif'{if $allowpdf}, 'pdf'{/if}],
			showUpload: false,
			showCaption: false,
			autoReplace: true,
			maxFileCount: 1,
			overwriteInitial: true,
			showRemove: false,

			{if $minres}
				minImageWidth: {$minres.0},
				minImageHeight: {$minres.0},
			{/if}
			{if $maxres}
				maxImageWidth: {$maxres.0},
				maxImageHeight: {$maxres.1},
			{/if}
			{if $imresize}resizeImage: true,{/if}

			uploadExtraData : {json_encode($extra_params)},
			uploadUrl: '{$endpoint}/uploadfile?id={$item->id}',
			progress: '',
			dropZoneEnabled: false,
			language: "{$ln}"
		}, initialPreview)			
	).on('fileerror', function (event, data) {

	}).on("filebatchselected", function (event, files) {
		if (files.length){
			//$(this).fileinput('reset');
			//$('.file-preview-thumbnails').html('')
			$(this).fileinput('resetFileStack');
			
			setTimeout("$('#{$id}').fileinput('upload');", 500);
		}

	}).on('filepreremove', function (event, id, index) {
					if (!window.confirm('Do you wish to remove?')) {
						event.preventDefault();
					}
	}).on('fileloaded', function(event, file, previewId, index, reader) {
			//$(this).fileinput('refresh');
	}).on('filepreajax', function(event, previewId, index) {
		
	});	
}
	
$(function () {
	$.get('{$endpoint}/inputFilePreview', { field: '{$field}', id:{$item->id}  }, function(data){  initImageInput{$id}(JSON.parse(data)) });
	

});
</script>	