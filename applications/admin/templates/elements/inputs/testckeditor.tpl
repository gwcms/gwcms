<textarea  
	id="{$id}" 
	name="{$input_name}" 
	{if $readonly}readonly="readonly"{/if}
	{foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
	style="display:none"
>{$value|escape}</textarea>

<script>
//var CKEDITOR_BASEPATH = '/vendor/ckeditor1/';	
	
require(["ckeditor"], function() {
	
	CKEDITOR.timestamp = null;//neveikia extra pluginsai
 
	var config = { }
	config.width = 'auto';

	//config.defaultLanguage = 'LT';
	config.language = '{$app->ln}';

	config.toolbarCanCollapse = true;
	
	
	config.extraPlugins = 'codemirror,autogrow,filebrowser';
	config.autoGrow_minHeight = 200;
	config.autoGrow_maxHeight = 600;
	config.autoGrow_bottomSpace = 50;
	
	config.filebrowserBrowseUrl = '/admin/{$app->ln}/sitemap/repository/fileselect';
	//config.filebrowserUploadUrl = '/admin/{$app->ln}/sitemap/repository/upload';	
	 
	{if $ck_options==minimum}
	config.toolbarStartupExpanded = false;
	config.toolbar = 'Basic'
	{/if}
	 
	 config.entities = false;
	 config.contentsCss = '/applications/site/assets/css/full.php';
	 //https://docs.ckeditor.com/ckeditor4/latest/guide/dev_file_browser_api.html
		
	CKEDITOR.replace( '{$id}', config); 
 
 
})

</script>

{if $ck_options==minimum}
<style>
	#{$id} .cke_toolbar{ padding: 0px; margin:0px; height: 24px !important; }
	#{$id} .cke_button{ padding: 4px !important; }
	#{$id} .cke_button:hover { padding: 3px !important; }
	#{$id} .cke_button_disabled:hover{ padding: 4px !important; }
	#{$id} .cke_top{ padding: 0px 3px 3px 3px !important; }
	#{$id} .cke_bottom{ display:none }
</style>
{/if}

