<div id="{$id}_containerck" class="{$class}">

	{*
	to see saved
	<textarea style="width:100%;height:100px">
{$value|escape}	
	</textarea>
	*}
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

{*jei per meilams siusti dadeti SITE_URL *}

<script>
//var CKEDITOR_BASEPATH = '/vendor/ckeditor1/';	
	
require(["ckeditor422"], function() {
	
	CKEDITOR.timestamp = null;//neveikia extra pluginsai
 
	var config = {if $ck_options}{json_encode($ck_options)}{else} { } {/if} ;
	config.width = 'auto';

	//config.defaultLanguage = 'LT';
	config.language = '{$app->ln}';

	config.toolbarCanCollapse = true;
	
	
	config.extraPlugins = (config.extraPlugins ? config.extraPlugins + ',':'') + 'codemirror,filebrowser,resize';
	
	config.removePlugins =  'forms';
	//config.removeButtons = 'Source,AutoFormat,CommentSelectedRange,UncommentSelectedRange,SearchCode';
	
	config.protectedSource = [];
	{literal}config.protectedSource.push( /\{[\s\S]*?\}/g );{/literal}
	

	config.extraPlugins += ',showprotected1';

	
	{if $height}
		config.height = "{$height}";
	{else}
		config.autoGrow_minHeight = 200;
		config.autoGrow_maxHeight = 600;
		config.autoGrow_bottomSpace = 50;
		config.extraPlugins += ',autogrow';
	{/if}
	config.filebrowserBrowseUrl = '/admin/{$app->ln}/sitemap/repository/fileselect{if $abspath}?abspath=1{/if}';
	//config.filebrowserUploadUrl = '/admin/{$app->ln}/sitemap/repository/upload';	
	 
	{if $ck_set==minimum}
	config.toolbarStartupExpanded = false;
	config.toolbar = 'Basic'
	{/if}
	 
	 config.entities = false;
	 config.basicEntities = false;
	 config.entities_greek = false;
	 config.entities_latin = false;
	 
	{*nes stiliaus taga isima*}
	 
	 
	{if $abspath} {*99% kad bus el laiskam ar pan tada leist head style ir tt elemetus*}
		config.allowedContent = true;
		config.fullPage = true;
	{/if}

	 config.autoParagraph = false;
	 config.coreStyles_bold = { element: 'b', overrides: 'strong' };
	 
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
		
		//anoying ctr+s shortcut always fails, ctrl+1
		$('.cke_wysiwyg_frame').each(function(o){
			this.contentWindow.document.addEventListener('keydown', e => {
				
				$("body").get(0).dispatchEvent(
				  new KeyboardEvent('keydown', e)
				);
			
				//prevent default for ctrl+s
				if(e.which == 83 && (e.ctrlKey || e.metaKey)){
					e.preventDefault();
				}			
			
			
			})
		})
		
		
		//nenukeliautu kode esantis >
		ev.editor.on('getData', function (evt) {
			//protected1 isimam
			 evt.data.dataValue = evt.data.dataValue.replace(/<span[^>]*data-protected="([^"]+)"[^>]*>(.*?)<\/span>/g,
					function (match, code, innerContent) {
					    return innerContent; // Remove span but keep content
					}
				);
			evt.data.dataValue = evt.data.dataValue.replace(/&gt;/g, '>').replace(/&lt;/g, '<');
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

