

var gw_changetrack = {
	
	
	init: function(formclass)
	{
		
		$(formclass).submit(function(){
			
			
		});
		
		var ovals=$('<input class="original_values" name="original_values" type="hidden">').appendTo(formclass)
				
		
		$(formclass).find("input[type='radio'], input[type='checkox'], input[type='radio'], select, textarea").each(function(){

			$(this).attr('data-origval', $(this).val());

			data = $(this).serializeArray();
			
			
			
			vals = ovals.val() ? JSON.parse(ovals.val()) : {};
			
			for(var i in data)
				vals[data[i]['name']]=data[i]['value'];
			
					
			ovals.val(JSON.stringify(vals));

		}).on('input propertychange change', function() {

			console.log($(this).attr('id')+' changed');
			changedobj = $(this);


			gw_changetrack.isChanged(changedobj);

			//mark field as changed
			//$(this).data('checkIsChanged')();
			//
			//
			//
			//
			//isvalomas nepasibaiges automatinio saugojimo timeout
			clearTimeout($(this).data('timeoutid'));
			var timeoutid = setTimeout(function() {
				// Runs 1 second (2000 ms) after the last change    
				//vykdytisaugojima()

				//gw_changetrack.doSaveField(changedobj)
			}, 2000);

			$(this).data('timeoutid', timeoutid);

		});
		
	},
	
	
	
	isChanged: function(obj)
	{
		if(obj.attr('data-origval')!=obj.val())
		{
			$('#'+obj.attr('id')+'_inputLabel').addClass("gwinput-label-modified");
		}else{
			$('#'+obj.attr('id')+'_inputLabel').removeClass("gwinput-label-modified");
		}
		
	},
	
	doSaveField: function(obj)
	{
		
		//i forma reikia itraukti pakeistus laukelius ir sisteminius
		
		console.log(obj.attr('id')+' dosave');
		//console.log()
		var fielddata=obj.serialize();
		var form=obj.parents('form')
		var sysfields=form.find('.gwSysFields');
		
		var data=fielddata+'&'+sysfields.serialize()+'&ajax=1';
		
		console.log(data);
		
		
		
		$.post(form.attr('action'), data,
				function (data, status, request) {
						
						if (request.getResponseHeader('GW_AJAX_FORM') == 'OK')
						{
							
																
								var id = request.getResponseHeader('GW_AJAX_FORM_ITEM_ID');
								var title = request.getResponseHeader('GW_AJAX_FORM_ITEM_TITLE');
								var messages = request.getResponseHeader('GW_AJAX_MESSAGES');
								
								gwcms.showMessages(JSON.parse(messages), title);
								
								
								console.log(data);
								
								
								//gw_navigator.jump(location.href, {id:id})
						} else {
								console.log('failed submit to:'+form.attr('action')+', status: '+status);
								console.log(data);
								
						}

				}
		);		
		
	}
	
	
	
	
}

