 {extends file="default_list.tpl"}

{block name="init"}

			
	{$dl_inline_edit=1}


	{if $m->write_permission}
		{$do_toolbar_buttons = [upload]}	
	{/if}
	
	{$do_toolbar_buttons[] = addfolder}	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[dialogconf,print,dialogconf2]}	
	{if $this->write_permission}
		{$do_toolbar_buttons_hidden[] = uploadzip}
	{/if}
	{$do_toolbar_buttons[] = search}
	{$dl_filters = []}
	
	
	
	{function name=do_toolbar_buttons_addfolder} 
		{if $m->write_permission}
			{toolbar_button title=GW::l('/A/VIEWS/doMkDir') iconclass='fa fa-plus-circle' 
				href=$m->buildUri(false,[act=>doMkDir]) query_param=["foldername", "Enter folder name"]}
		{/if}
	{/function}	
	
	{if $m->write_permission}
		{include file="`$m->tpl_dir`/upload_inlist.tpl"}
	{/if}
		
	
	{function dl_cell_ico}
		{if $item->isdir==1}
			<i class="fa fa-folder-o dragable dropable"  data-id="{$item->relpath}"></i>
		{else}
			{if $item->extension=='svg'}
				{$dim=explode('x',$icosize)}
				<img src='{$item->url}' style="width:{$dim.0}px;max-height:{$dim.1}px;">
			{elseif $item->type=='image'}
				<a title="{$item->filename}" href="{$item->url}" data-fancybox-group="repositima" class="fancybox-thumbs">
					<img  class="dragable file" data-file="{$file}" src="{$app->sys_base}tools/img_resize?file={urlencode($item->relpath)}&dirid=repository&size={$icosize}&method=crop&update_time={$item->timestamp}" title="{$dir} {$filename}" alt="{$filename}"  data-id="{$item->relpath}" />
				</a>
			{else}
				<i class="dragable {Mime_Type_Helper::icon($item->path)}" data-id="{$item->rel_path}"></i>
			{/if}
		{/if}	
	{/function}
	
	{function dl_cell_size}
		{$item->humansize}
	{/function}
	
	{function dl_cell_filename}
		{if $item->isdir}
			{*{$parent=$item->relpath}
			{if $smarty.get.parent}{$parent="`$smarty.get.parent`/{}"}
			*}

			<a href="{$m->buildUri(false,[parent=>$item->relpath])}">{$item->filename|truncate:80} ({$item->subfilescount})</a>
		{else}	
			
			{if strlen($item->filename) > 80}
				<span title="{$item->filename|escape}">{$item->filename|truncate:80}{$item->extension}</span>
			{else}
				{$item->filename}
			{/if}
		{/if}
	{/function}	

	
	
	{function dl_actions_preview}
		{if $item->isdir==0}
			{list_item_action_m 
				url=[preview,[id=>$item->id,clean=>1]] iconclass="fa fa-eye" action_addclass="iframe-under-tr"
				tag_params=['data-iframeopt'=>'{"width":"1000px","height":"600px"}']
			}
		{/if}
	{/function}		
	
	
	{$dl_smart_fields=[ico,size,filename]}
	
	{*ext_actions*}
	{$dl_actions=[preview,delete,ext_actions]}
	
	
	
	{capture append="footer_hidden"}
		<script>
			
			var transfer_url = "{$m->buildUri(false, [act=>doDragDrop])}";
			
			function closeFolders(){
				$('.fa-folder-open-o').removeClass('fa-folder-open-o').addClass('fa-folder-o');
			}
			



			
			require(['gwcms'], function(){
				$('.dragable').attr('dragable', true);

				$('.dragable').bind('dragstop', function(){ closeFolders() });
				
				$('.dragable').on('dragstart', function(evt) {
					evt.originalEvent.dataTransfer.setData("text", $(evt.target).data("id"));
				});		
				
				$('.dropable').on('drop', function(evt) {
					evt.preventDefault();

					var data = evt.originalEvent.dataTransfer.getData("text");

					var dropto = $(evt.target).data('id');
					var itemid = data

					
					location.href = gw_navigator.url(transfer_url, { dropto: dropto, itemid: itemid });
				});	
				
				$('.dropable').on('dragover', function(evt) {
					closeFolders();
				
					$(evt.target).removeClass('fa-folder-o').addClass('fa-folder-open-o');
				
					evt.preventDefault();
				});			
				
			})

		</script>

		<style>
			.dl_cell_ico i{ font-size: 24px; margin-top: 1px;margin-bottom: 1px; }
			.dl_cell_ico{ padding: 0px 5px 0 5px !important;vertical-align:middle; }
		</style>

		
	
	{*<script type="text/javascript" src="{$app->sys_base}vendor/fancybox/lib/jquery-1.10.1.min.js"></script>*}

	<!-- Add fancyBox main JS and CSS files -->

		<link rel="stylesheet" type="text/css" href="{$app_root}static/vendor/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
		<!-- Add Thumbnail helper (this is optional) -->
		<link rel="stylesheet" type="text/css" href="{$app_root}static/vendor/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />



			<script type="text/javascript">

				function initFancy()
				{
						$('.fancybox-thumbs').fancybox({
								prevEffect: 'fade',
								nextEffect: 'fade',
								closeBtn: false,
								arrows: true,
								nextClick: true,
								helpers: {
										thumbs: {
												width: 50,
												height: 50
										}
								},
								caption: function (instance, item) {
								     // Display image title
								     return $(this).attr('title');
								 }									
						});
						//$('.fancybox').fancybox();	
				}

				//this will allow open dialog in root window, if this window is iframed
				require(['gwcms'], function(){   
					require(['vendor/fancybox/lib/jquery.mousewheel-3.0.6.pack', 'vendor/fancybox/source/jquery.fancybox'], function(){ 
						require(['vendor/fancybox/source/helpers/jquery.fancybox-thumbs'], function(){ initFancy() })
					})	
				});

			</script>
			{assign var=gwcms_fancybox_initdone value=1 scope=global}


	{/capture}	

	{$dl_checklist_enabled=1}
	{capture append="dl_checklist_actions"}<option value="checked_action_postids('dialogMoveItems')">Perkėlti į kitą katalogą</option>{/capture}
	{capture append="dl_checklist_actions"}<option value="checked_action_postids('downloadmultiple', true)">Parsisiųsti</option>{/capture}
	{capture append="dl_checklist_actions"}<option value="checked_action_postids('{$m->buildUri(false,[act=>doRemoveMultiple])}', true)">Šalinti</option>{/capture}
		

	
{/block}

	{block name="after_list"}
		
		<br />
		<small style="color:silver">Max upload size: {$max_upload_size}</small>
		<small style="color:silver">Max execution time: {ini_get('max_execution_time')}</small>
		
		
	{/block}	