{extends file="default_list.tpl"}

{block name="init"}

	
	
	{$dl_inline_edit=1}



	{$do_toolbar_buttons = [upload]}	
	{$do_toolbar_buttons[] = addfolder}	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[dialogconf,print]}	
	{$do_toolbar_buttons[] = search}
	
	
	
	{function name=do_toolbar_buttons_addfolder} 
		{toolbar_button title=GW::l('/A/VIEWS/doMkDir') iconclass='fa fa-plus-circle' 
			href=$m->buildUri(false,[act=>doMkDir,foldername=>""]) query_param="Enter folder name"}	
	{/function}	
	
	
	{function name=do_toolbar_buttons_upload}
		
			{toolbar_button title=GW::l('/m/VIEWS/doStore') iconclass='fa fa-upload'  btnclass="select_attachments_btn11" onclick="$('.select_attachments_btn').click()" }	

		<div class="attachments_container" style='display:none'>
			
			<button class="select_attachments_btn"></button>
			<span style='display:inline-block;'>

				<input style="display:none" class="gwfileinput" type="file" multiple
				   data-url="{$app->buildUri(false)}" 
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
	
	

	
	
	{function dl_cell_ico}
		{if $item->isdir==1}
			<i class="fa fa-folder-o"></i>
		{else}
			
			
			{if $item->type=='image'}
				<img class="file" data-file="{$file}" src="{$app->sys_base}tools/img_resize?file={urlencode($item->relpath)}&dirid=repository&size=16x16" title="{$dir} {$filename}" alt="{$filename}" />
			{else}
				<i class="{Mime_Type_Helper::icon($item->path)}"></i>
			{/if}
		{/if}	
	{/function}
	
	{function dl_cell_size}
		{$item->humansize}
	{/function}

	
	
	{function dl_actions_preview}
		{if $item->isdir==0}
			{list_item_action_m url=[preview,[id=>$item->id,clean=>1]] iconclass="fa fa-eye" action_addclass="iframe-under-tr"}
		{/if}
	{/function}		
	
	
	{$dl_smart_fields=[ico,size]}
	
	
	{$dl_actions=[preview,edit,delete,ext_actions]}
	

	
{/block}

