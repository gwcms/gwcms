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
			jstreetypes = {json_encode($jstree_types)}
			var url = gw_navigator.url(this.href, { packets:1 })
			var urlh = location.href.split('#');
			
					
					
			function backEndAction(args, callback)
			{
				var aurl = gw_navigator.url(url, args)

				$.ajax({ url: aurl , type: "GET", dataType: "json", success: function (data) { 
					if(callback)
						callback(data)
					elsea
						gw_adm_sys.runPackets(data);
				}});	
			}		

			function openEdit(id)
			{	
				$('#gwtreeedit').attr('src', gw_navigator.url(formurl, { id: id, clean: 1 }));							
				$('#gwtreeedit').show();
				$('#gwtreeedit').data('id', id)
			}
			
			var hash = window.location.hash.substring(1)
			if(hash){
				openEdit(hash);
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
					backEndAction({ act: "doCreateNode", parent: data.parent }, function(response) {
						data.instance.set_id(data.node, response.id);
					});
				}).on('delete_node.jstree', function(e, data){

					e.stopImmediatePropagation();
					console.log({ "event":e, "data": data });
				}).on('rename_node.jstree', function(e, data){
					console.log({ "event":e, "data": data });

					backEndAction({ act: "doRename", title: data.text , id: data.node.id });
				}).on('open_node.jstree', function(e, data){
					console.log({ "event":e, "data": data });
				})
				.on('move_node.jstree', function(e, data){
					console.log({ "event":e, "data": data });
					backEndAction({ act: "doMoveNode", id: data.node.id, parent: data.parent, old_parent: data.old_parent, priority:data.position, old_priority:data.old_position  });
				}).on("dblclick.jstree", function (e, data) {

					var instance = $.jstree.reference(this),
					node = instance.get_node(e.target);		
					if(node.id){
							openEdit(node.id);	
							gw_navigator.switchHash(node.id);
					}

					console.log({ "event":e, "data": data });
				}).on("deselect_node.jstree", function (e, data) {
					console.log({ "event":e, "data": data });


				}).on('loaded.jstree', function(e, data){

					if(urlh.length==2){
						var id = urlh[1];
						data.instance.deselect_all();
						data.instance.select_node(id);
						openEdit(id);
					}
				})
				.jstree({
					"types" : jstreetypes,							
					"plugins" : [ "contextmenu", 'dnd', "state","unique", 'types'],		
					"core" : {
							   // so that create works
						"check_callback" : true,
						'multiple' : false,
						'data' : {
							"url": dataurl,
							"dataType" : "json"								
						}
					},
					"contextmenu" : {
						items : function (node) {
							var contextmenu = $.jstree.defaults.contextmenu.items(node);
							contextmenu.remove.action = function (data) {
								var inst = $.jstree.reference(data.reference),
									obj = inst.get_node(data.reference);
								// show your confirm dialog here
								// in its confirm callback execute the code below:
			
								if(confirm('Are you sure you want delete node "'+obj.text+'"')){
									backEndAction({ act: "doDelete", id: obj.id });
									inst.delete_node(obj); 
									
								}
								// function () { inst.delete_node(obj); }
							}
							return contextmenu;
						}
					}
					
				})
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