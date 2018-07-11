
<span class="input-group-btn addEditControls" id="addEditControls{$name}" data-name="{$name}" data-add>
	<button class="btn btn-default editBtn" type="button" title="{$object_title} :: {GW::l('/g/EDIT')}" data-url="{$form_url}"><i class="fa fa-pencil-square-o"></i></button>	
	<button class="btn btn-default addBtn" type="button" title="{$object_title} :: {GW::l('/g/ADD')}"  data-url="{Navigator::buildURI($form_url,[id=>0])}"><i class="fa fa-plus-circle"></i></button>		
</span>
		
	
{if !isset($GLOBALS.init_addEdit_input_done)}		
	{$GLOBALS.init_addEdit_input_done=1}
	<script type="text/javascript">
		//this will allow open dialog in root window, if this window is iframed
		function rootgwcms() {
			try {
			    return window.self !== window.top ?  window.parent.gwcms : gwcms; 
			} catch (e) {
			    return gwcms
			}
		}
		
		require(['gwcms'], function(){
			function addEditControls(obj)
			{
				this.name = obj.data('name');					
				this.addBtn = obj.find('.addBtn');
				this.editBtn = obj.find('.editBtn');
				this.inputobj = $('#itemform select[name="item['+this.name+']"]');
				var obj = this;


				this.selected = function (context)
				{
					if(context.item)
					{
						var item = context.item

						obj.inputobj.html("")
						obj.inputobj.select2("trigger", "select", { data: { id: item.id, text: item.title } });					
					}
				}

				this.addBtn.click(function(){
					rootgwcms().open_dialog2({ url: $(this).data('url'), iframe:1, title: this.title, close_callback: obj.selected })
				})

				this.editBtn.click(function(){	
					var id = obj.inputobj.val();		
					if(!id)
						return false;

					var url = gw_navigator.url($(this).data('url'), { id: id })
					rootgwcms().open_dialog2({ url: url, iframe:1, title:this.title, close_callback: obj.selected })
				})

				this.resetInput = function()
				{
					obj.inputobj.html("")
					obj.inputobj.val("").trigger("change"); 
				}

				//obj.on('chageevent', function(){ console.log('change'); })


			}

			$('.addEditControls').each(function(){ $(this).data('obj', new addEditControls($(this))) })

	});
	</script>
{/if}	

