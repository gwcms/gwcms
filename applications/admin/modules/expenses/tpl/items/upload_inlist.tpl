{function name=do_toolbar_buttons_upload}
	{toolbar_button title="Įkelti čekius" iconclass='fa fa-upload' btnclass="select_expenses_btn11" onclick="$('.select_expenses_btn').click()" }

	<div class="attachments_container" style="display:none">
		<button class="select_expenses_btn"></button>
		<span style="display:inline-block;">
			<input style="display:none" class="gwexpensefileinput" type="file" multiple
				data-url="{$m->buildUri(false,[act=>doUpload])}"
				data-name="files[]">
		</span>
	</div>

	{if !isset($GLOBALS.html_inp_expenses_upload)}
		{$GLOBALS.html_inp_expenses_upload=1}
		{capture append=footer_hidden}
			<script type="text/javascript">
				require(['gwcms'], function(){ require(['pack/upload_input/js'], function(){ initExpenseUploadInput() }) });

				function initExpenseUploadInput()
				{
					$('.gwexpensefileinput').each(function(){
						var upload = new Upload($(this));
						$(this).data('upload', upload);
						upload.status_display = $('.select_expenses_btn11');
						upload.init();
						upload.onUpload = function(){
							location.href = location.href;
						}
					});
				}
			</script>
		{/capture}
	{/if}
{/function}
