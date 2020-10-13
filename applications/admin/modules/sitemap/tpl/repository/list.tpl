 {extends file="default_list.tpl"}

{block name="init"}

			
	{$dl_inline_edit=1}



	{$do_toolbar_buttons = [upload]}	
	{$do_toolbar_buttons[] = addfolder}	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[dialogconf,print,uploadzip]}	
	{$do_toolbar_buttons[] = search}
	{$dl_filters = []}
	
	
	
	{function name=do_toolbar_buttons_addfolder} 
		{toolbar_button title=GW::l('/A/VIEWS/doMkDir') iconclass='fa fa-plus-circle' 
			href=$m->buildUri(false,[act=>doMkDir]) query_param=["foldername", "Enter folder name"]}	
	{/function}	
	
	
	{include file="`$m->tpl_dir`/upload_inlist.tpl"}
		
	
	{function dl_cell_ico}
		{if $item->isdir==1}
			<i class="fa fa-folder-o dragable dropable"  data-id="{$item->relpath}"></i>
		{else}
			{if $item->type=='image'}
				<img class="dragable file" data-file="{$file}" src="{$app->sys_base}tools/img_resize?file={urlencode($item->relpath)}&dirid=repository&size=30x24&method=crop" title="{$dir} {$filename}" alt="{$filename}"  data-id="{$item->relpath}" />
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
		
		

	{/capture}	

	{$dl_checklist_enabled=1}
	{capture append="dl_checklist_actions"}<option value="checked_action_postids('dialogMoveItems')">Perkėlti į kitą katalogą</option>{/capture}
	{capture append="dl_checklist_actions"}<option value="checked_action_postids('downloadmultiple', true)">Parsisiųsti</option>{/capture}
	{capture append="dl_checklist_actions"}<option value="checked_action_postids('{$m->buildUri(false,[act=>doRemoveMultiple])}', true)">Šalinti</option>{/capture}
		

	
{/block}

	{block name="after_list"}
		
		<br />
		<small style="color:silver">Max upload size: {$max_upload_size}</small>
		
		
	{/block}	