{include file="default_open.tpl"}


	<link rel="stylesheet" href="{$app->sys_base}vendor/jstree/dist/themes/default/style.min.css" />




	<h1>Interaction and events demo</h1>
	<button id="evts_button">select node with id 1</button> <em>either click the button or a node in the tree</em>
	<div id="evts" class="demo"></div>


  
	<script src="{$app->sys_base}vendor/jstree/dist/jstree.min.js"></script>
	
	<script>
	// html demo

	

	// ajax demo
	/*
	$('#ajax').jstree({
		'core' : {
			'data' : {
				"url" : "./root.json",
				"dataType" : "json" // needed only if you do not supply JSON headers
			}
		}
	});
*/


	// interaction and events
	$('#evts_button').on("click", function () {
		var instance = $('#evts').jstree(true);
		instance.deselect_all();
		instance.select_node('1');
	});
	$('#evts')
		.on("changed.jstree", function (e, data) {
			if(data.selected.length) {
				console.log('The selected node is: ' + data.instance.get_node(data.selected[0]).text);
			}
		})
		.jstree({

			"plugins" : [ "contextmenu" ],		
			'core' : {
				       // so that create works
				"check_callback" : true,
				'multiple' : false,
				'data' : [
					{ "text" : "Root node", "children" : [
							{ "text" : "Child node 1", "id" : 1 },
							{ "text" : "Child node 2" }
					]}
				]
			}
		});
	</script>




{include file="default_close.tpl"}