<img id="image{$id}" src="{$value}"> <input id="text{$id}" name="{$input_name}" type="hiddden" value="{$value|escape}" style="width: calc(90% - 30px)" />


<div id="{$id}"  data-id="{$id}" class="jstree" {if $items}data-items='{json_encode($items)}'{/if} {if $datasource}data-url="{$datasource}"{/if}></div>

	
	{if !$gwcms_input_select_ajax_loaded}
		{$m->addIncludes("upload_input/css", 'css', "`$app_root`static/vendor/jstree/dist/themes/default/style.min.css")}
		<script type="text/javascript">
			translate_submit = "{GW::ln('/g/SUBMIT')}";
			//translate_selectall = "{GW::ln('/g/SELECT_ALL')}";
			//translate_foundresults = "{GW::ln('/g/TOTAL')} {GW::ln('/g/FOUND')}";


			function initTreeInputs(){
				
				
				
				$('.jstree').each(function(){
						console.log($(this).data('url'));
						//var jsdata = JSON.parse($(this).data('items'));
						
						if($(this).data('url')){
							var self = $(this);
							$(this).jstree({
								core : {
									'data' : {
									"url": $(this).data('url'),
									"dataType" : "json"								
									}
								}
							}).on("changed.jstree", function (e, data) {
								if(data.selected.length) {
									console.log('The selected node is: ' + data.instance.get_node(data.selected[0]).text);

									if(data.selected.length != 1 || $('#gwtreeedit').data('id') != data.instance.get_node(data.selected[0]).id ){
										
										var target = data.instance.get_node(data.selected[0]);
										if(target.icon && target.icon != true){
										
											$('#text'+self.data('id')).val(target.icon)
											$('#image'+self.data('id')).attr('src',target.icon)
											
											self.jstree('close_all');
										}
									}
								}
							})					
							
						}
						

				})
				
			}


			require(['gwcms'], function(){  require(['vendor/jstree/dist/jstree.min'], function(){ initTreeInputs() }) });
			
		</script>
		
		
		{assign var=gwcms_input_select_ajax_loaded value=1 scope=global}
	{/if}		