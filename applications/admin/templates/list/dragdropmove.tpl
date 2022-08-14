
{capture append="footer_hidden"}
		<style>
			.gwroot_node{ color: silver}
			.gwroot_node:first-child{
				padding: 5px;
			}

			body.dragging, body.dragging * {
				cursor: move !important;
			}

			.dragged {
				position: absolute;
				opacity: 0.5;
				z-index: 2000;
			}

			.gwlisttable > tbody > tr {
				cursor: move !important;
			}

			.gwlisttable tr.placeholder {
				display: block;
				background: red;
				position: relative;
				margin: 0;
				padding: 0;
				border: none; }
			/* line 103, /Users/jonasvonandrian/jquery-sortable/source/css/application.css.sass */
			.gwlisttable tr.placeholder:before {
				content: "";
				position: absolute;
				width: 0;
				height: 0;
				border: 5px solid transparent;
				border-left-color: red;
				margin-top: -5px;
				left: -5px;
				border-right: none; }
			.gw_separator {
				background-color: #ffc;
			}


		</style>
		<script>
			var action_url = "{$m->buildUri(false,['act'=>'doSavePositions'])}";
			var rearragefield = "{$dl_dragdropmove_field}";
			var sortabledata;
			
			function saveSortings()
			{
				var tmp = JSON.stringify(sortabledata[0], null, ' ');
				
				$.post(action_url, { 'positions':tmp, 'rearragefield': rearragefield, offset:1 }, function(data){ 

					gwcms.showMessage(data, 0, 500);
					setTimeout("$('#updated_box').fadeOut();",3000);
				})	
			}
			
			
		require(['sortable'],function(){
			
			$(function () {
				
					var sortabegroup=$('.gwListTable').sortable({
						containerSelector: 'table',
						itemPath: '> tbody',
						itemSelector: 'tr',
						placeholder: '<tr class="placeholder"><td colspan="99" style="background-color:yellow">&nbsp;</td></tr>',

						onDrop: function ($item, container, _super) {

						  sortabledata = sortabegroup.sortable("serialize").get();

						  saveSortings();
						 _super($item, container);
						}					
					});		
			})			
			
			

				// Sortable column heads


		});		
		

		</script>
	{/capture}