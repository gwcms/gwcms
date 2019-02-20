

{capture assign=addnew}
	
      <span class="input-group-btn">
			{$url=$app->buildUri('competitions/composers/form',[clean=>2,dialog=>1])}
			{*{$url=$app->buildUri('competitions/composers/form',[id=>0,clean=>1,'RETURN_TO'=>$m->buildUri('iframeclose')])}*}
			{**}
			{*gwcms.open_iframe({ url:'{$url}', title:'Sukurti naują kompositorių' });*}
			{*gw_dialog.open('{$url}', { width: 400 })*}
			{**}
			<script>
				function searchComposer(url)
				{					
					gwcms.open_dialog2({ url: url, iframe:1, title:'Pridėti kompositorių', close_callback:composerSelected })
				}
				
				function composerSelected(context){
					
					if(context.item)
					{
						var item = context.item
						///alert('sukurtas naujas compositorius id: '+item.id+'. vardas: '+item.title);
						
						
						var autocompleteInput = $('#itemform select[name="item[composer_id]"]');
						
						autocompleteInput.html("")
						
						
						//autocompleteInput.val("CA").trigger("change"); 
						
						autocompleteInput.select2("trigger", "select", { data: { id: item.id, text: item.title } });
					}
				}
				
				
				function editComposer(url)
				{	
					var composer_id = $('#itemform select[name="item[composer_id]"]').val();	
					
					url = gw_navigator.url(url, { id: composer_id })
					gwcms.open_dialog2({ url: url, iframe:1, title:'Redaguoti kompozitorių', close_callback:composerSelected })
				}
				
				function composerChanged(idchanged)
				{
					{if $composer_composition_chained}
						if(idchanged){
							gwcms.showMessages([{ type:0, 'text': 'Kūrinio nustatymas perkrautas', title:"Kompozitorius pakeistas" }])
							resetComposition();
						}else{
							gwcms.showMessages([{ type:0, title:"Kūrinys atnaujintas" }])
						}
					{/if}
				}
				
				function resetComposer()
				{
						var autocompleteInput = $('#itemform select[name="item[composer_id]"]');
						autocompleteInput.html("")
						autocompleteInput.val("").trigger("change"); 
				}					
				
				
				
			</script>
		  
		<button class="btn btn-default" type="button" onclick="searchComposer('{$url}')"><i class="fa fa-plus-circle"></i></button>
		<button class="btn btn-default" type="button" onclick="editComposer('{$url}')"><i class="fa fa-pencil-square-o"></i></button>		  
      </span>
	
	
{/capture}

{call e field="composer_id" 
	type=select_ajax 
	rowclass="composerslist" 
	maximumSelectionLength=1
	options=$options.composer_id
	onchangeFunc="composerChanged"
	datasource=$app->buildUri('competitions/composers/search') 
	after_input=$addnew
}
	
