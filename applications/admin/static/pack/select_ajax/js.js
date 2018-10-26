function addEditControls(obj)
{
	this.name = obj.data('name');					
	this.addBtn = obj.find('.addBtn');
	this.editBtn = obj.find('.editBtn');

	this.valueIdsBtn = obj.find('.valueIdsBtn');
	this.valueRowsBtn = obj.find('.valueRowsBtn');

	this.multiple = obj.data('multiple')-0;	



	this.inputctrl = $('#itemform select[name="item['+this.name+']'+(this.multiple?'[]':'')+'"]');

	var ctrl = this;


	this.selected = function (context)
	{
		if(context.item)
		{
			var item = context.item

			var append = ctrl.multiple ? true : false;

			ctrl.inputctrl.trigger('fillitems', [[item], append]);
		}
	}

	this.addBtn.click(function(){
		rootgwcms().open_dialog2({ url: $(this).data('url'), iframe:1, title: this.title, close_callback: ctrl.selected })
	})

	this.editBtn.click(function(){	
		var id = ctrl.inputctrl.val();		
		if(!id)
			return false;

		var url = gw_navigator.url($(this).data('url'), { id: id })
		rootgwcms().open_dialog2({ url: url, iframe:1, title:this.title, close_callback: ctrl.selected })
	})




	this.valueIdsBtn.click(function(){


		var dconf = { width:400,height:250, title: ctrl.valueIdsBtn.attr('title'), buttons: { } }
		var ids = ctrl.inputctrl.val().join(',')
		dconf.html = "<textarea style='width:100%;height:100%' id='importids'>"+ids+"</textarea>";
		dconf.buttons[translate_submit] = function () {
			var ids = $('#importids').val();
			$(this).dialog("close");
			$(this).remove();						
			ids = ids.split(',');
			ctrl.inputctrl.trigger("inittitles", [ids]);
		}

		rootgwcms().open_dialog(dconf)

	});

	this.valueRowsBtn.click(function(){
		var ids = ctrl.inputctrl.val().join(',')



		$.post(obj.data('export_url'), { ids: ids }, function(data){

			var dconf = { title: ctrl.valueRowsBtn.attr('title'), buttons: { } }
			var badrowsconthtml = "<div id='badrowscont' style='margin-bottom:10px;display:none;'>Check bad rows: <textarea style='width:100%;heigh:50px;'></textarea></div>";
			dconf.html = badrowsconthtml+"<textarea id='importrows' style='width:100%;height:100%'>"+data+"</textarea>";
			dconf.buttons[translate_submit] = function () {
				var rows = $('#importrows').val();

				var dialg=$(this);
				$('#badrowscont').fadeOut();

				$.post(obj.data('import_url'), { rows: rows }, function(data){

					data = JSON.parse(data)

					console.log([data.failcnt, (data.failcnt-0)==0])


					if((data.failcnt-0)==0)
					{
						dialg.dialog("close");
						dialg.remove();	

					}else{
						$('#badrowscont').fadeIn();
						$('#badrowscont textarea').val(data.failedrows);
					}



					if(data.errors)
						gw_adm_sys.notification({ text: data.errors.split("\n").join('<br>'), type:2, time: 1000000  });


					gw_adm_sys.notification({ text: data.message })

					ctrl.inputctrl.trigger("inittitles", [data.ids], true);									
				});



			}						

			rootgwcms().open_dialog(dconf)						
		});	

	});					





	this.resetInput = function()
	{
		ctrl.inputctrl.html("")
		ctrl.inputctrl.val("").trigger("change"); 
	}

	//ctrl.on('chageevent', function(){ console.log('change'); })


}
