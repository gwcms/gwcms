<div id="{$id}_containerck">
<textarea  
	id="{$id}" 
	name="{$input_name}" 
	{if $readonly}readonly="readonly"{/if}
	{foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
	style="display:none"
>{$value|escape}</textarea>
</div>

{if !$ck_set}
	{$ck_set = 'minimum'}
{/if}


{if !is_array($ck_options)}
	{$ck_options=[]}
{/if}
{if $add_site_css}
	{$ck_options.contentsCss = '/applications/site/assets/css/full.php'}
{/if}
{if !$ck_options.contentsCss}
	{$ck_options.contentsCss = '/applications/admin/static/css/ck_default.css'}
{/if}

<script>
//var CKEDITOR_BASEPATH = '/vendor/ckeditor1/';	
	
require(["ckeditor"], function() {
	
	CKEDITOR.timestamp = null;//neveikia extra pluginsai
 
	var config = {if $ck_options}{json_encode($ck_options)}{else} { } {/if} ;
	config.width = 'auto';

	//config.defaultLanguage = 'LT';
	config.language = '{$app->ln}';

	config.toolbarCanCollapse = true;
	
	
	config.extraPlugins = (config.extraPlugins ? config.extraPlugins + ',':'') + 'codemirror,filebrowser,resize';
	
	{if $height}
		config.height = "{$height}";
	{else}
		config.autoGrow_minHeight = 200;
		config.autoGrow_maxHeight = 600;
		config.autoGrow_bottomSpace = 50;
		config.extraPlugins += ',autogrow';
	{/if}
	config.filebrowserBrowseUrl = '/admin/{$app->ln}/sitemap/repository/fileselect';
	//config.filebrowserUploadUrl = '/admin/{$app->ln}/sitemap/repository/upload';	
	 
	{if $ck_set==minimum}
	config.toolbarStartupExpanded = false;
	config.toolbar = 'Basic'
	{/if}
	 
	 config.entities = false;
	 
	 //https://docs.ckeditor.com/ckeditor4/latest/guide/dev_file_browser_api.html

	if(config.enterMode=='CKEDITOR.ENTER_BR')
		config.enterMode=CKEDITOR.ENTER_BR;
	
	CKEDITOR.replace( '{$id}', config); 
 
 
	CKEDITOR.instances['{$id}'].on('instanceReady', function (ev) {

		//$(CKEDITOR.instances['{$input_name}'].element.$).attr('id',"{$id}")

		ev.editor.on('change', function() { 
			this.updateElement();
			$(CKEDITOR.instances['{$id}'].element.$).change()
		});
	});	
 
})

</script>

{if $ck_set==minimum}
<style>
	#{$id}_containerck .cke_toolbar{ padding: 0px; margin:0px; height: 24px !important; }
	#{$id}_containerck .cke_button{ padding: 4px !important; }
	#{$id}_containerck .cke_button:hover { padding: 3px !important; }
	#{$id}_containerck .cke_button_disabled:hover{ padding: 4px !important; }
	#{$id}_containerck .cke_top{ padding: 0px 3px 3px 3px !important; }
	#{$id}_containerck .cke_bottom{ display:none }
</style>
{/if}


{if $ck_set==medium}
<style>
	#{$id}_containerck .cke_toolbar{ padding: 0px; margin:0px; height: 24px !important; }
	#{$id}_containerck .cke_button{ padding: 4px !important; }
	#{$id}_containerck .cke_button:hover { padding: 3px !important; }
	#{$id}_containerck .cke_button_disabled:hover{ padding: 4px !important; }
	#{$id}_containerck .cke_top{ padding: 0px 3px 6px 3px !important; }
</style>
{/if}

