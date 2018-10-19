{function name=do_toolbar_buttons_upload}
		
			{toolbar_button title=GW::l('/m/VIEWS/doStore') iconclass='fa fa-upload'  btnclass="select_attachments_btn11" onclick="$('.select_attachments_btn').click()" }	

		<div class="attachments_container" style='display:none'>
			
			<button class="select_attachments_btn"></button>
			<span style='display:inline-block;'>

				<input style="display:none" class="gwfileinput" type="file" multiple
				   data-url="{$m->buildUri(false)}" 
				   data-name='files[]' 
				   >
			</span>
		</div>

{if !isset($GLOBALS.html_inp_attachments)}
	{$GLOBALS.html_inp_attachments=1}
	
	
	{capture append=footer_hidden}

	<script type="text/javascript">

	require(['gwcms'], function(){  require(['pack/upload_input/js'], function(){ initUploadInput() }) });

	function initUploadInput()
	{
		$('.gwfileinput').each(function(){		
			var upload = new Upload($(this));
			$(this).data('upload', upload)
			
			upload.status_display=$('.select_attachments_btn11')
			upload.init();
			
			upload.onUpload = function(){
				console.log('upload successfull!');
				location.href = location.href;
			}
			
		})
		
	}

	</script>

	{/capture}

{/if}		
	{/function}
	
	
{function name=do_toolbar_buttons_uploadzip}
	
	{toolbar_button title=GW::l('/m/VIEWS/doUploadZip') iconclass='fa fa-upload' onclick="$('#zipuploadinput').click()" }	
	
<form id="zipuploadform" action="{$smarty.server.REQUEST_URI}" method="post"  enctype="multipart/form-data" style="display:none" >
	<input type="hidden" name="act" value="doUploadZip" />
	<input id="zipuploadinput" type="file" name="zipfile" multiple="multiple" onchange="$('#zipuploadform').submit()" />
</form>

{/function}