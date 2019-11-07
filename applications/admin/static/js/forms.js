gw_forms = {
	enabled_langs: {},
	
	initI18nForm: function(langs)
	{
		gw_forms.enabled_langs = langs;
		
		$('.i18n_tag').click(function(){ 
			if($(this).hasClass('i18n_tag_active')){
				$(this).removeClass('i18n_tag_active')
				
				var i=0
				for(var ln in gw_forms.enabled_langs)
				{
					var ln_containers =$(this).parents('td:first').next('.input_td').find('.ln_contain_'+ln);
					
					i == 0 ? ln_containers.fadeIn() : ln_containers.fadeOut()
					
					i++;
					
				}
				
				
			}else{
				for(var ln in gw_forms.enabled_langs)
					$(this).parents('td:first').next('.input_td').find('.ln_contain_'+ln).fadeIn();
					
				
				$(this).addClass('i18n_tag_active')
			}
			
			
		})
	
		$('.gwform_sw_ln').click(function(){
			$(this).parents('.ln_contain').fadeOut();
		})
		
		$('.ln_cont_oth').hide();
		$('.i18n_expand').fadeIn();
	},	
	
	lnEnable:function(ln, state, trigg){
		
		$('.ln_contain_'+ln).toggle(state==1)
		
		if(state==1){
			gw_forms.enabled_langs[ln]=1;
		}else{
			delete gw_forms.enabled_langs[ln];
		}
		
		$(trigg).parents('.input_td').find('.ln_contain').show();
	},
	
	initForms: function(){
		
		$(function(){
			$('#itemform').data('originalvals', $('#itemform').serializeArray());	
									
			if(changes_track){
					gw_changetrack.init('.itemform');
			}
		})

		$('#itemform').submit(function() {
			$(this).trigger( "beforesubmitevents", [ "Custom", "Event" ] );
			
			window.onbeforeunload = null;
		});	


		window.onbeforeunload = function() {
			if(gw_changetrack.isFormValuesChanged())
				return "You have made changes on this page that you have not yet confirmed. If you navigate away from this page you will lose your unsaved changes";
		}
		
		//fix rotated label position
		$('.rotate-lbl').parent().keypress(function(){
			if($(this).data('lastheight') != $(this).height())
			{
				$(this).find('.rotate-lbl').css('top', ($(this).height()-10)+'px')
				$(this).data('lastheight', $(this).height());
			}
		});
		
		$(window).bind('keydown', function(event) {
		    if (event.ctrlKey || event.metaKey) {
			switch (event.which) {
			case 83:
				$('#itemform').get(0).elements['submit_type'].value=1; //apply (stay in form after save)
				$('#itemform').submit();
				event.preventDefault();
			break;
		    }
		}});		
	}	
}

var gw_changetrack = {
	
	
	readValue: function(selector)
	{
		var o=$(selector);
		
		if(o.attr('type')=='checkbox')
			return o.is(":checked") ? 1 : 0;
		
		return o.val();
	},
	
	
	init: function(formclass)
	{
		
		$(formclass).submit(function(){
			
			
		});
		
		//form changes loss protection
		var ovals=$('<input class="original_values" name="original_values" type="hidden">').appendTo(formclass)
				
		
		$(formclass).find("input[type='text'], input[type='radio'], input[type='radio'], select, textarea, .gwcheckboxinput").each(function(){

			
			$(this).attr('data-origval', gw_changetrack.readValue(this));

			data = $(this).serializeArray();
			
			
			
			vals = ovals.val() ? JSON.parse(ovals.val()) : {};
			
			for(var i in data)
				vals[data[i]['name']]=data[i]['value'];
			
					
			ovals.val(JSON.stringify(vals));
			
			//console.log(JSON.stringify(vals))
		}).on('input propertychange change click', function() {

			
			var changedobj = $(this);
			var changed = gw_changetrack.isChanged(changedobj);
			
			//mark field as changed
			//$(this).data('checkIsChanged')();
			//
			//
			//
			//
			//isvalomas nepasibaiges automatinio saugojimo timeout
			var elm=this;
			
			if(gw_auto_save && changed){
				
				clearTimeout($(this).data('timeoutid'));
				var timeoutid = setTimeout(function() {

					// Runs 1 second (2000 ms) after the last change    
					//vykdytisaugojima()
					gw_changetrack.doSaveField([changedobj])
					
				}, 5000);

				$(this).data('timeoutid', timeoutid);
			}

		});
		
	
		
		$(window).bind('keydown', function(event) {
		    if (event.altKey) {
			    console.log(event.which)
			switch (event.which) {
			case 83: //s
			    event.preventDefault();
			    
			    var objs = []; 
			    $('.gwinput-modified').each(function(){ objs.push($(this)) })
			    gw_changetrack.doSaveField(objs) 
			    
			    
			    break;
			}
		    }
		});		
		
		
	},
	
	
	
	isChanged: function(obj)
	{
		var lab = $('#'+obj.attr('id')+'_inputLabel');
		
		if(obj.attr('data-origval')!=gw_changetrack.readValue(obj))
		{
			//console.log(obj.attr('name')+' changed');
			
			lab.addClass("gwinput-label-modified");
			obj.addClass('gwinput-modified')
			
			if(gw_auto_save)
				if(lab.find('.fa-floppy-o').length==0){
					lab.append('<i class="fa fa-floppy-o text-muted"></i>')
				}else{
					lab.find('.fa-floppy-o').animate({color: "silver"}, 500 );
				}
			
			return true;
		}else{
			lab.removeClass("gwinput-label-modified");
			obj.removeClass('gwinput-modified')
			return false;
		}
	},
	
	doSaveField: function(obj) // array of inputs
	{
		//i forma reikia itraukti pakeistus laukelius ir sisteminius
		
		//console.log(obj.attr('id')+' dosave');
		
		for(var i in obj){
			obj[i].attr('data-neworigval', gw_changetrack.readValue(obj[i]));
		}
		
		console.log('JEI BUS DVI FORMOS REIK PERZIURET KODA');
		var form=obj[0].parents('form')
		
		var sysfields=form.find('.gwSysFields');
		var data=sysfields.serialize()+'&ajax=1';
		
		for(var i in obj){
			data += '&'+obj[i].serialize();
			
			var lab = $('#'+obj[i].attr('id')+'_inputLabel');
			var labsave = lab.find('.fa-floppy-o');

			labsave.animate({color: "orange"}, 500 );			
		}			
		
		$.post(form.attr('action'), data,
			function (data, status, request) {

				if (request.getResponseHeader('GW_AJAX_FORM') == 'OK')
				{
					
					for(var i in obj){
						obj[i].attr('data-origval', obj[i].attr('data-neworigval'))
						gw_changetrack.isChanged(obj[i]);
					}	

					var id = request.getResponseHeader('GW_AJAX_FORM_ITEM_ID');
					var title = request.getResponseHeader('GW_AJAX_FORM_ITEM_TITLE');
					var messages = request.getResponseHeader('GW_AJAX_MESSAGES');

					//gwcms.showMessages(JSON.parse(messages), title);
					
					data = JSON.parse(data);
					var last_update_time = form.find('[name="last_update_time"]');
					if(last_update_time.length && data.hasOwnProperty('last_update_time'))
					{
						console.log('Lastupdate time: '+last_update_time.val()+' => '+data.last_update_time);
						last_update_time.val(data.last_update_time);
					}
					
								
					for(var i in obj)
						$('#'+obj[i].attr('id')+'_inputLabel').find('.fa-floppy-o').animate({color: "green"}, 1000 );	

						//gw_navigator.jump(location.href, {id:id})
				} else {
	
					for(var i in obj)
						$('#'+obj[i].attr('id')+'_inputLabel').find('.fa-floppy-o').animate({color: "red"}, 1000 );			
											
					
					console.log('failed submit to:'+form.attr('action')+', status: '+status);
					console.log(data);	
				}

			}
		);		
		
	},
	
	
	animateChanged: function (obj, speed)
	{
		//obj.fadeOut('slow', function(){ $(this).fadeIn('slow') }).animate({backgroundColor: "#003311",color: "#fff"}, 300 );
		
		var curr_bgcolor = obj.css("background-color");
		var curr_color = obj.css("color");

		obj.animate({backgroundColor: "#FFA500",color: "#fff"}, 1000 );

		setTimeout(function(){
				obj.animate({backgroundColor: curr_bgcolor, color: curr_color}, speed ? speed/2 : 300 );
		}, (speed ? speed/2 : 300))

	},
	
	isFormValuesChanged: function(){
		$('#itemform').data('newvals', $('#itemform').serializeArray());	
			
		var orig_vals = gw_changetrack.recodeArray($('#itemform').data('originalvals'));					
		var new_vals = gw_changetrack.recodeArray($('#itemform').data('newvals'))

		delete new_vals['original_values'];

		var changesfound = false;

		for(var field in orig_vals)
		{
			
			if(!new_vals.hasOwnProperty(field))
				new_vals[field] ='';
			
			if(JSON.stringify(orig_vals[field]) != JSON.stringify(new_vals[field])){
								
				var fieldname = field + ( Array.isArray(orig_vals[field]) ? '[]' :'');
				var obje=$('#itemform [name="'+fieldname+'"]');
				
				if(obje.data('ignorechanges'))
					continue;
				
				var container = obje.parents('tr:first');
				
				if(changes_track && !gw_changetrack.isChanged(obje))
				{
					console.log(field+': autosave field subsystem, ignored');
					continue;
				}
				
				gw_changetrack.animateChanged(container.fadeIn(), 3000)
				//$('#'+fieldid+'_inputLabel').addClass("gwinput-label-modified")
				
				console.log("Change found in field: "+field+' before: '+orig_vals[field]+' now: '+new_vals[field])
				changesfound = true;
			}
		}	
		
		return changesfound;
	},
	
	recodeArray: function(arr)
	{
		var newvals = { }
		for(var field in arr)
		{
			var it = arr[field]
			if(it.name.slice(-2)=='[]'){
				var newkey=it.name.substring(0,it.name.length-2);

				if(!newvals.hasOwnProperty(newkey)){
					newvals[newkey] = [];
				}

				newvals[newkey].push(it.value);
			}else{
				newvals[it.name]=it.value
			}

		}
		return newvals;
	}
	
	
}



