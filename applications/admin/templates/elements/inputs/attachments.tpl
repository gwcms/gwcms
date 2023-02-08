{$owner_type="{$m->module_path.0}/{$m->module_name}"}
{$owner_params=['field'=>$name,'owner_type'=>$owner_type,'owner_id'=>$item->id,'owner_temp_id'=>$item->temp_id]}
{$dropid="drop_{md5(json_encode($owner_params))}"}

{$x=$app->sess("attachments/{$owner_type}",$valid)}

{*PATAISYT KLAIDA*}


<div class="attachments_container">
	
	<div class="attachments_drop" id='{$dropid}'>loading..</div>
	{if !$readonly}
	<a href="#" class="select_attachments_btn"><i class='fa fa-plus-circle'></i> {GW::l('/M/datasources/ADD_ATTACHMENT')}</a>
	{/if}

	<span style='display:{if !$readonly}inline-block;{else}none{/if}'>
		
		<input style="display:none" class="gwfileinput" type="file" multiple
	       data-url="{$app->buildUri('datasources/attachments/listajax',[dropid=>$dropid,preview=>$preview,readonly=>$readonly]+$owner_params)}" 
	       data-name='files[]' 
	       >
	{if !$readonly}	
		<div style="display:inline-block" class="status"></div> <br />
		<div class="progress-bar"></div>
	{/if}
	</span>
	
	
</div>

{if !isset(GW::$globals.html_inp_attachments)}
	{GW::$globals.html_inp_attachments=1}
	
	{$m->addIncludes("upload_input/css", 'css', "`$app_root`static/pack/upload_input/css.css")}
	
	{capture append=footer_hidden}

	<script type="text/javascript">

	require(['gwcms'], function(){  require(['pack/upload_input/js'], function(){ initUploadInput() }) });

	function initUploadInput()
	{
		$('.gwfileinput').each(function(){		
			var upload = new Upload($(this));
			$(this).data('upload', upload)

			upload.init();
		})
		
	}

	</script>

	{/capture}

{/if}

