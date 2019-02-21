{include file="default_open.tpl"}



{*
	<h1>Interaction and events demo</h1>
	<button id="evts_button">select node with id 1</button> <em>either click the button or a node in the tree</em>
*}

	<table class="gwTreeViewTbl">
		<tr>
			<td>
	<div id="gwtree" class="demo"></div>
			</td>
			<td>&nbsp;</td>
			<td style="width:99%">
				<iframe id="gwtreeedit" style="width:99%" frameborder="0"></iframe>
			</td>
	</table>
			
	{$m->addIncludes("upload_input/css", 'css', "`$app_root`static/vendor/jstree/dist/themes/default/style.min.css")}
	
	{capture append=footer_hidden}
	<style>
		.jstree{ background-color: white; padding: 10px; border: 1px solid silver; border-radius: 10px; display:inline-block }
		.gwTreeViewTbl td{ vertical-align: top;}
	</style>

	<script type="text/javascript">
		function initTree(){
			gwcms.initAutoresizeIframe('#gwtreeedit', { minHeight: 100, heightOffset: 0, fixedWidth:true, interval:1000})
			
			
			dataurl = "{$m->buildUri(false,[act=>dogettree])}";
			formurl = "{$m->buildUri(form)}";
			var url = gw_navigator.url(this.href, { packets:1 })
			
				$('#evts_button').on("click", function () {
						var instance = $('#gwtree').jstree(true);
						instance.deselect_all();
						instance.select_node('1');
					});
					
					
					function deleteNode(id)
					{
						$.ajax({});
					}
					
					function createNode()
					{
						$.ajax({});
					}
					
					function action(args, callback)
					{
						var aurl = gw_navigator.url(url, args)
						
						$.ajax({ url: aurl , type: "GET", dataType: "json", success: function (data) { 
								if(callback)
									callback(data)
								else
									gw_adm_sys.runPackets(data);
							}});	
					}		
					
					function openEdit(id)
					{
						
						$('#gwtreeedit').attr('src', gw_navigator.url(formurl, { id: id, clean: 1 }));							
						$('#gwtreeedit').show();
						$('#gwtreeedit').data('id', id)
					}
					
					
					
					$('#gwtree')
						.on("changed.jstree", function (e, data) {
							if(data.selected.length) {
								console.log('The selected node is: ' + data.instance.get_node(data.selected[0]).text);
								
								if(data.selected.length != 1 || $('#gwtreeedit').data('id') != data.instance.get_node(data.selected[0]).id ){
									$('#gwtreeedit').hide();
								}
								
							}
						}).on("create_node.jstree", function (e, data) {
							console.log({ "event":e, "data": data });
							
							action({ act: "doCreateNode", parent: data.parent }, function(response) {
								data.instance.set_id(data.node, response.id);
							});
							
							
							
						}).on('delete_node.jstree', function(e, data){
							
							console.log({ "event":e, "data": data });
						}).on('rename_node.jstree', function(e, data){
							console.log({ "event":e, "data": data });
							
							action({ act: "doRename", title: data.text , id: data.node.id });
						}).on('open_node.jstree', function(e, data){
							console.log({ "event":e, "data": data });
						})
						.on('move_node.jstree', function(e, data){
							console.log({ "event":e, "data": data });
					
					
							action({ act: "doMoveNode", id: data.node.id, parent: data.parent, old_parent: data.old_parent, priority:data.position, old_priority:data.old_position  });
						}).on("dblclick.jstree", function (e, data) {
							
							var instance = $.jstree.reference(this),
							node = instance.get_node(e.target);		
							if(node.id){
									openEdit(node.id);					
							}
							
							console.log({ "event":e, "data": data });
						}).on("deselect_node.jstree", function (e, data) {
							console.log({ "event":e, "data": data });
							

						})	
						
						
						
						
								
						
								
						 
						.jstree({
							"types" : {
							  "t1" : {
								"icon" : "fa fa-folder text-warning",
								"valid_children" : ["t0","t1"],
								"create_node": true
							  },
							  "t0" : {
								"icon" : "fa fa-cog text-brown",
								"valid_children" : ["t1","t0"],
								"create_node": true
							  },
							},							
							"plugins" : [ "contextmenu", 'dnd', "state","unique"],		
							"core" : {
									   // so that create works
								"check_callback" : true,
								'multiple' : false,
								'data' : {
									"url": dataurl,
									"dataType" : "json"								
								}
							}
						});		
		}
		require(['gwcms'], function(){  require(['vendor/jstree/dist/jstree.min'], function(){ initTree() }) });
	</script>
	{/capture}
	{*
	<script>
	// html demo

	

	// ajax demo
	
	$('#ajax').jstree({
		'core' : {
			'data' : {
				"url" : "./root.json",
				"dataType" : "json" // needed only if you do not supply JSON headers
			}
		}
	});



	// interaction and events
	
	</script>
	*}



{include file="default_close.tpl"}