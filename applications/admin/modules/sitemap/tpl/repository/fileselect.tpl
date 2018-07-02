{include "head.tpl"}
    

<div id="gwFileBrowser" class="folder" data-dir="/" data-url="{$m->buildUri(fileslist)}"></div>


	<style>
		#gwFileBrowser .dircontents{
			margin-left: 10px;
		}
		#gwFileBrowser{
			background-color: white;
			padding: 10px;
			color: #444;
		}
		.selectedFile{ color: white; background-color: navy; }
		.folderlink, .file{ cursor:pointer; }
		
		.folderlink:hover, .file:hover{ color:#000; }
		img.file{ opacity: 0.6 }
		img.file:hover{ opacity: 1 }
		.selectedFile{ opacity: 1 !important; }
		
		.selectedFile:hover{ color:#eee; }
		.markselecteddir{ color: green !important; }
	</style>	
	{$m->addIncludes("upload_input/css", 'css', "`$app_root`static/pack/upload_input/css.css")}
	
	{capture append=footer_hidden}

	<script type="text/javascript">
		
		

	

	require(['gwcms'], function(){  require(['pack/browse_repository/js'], function(){ initBrowseRepos() }) });

	function initBrowseRepos()
	{
		var funcNum = getUrlParam( 'CKEditorFuncNum' )
		window.opener.CKEDITOR.tools.callFunction( funcNum, '', function() {
		       var type=this.getDialog().getName() == 'image' ? 'image' : 'file';
		       var br = new BrowseRepository($('#gwFileBrowser'), type);
		       br.init();
		       
		       
		       if(type=="image")
			       $('#imageOpts').addClass('imageOptsEnabled');
		});
	}

	</script>
	{/capture}

 
    
    {foreach $files as $file}
	    <input type="checkbox" name="file" value="{$file}"> {$file}</br>
{/foreach}
    
    




<div style="display:none" id="imageOpts">
	Width <input type="number" id="width" value="300">
	Height <input type="number" id="height" value="300">
</div>

<input type="text" id="filename" style="width:300px;">
    <button id="returnBtn">Select File</button>
    
    
    
    
<input id="fileinput" style="display:none" class="gwfileinput" type="file" multiple
	       data-url="{$m->buildUri(false,[act=>doUpload])}" 
	       data-name='files[]' 
	       >    


<span class="status"></span>
<span class="progress-bar"></span>  
    

{include "default_close_clean.tpl"}
    
