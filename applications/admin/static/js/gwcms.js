/*USED*/






var GW = {
	zero: function (num, count)
	{
		var add = count - String(num).length;
		while (add > 0) {
			num = '0' + num;
			add--
		}
		return num;
	},
	path_parent: function ()
	{
		return GW.app_base + GW.ln + '/' + gw_navigator.dirname(GW.path)
	},
	dump: function (obj)
	{
		try {
			console.log(obj)
		} catch (err) {
		}
	},
	copy_to_clipboard: function (text) {

		// Create a "hidden" input
		var aux = document.createElement("input");

		// Assign it the value of the specified element
		aux.setAttribute("value", text);

		// Append it to the body
		document.body.appendChild(aux);

		// Highlight its content
		aux.select();

		// Copy the highlighted text
		document.execCommand("copy");

		// Remove it from the body
		document.body.removeChild(aux);

	},
	
	uptime: function(timeinsecs)
	{
		var m = Math.floor(timeinsecs / 60);
		var s = timeinsecs - m * 60;
		var str = (m ? m + ' m ' : '') + (m < 3 ? s + ' s' : '');
		return str;
	}
}

/*USED*/
var gw_navigator = {
	implode_url: function (url)
	{
		return url.base + (url.query ? '?' + $.param(url.query) : '') + (url.anchor ? '#' + url.anchor : '')
	},
	explode_url: function (url) {

		if (!url)
			url = location.href;

		var split = url.split(/[\?\#]/);

		var tmp1 = {
			base: split[0],
			query: split[1] ? gw_navigator.explode_args(split[1]) : {},
			anchor: split[2] ? split[2] : ''
		}

		return tmp1;
	},
	unescape: function (str) {
		return unescape(str.replace('+', ' '));
	},
	explode_args: function (args) {
		if (!args)
			return {};

		args = args.split('&');
		var arr = {}, tmp, i

		for (i in args) {
			tmp = args[i].split('=');
			arr[gw_navigator.unescape(tmp[0])] = tmp[1] ? gw_navigator.unescape(tmp[1]) : false;
		}
		return arr;
	},
	url: function (url, params)
	{
		if (!url)
			url = window.location.href;

		if (typeof params == 'object')
		{
			url = gw_navigator.explode_url(url);
			
			if(params.baseadd){
				url.base += params.baseadd
				delete params.baseadd;
			}

			$.extend(url.query, params);
						
			url = gw_navigator.implode_url(url);
		}

		return url
	},
	jump: function (url, params)
	{
		window.location = gw_navigator.url(url, params);
		return false;
	},
	//http://planetozh.com/blog/2008/04/javascript-basename-and-dirname/
	dirname: function (path) {
		return path.replace(/\\/g, '/').replace(/\/[^\/]*$/, '');
	},
	removeUserPassFromUrl: function (url) {
		var parts = url.split('@')

		if (parts.length == 1)
			return url;

		return parts[0].split('//')[0] + '//' + parts[1];
	},
	switchHash: function(hash){
		var url = location.href;
		
		var parts = url.split('#')
		
		window.location.replace(parts[0]+'#'+hash)
	},
	


	post: function(path, params, method) {
		method = method || "post"; // Set method to post by default if not specified.
		var form = document.createElement("form");
		form.setAttribute("method", method);
		form.setAttribute("action", path);

		for (var key in params) {
			if (params.hasOwnProperty(key)) {
				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", key);
				hiddenField.setAttribute("value", params[key]);

				form.appendChild(hiddenField);
			}
		}

		document.body.appendChild(form);
		form.submit();
	}
}

var gw_adm_sys = {
	paging_select_box: function (object, from_val, to_val)
	{
		var opt = {}
		while (from_val <= to_val)
		{
			opt[from_val] = from_val;
			from_val++
		}
		$(object).addOption(opt, false);
	},
	change_page: function (page)
	{
		gw_navigator.jump(false, {'act': 'doSetListParams', 'list_params[page]': page})
		return false;
	},
	init: function ()
	{
		gwcms_project.init();
		GW.init_time = new Date().getTime();
		$(document).ready(gw_adm_sys.init_after_load);
		gw_adm_sys.initObjects();
		
	},
	init_after_load: function ()
	{
		gw_session.init();
		gw_server_time.init();
		/*USED*/

		//gw_login_dialog.open();
	},
	updateNotifications: function (count)
	{

	},
	notify: function (errlevel, text, opts) {
		$.niftyNoty({
			type: errlevel,
			container: '#gwcms-dynamic-alerts-container',
			html: text,
			focus: false,
			container: 'floating',
			timer: (opts && opts.hasOwnProperty('timer') ? opts.timer: 3000)
		});
	},
	init_iframe_open: function(){
		$(".iframeopen:not([data-initdone='1'])").click(function(event){			
			var title = $(this).attr('title');
			if(!title)
				title = $(this).text();
			
			//hold ctrl key & moouse click  to exit iframe
			title = "<span  data-url='"+$(this).attr('href')+"' onclick='if(window.event.ctrlKey){ window.open($(this).data(\"url\")) }else{ return false }'>"+title+'</span>';
			
			var dialogwidth = $(this).data('dialog-width') ? $(this).data('dialog-width') : 800;
			var opts = { url: $(this).attr('href'), iframe:1, title:title, widthOffset:0, minWidth:dialogwidth };
			
			if($(this).data('dialog-minheight'))
				opts.minHeight=$(this).data('dialog-minheight');
			
			gwcms.open_dialog2(opts)
			event.stopPropagation();
			return false;
		}).attr('data-initdone',1);
	},
		
	init_list: function()
	{
		$('.setListParams').on('submit', function(){
			var args = {act:'doSetListParams' }
			args[$(this).attr('name')] = $(this).val()
			gw_navigator.jump(false, args)
		}).keypress(function(e){
			var keyCode = e.keyCode || e.which;
			if (keyCode === 13) { 
				
				$(this).trigger( "submit" );
				e.preventDefault();
				return false;
			}			
		});
		
		$('.gwActiveTable td').mousedown(function (event) {
			if (event.ctrlKey && $(event.target).parents('a').length == 0) {
				
				//alert($(this).data('key') + event.button);
				var field = this.className.replace(/dl_cell_/,'');
				
				
				gwcms.addFilters(field, $(this).text().trim());
				
				
				//var searchmod = $(this).hasClass('transover') ? 'translations_over': 'translations';

				//window.open("/admin/"+gw_ln+"/datasources/"+searchmod+"/?transsearch=" + encodeURIComponent($(this).data('key')))

				event.preventDefault();
			}


		})
	},
	
	gwws: false,
	
	initWS: function(config){
				
		gw_adm_sys.gwws = new GW_WS()
		
		var gwws = gw_adm_sys.gwws;
			
		gwws.registerEvent('connect', 'main', function () {
			gwws.authorise({ username: config.user, pass: config.apikey })
		});
		
		gwws.registerEvent('authorise', 'main', function (data) {			
			console.log('WSC Authorised!')				
		});
		
		gwws.registerMessageCallback('messageprivate','main', function(data){ 
			console.log({"Private_message_received":data}); 
			
			//perduodama json uzkoduota zinute su parametrais title,text
			var decoded = JSON.parse(data.data);
			
			if(!decoded.hasOwnProperty('action'))
				decoded.action = 'notification';
			
			gw_adm_sys.runPackets([decoded]);
			
		});
		
		gwws.connect("wss://"+config.host+":"+config.port+"/irc");
	},
	
	bgTaskComplete: function(id)
	{
		$('#backgroundtask_'+id).fadeOut("slow", function() {
			
			if($('.backgroundTask:visible').length==0)
			{
				clearInterval(gw_adm_sys.bgTaskRunCountersInterval);
				$('#backgroundTasks').fadeOut()
			}
			
			
		 })
	},
	
	bgTaskRunCountersInterval:false,
	
	bgTaskRunCounters: function(servertime)
	{
		serverBrowserTimeDiff = servertime - Math.floor(Date.now() / 1000)
		
		gw_adm_sys.bgTaskRunCountersInterval = setInterval(function(){
			
			var nowsecs = Math.floor(Date.now() / 1000)-serverBrowserTimeDiff;
			$('.backgroundTask:visible').each(function(){
				var starttime = $(this).find('.startTime').text()
				var duration = nowsecs - starttime;
				$(this).find('.timeGoing').text(GW.uptime(duration))
				var expecteddur=$(this).find('.expectedDuration').text();
				
				if(expecteddur){
					var progress = Math.round(duration/expecteddur*100);
					progress = progress <= 100 ? progress : 100;
					$(this).find('.progress-bar').css({'width':progress+'%'}).find('.sr-only').text(progress+'%');
					
				}
					
				
			})			
			
		}, 1000);
	},
	
	
	updintervals: {},


	runPackets: function(data){
		for(var packetidx in data)
		{
			var packet = data[packetidx]

			if(gw_adm_sys.packetactions.hasOwnProperty(packet.action)){
				gw_adm_sys.packetactions[packet.action](packet);
			}else{
				console.log(["Action "+packet.action+" not supported", packet])
			}
		}		
	},
	
	runUpdaters: function(id, url, args, intervalSecs, instant){
		var f=function(){
			$.ajax({ url: url, type: "GET", dataType: "json", success: function (data) { gw_adm_sys.runPackets(data) }});
		};
		gw_adm_sys.updintervals[id]=setInterval(f, intervalSecs)
		
		if(instant==1)
			f();
	},

	packetactions: {
		updateProgress: function(data){			
			$('#progess_'+data.id).find('.valuedrop').text(data.progress+'%');
			$('#progess_'+data.id).find('.progress-bar').css({'width':data.progress+'%'})
		},
		clearInterval: function(data){
			console.log(['Clear interval debug', data]);
			clearInterval(gw_adm_sys.updintervals[data.id]);
		},
		notification: function(data)
		{
			gw_adm_sys.notification(data);
		},
		
		bgtask_close: function(data){
			gw_adm_sys.bgTaskComplete(data.bgtaskid)
		},
		
		update_row: function(data){
			gw_adm_sys.updateRow(data.id)
		},	
		delete_row: function(data){
			gw_adm_sys.deleteRow(data.id)
		},
		update_container: function(data){
			gw_adm_sys.updateContent(data.id, data.value, data)
		},
		update_containers: function(data){
			
			for(var key in data.data)
				gw_adm_sys.updateContent(key, data.data[key])
		}		
	},
	
	updateRow: function(id){
		$.get(location.href, { act:'',background:'',ajax_row:id }, function(data){
			var row=$('#list_row_' + id);
			var prevrow=row.prev();
			row.remove();
			prevrow.after(data);
			
			animateChangedRow(id, 2000);
			
			gw_adm_sys.initObjects();
		})
		
	},
	updateContent: function(id, value, opts)
	{
		$('#'+id).html(value);
	},
	
	deleteRow: function(id){
		$('#list_row_' + id).remove();
	},
	
	initObjects: function()
	{
		$(".ajax-link:not([data-initdone='1'])").click(function(event){			
			$(this).data('ownsrc', $(this).html());
			$(this).html('<i class="fa fa-spinner fa-pulse"></i>');
			var obj = $(this);
			var url = gw_navigator.url(this.href, { packets:1 })
			$.ajax({ url: url , type: "GET", dataType: "json", success: function (data) { 
					gw_adm_sys.runPackets(data);
					obj.html(obj.data('ownsrc'));
				}});
			
			event.stopPropagation();
			return false;
			
		}).attr('data-initdone',1);
		
		$(".iframe-under-tr:not([data-initdone='1'])").click(function(){
			var obj = $(this);
						
			var afterc = obj.data('iframe-after-close') ? obj.data('iframe-after-close') : false;
			

			var opt = obj.data('iframeopt') ? obj.data('iframeopt') : {};
						
			openIframeUnderThisTr(this, this.href, afterc, opt)
			event.preventDefault();
		}).attr('data-initdone',1);
		
		
		$(".add-popover:not([data-initdone='1'])").popover().attr('data-initdone',1);
		
		gw_adm_sys.init_iframe_open();
		gw_checklist.init();
	},
	resetInitState: function(parent){
		console.log('reset '+parent.find("[data-initdone='1']").length);
		parent.find("[data-initdone='1']").attr('data-initdone',0);
	},
	
	notification: function(data){
		var typetrans = {0:'success', 1: "warning", 2: "danger", 3: "info", 4: "dark"}
		//primary info success warning danger mint purple pink dark

		var nndata = {
			type: data.hasOwnProperty('type') ? (typetrans.hasOwnProperty(data.type) ? typetrans[data.type] : 'info')  : 'info',
			message: data.text,
			container: 'floating',
			timer: data.hasOwnProperty('time') ? data.time : 10000,
		};
		
		
		if(data.hasOwnProperty('footer'))
			nndata.message+="<span class='alert-footer'>"+data.footer+'</span>';
		

		if(data.hasOwnProperty('title') && data.title!=false)
			nndata.title = data.title;

		$.niftyNoty(nndata);		
	},
	
	addscript: function(url){
		var head=document.getElementsByTagName('head')[0];
		var newscript=document.createElement('script');
		newscript.async=1;
		newscript.src=url;
		head.appendChild(newscript);	
	}
	
	
}

var gw_session =
		{
				upd_time: 0,
				display_timer60: 0,
				display_timer1: 0,
				keep_timer: 0,
				exp: 1,
				dialog_open_state:false,
				
				process_notifications: function (response)
				{
						gw_adm_sys.updateNotifications(response.new_messages ? response.new_messages : 0);
				},
				ping: function (response) {
						$.ajax({url: 'tools/ping', success: gw_session.process_notifications, dataType: 'json'});
				},
				keep: function (response)
				{
						if (!response) {
							return $.ajax({url: 'tools/ping', success: gw_session.keep, dataType: 'json'});
						} else {
							gw_session.process_notifications(response);
						}

						gw_session.time_left(response.sess_expires);

						if (gw_session.exp < 0) {
								//clearInterval(gw_session.keep_timer);
								gw_session.login_dialog_open();
						}
				},
				time_left: function (set)
				{
						if (typeof (set) != 'undefined')
						{
								gw_session.exp = set - 1 + 1;
								gw_session.upd_time = new Date().getTime();
						}

						return Math.floor((gw_session.upd_time + gw_session.exp * 1000 - new Date().getTime()) / 1000);
				},
				timer_update: function ()
				{
						var left_secs = gw_session.time_left();

						if (left_secs < 180 && gw_session.display_timer60) // < 3 min
						{
								clearInterval(gw_session.display_timer60);
								gw_session.display_timer60 = 0;
								gw_session.display_timer1 = setInterval(gw_session.timer_update, 1000);
						}

						if (left_secs < 0) {
							
								clearInterval(gw_session.display_timer1);
								setTimeout(gw_session.keep, 3000);
								
								left_secs = 0;
						}


						

						$('#session_exp_t').html(GW.uptime(left_secs));
				},
				login_dialog_open: function ()
				{
						//if (!gw_login_dialog.is_open())
						//		gw_login_dialog.open();
							
						if(gw_session.dialog_open_state)
							return false;
						
							
						gw_session.dialog_open_state=1;
						
						gwcms.open_dialog2({ url: GW.app_base+GW.ln+'/users/login/dialog', iframe:1, title:'Login dialog'})							
							
				},
				login_dialog_close: function()
				{
					gwcms.close_dialog2();
					gw_session.dialog_open_state = false;
					
					gw_session.extend_success();
				},
				
				extend_success: function ()
				{
						gw_session.keep(0);
						setTimeout('gw_session.init(1)', 2000);
				},
				init: function (extend)
				{
					//user is not logged
					if(GW.session_exp==0)
						return false;
					
					
					if (!extend)
					{
							gw_session.upd_time = GW.init_time;
							gw_session.exp = GW.session_exp;
					}



					if (GW.session_exp != -1)
					{

							gw_session.keep_timer = setInterval('gw_session.keep(0)', 1 * 60 * 1000);//1min
							gw_session.display_timer60 = setInterval(gw_session.timer_update, 5 * 1000);//1min
							gw_session.timer_update();
					} else {

							setInterval('gw_session.ping()', 60 * 5 * 1000);//1min
					}
				}

		}


/*USED*/
var gw_server_time = {
	diff: 0,
	server_time: 0,
	get_server_time: function ()
	{
		var tmp = new Date();
		tmp.setTime(tmp.getTime() - gw_server_time.diff);
		return tmp;
	},
	update_server_clock: function ()
	{
		var t = gw_server_time.get_server_time();
		$('#server_time').html(GW.zero(t.getHours(), 2) + ':' + GW.zero(t.getMinutes(), 2) + ':' + GW.zero(t.getSeconds(), 2));
	},
	init: function ()
	{
		gw_server_time.server_time = new Date(GW.server_time).getTime();
		gw_server_time.diff = GW.init_time - gw_server_time.server_time;
		setInterval(gw_server_time.update_server_clock, 1000);
	}
}

var gwcms = {
	open_dialog: function (conf)
	{
		$("#dialog:ui-dialog").dialog("destroy");
		gwcms.dialog_cnt++;

		if(conf.title)
		{
			$('#' + id).attr('title', conf.title)
		}
		
		if(conf.elem){
			id = conf.elem; 
		}else{
			var id = 'ajaxdialog' + gwcms.dialog_cnt;
			$('body').append('<div id="' + id + '"></div>');
		}

		if (conf.iframe) {
			$('#' + id).get(0).innerHTML = '<iframe frameborder=0 style="width:100%;height:95%" src="' + conf.url + '">';
		} else if(conf.url) {
			$('#' + id).load(conf.url)
		}else if(conf.html){
			$('#' + id).html(conf.html)
		}

		var dconf = {  }
			
		dconf.width =  conf.hasOwnProperty('width') ? conf.width : $(document).width() / 10 * 6
		dconf.height =  conf.hasOwnProperty('height') ? conf.height : $(window).height() / 10 * 6
			
		if(conf.buttons){
			dconf.buttons = conf.buttons;
		}else{
			dconf.buttons[translations.CLOSE] = function () {
				$(this).dialog("close");
				$('#' + id).dialog('destroy');
			}			
		}		

		$.extend(dconf, conf);

		$('#' + id).dialog(dconf);
	},
	
	close_callback: false,
	
	open_dialog2: function (conf)
	{
		gwcms.dialog_cnt++;
		
		var defaults = {
			 minHeight : 200,
			 minWidth : 200,
			 heightOffset : 0,
			 widthOffset:0,
			 delay: 1000
		 };
		 
		 var conf = $.extend({}, defaults, conf || {});		
		 
		if(conf.close_callback)
			gwcms.close_callback = conf.close_callback;

		 console.log(conf);

		require(["iframeautoheight"], function (test) {

			var modal_body = '<iframe id="gwDialogConfiFrm" src="' + conf.url + '" frameborder="0"></iframe>';

			$('body').append('<div class="modal fade" id="gwcmsDialog" role="dialog" tabindex="-1" aria-hidden="true"><div class="modal-dialog" style="width:auto"><div class="modal-content">\
					<div class="modal-header"><button type="button" class="close" data-dismiss="modal" onclick="gwcms.close_dialog2()"><i class="pci-cross pci-circle"></i></button><h4 class="modal-title">' + conf.title + '</h4></div><div class="modal-body" style="padding:0">' + modal_body + '\
					</div></div></div></div>'
					);

			$("#gwDialogConfiFrm").iframeAutoHeight(conf);
			$("#gwDialogConfiFrm").load(function () {
				$("#gwcmsDialog").modal({backdrop: 'static'}).on('hidden.bs.modal', function () {
					$('#gwcmsDialog').remove();
					$('.modal-backdrop').remove()
				})

			});
		});
	},
	close_dialog2: function(context)
	{
		$('#gwcmsDialog').remove();
		$('.modal-backdrop').fadeOut();
		$('body').removeClass('modal-open');
		
		if(gwcms.close_callback)
		{
			gwcms.close_callback(context)
			gwcms.close_callback = false;
		}
	},
	
	close_dialog_all_types: function()
	{
		gwcms.close_dialog2();
	},
	
	initAutoresizeIframe: function(selector, cfg, callback)
	{
		require(["iframeautoheight"], function () {
			var defaults = {
				minHeight: 200, 
				//maxHeight: $("body").innerHeight() - 250, 
				minWidth: 200, 
				maxWidth: $("body").innerWidth() - 250, 
				heightOffset: 10, 
				widthOffset: 60/*, debug:true */};
			
			cfg = $.extend(defaults, cfg)
			
			
				
			
			$(selector).iframeAutoHeight(cfg);
			
			if(callback)
				callback();
			
		});
	},
	
	dialogClose: function () {
		location.href = location.href;
		//$('#gwcmsDialog').modal('hide');

	},
	open_iframe: function (conf)
	{
		conf.iframe = 1
		gwcms.open_dialog(conf);
	},
	open_ajax: function (conf)
	{
		conf.ajax = 1
		gwcms.open_dialog(conf);
	},
	dialog_cnt: 0,
	open_rtlogview: function (fileid)
	{
		conf =
				{
					title: fileid + ' LogWatch',
					url: GW.app_base + GW.ln + '/system/logwatch/iframe?id=' + fileid
				}
		gwcms.open_iframe(conf)
	},
	equaliseWidths: function (selector)
	{
		$(selector).width('auto')
		// Intialize width/height
		var widest = 0;
		// Loop through equalize elements
		$(selector).each(function () {
			// Set width/height to widest/tallest elements
			widest = $(this).width() > widest ? $(this).width() : widest;
		}).width(widest);
	},
	equaliseHeights: function (selector)
	{
		// Intialize width/height
		var tallest = 0;
		// Loop through equalize elements
		$(selector).each(function () {
			// Set width/height to widest/tallest elements

			tallest = $(this).height() > tallest ? $(this).height() : tallest;
		}).height(tallest);
	},
	filtersChanged: function ()
	{

		var filterscount = $("[name^='filters[']").length;

		if (filterscount) {
			$('#gwFilterTgglBtn').addClass('active');
			$('#gwDropFiltersLoading').fadeOut();
			$('#gwFiltersActions').fadeIn();

			$('#gwFiltersContainer').addClass('gwFiltersActive');

		} else {
			$('#gwFilterTgglBtn').removeClass('active');
			$('#gwFiltersActions').fadeOut();

			$('#gwFiltersContainer').removeClass('gwFiltersActive');


			//nuresetint filtrus jei buvo uzdetu
			var filtersPresent = $('#gwFiltersForm').attr('data-filters-present')

			if (filtersPresent)
				gwcms.filtersUnset();

		}

		$('.gwAddFilterMI').each(function () {
			var fieldname = $(this).attr('data-field');

			if ($('.filterRow' + fieldname).length == 0) {
				this.style.fontWeight = 'normal'
			} else {
				this.style.fontWeight = 'bold'
			}
		})


		gwcms.equaliseWidths(".gwFiltLabel");
	},
	filtersInit: function()
	{
		gwcms.filtersChanged();	
	},
	
	filtersSubmit: function(){
		
		
		//stringify multiselect values
		
		$("#gwFiltersForm select[multiple]").each(function(){
			input = $(this)
			name = input.attr('name')
			input.removeAttr('name')
			val = JSON.stringify(input.val())

			$('<input>')
					.attr('type','hidden')
					.attr('name', name)
					.val(val)
					.appendTo('form');
		})


		return false;
			
	},
	
	loadedfilters: [],
	addFilters: function (name, value) {
		$('#gwDropFiltersLoading').show();
		data = {act: 'doGetFilters', fieldname: name}
		
		if(value)
			data.value = value;
		
		$.get(location.href, data, function (rdata) {
			$('#gwDropFilters').append(rdata);
			gwcms.filtersChanged();
		});
	},
	addAllFilters: function () {

		gwcms.addFilters('');
	},
	removeFilter: function (obj, field)
	{
		$(obj).parents('.filterRow').fadeOut(300, function () {
			$(this).remove();
			gwcms.filtersChanged();
		});


	},
	filtersUnset: function () {
		$('#gwFiltersUnset').val(1);
		$('#gwFiltersForm').submit();
	},
	filtersBtnClick: function (btn, filtersPresent) {

		if ($(btn).hasClass('active'))
		{

			var filtersPresent = $('#gwFiltersForm').attr('data-filters-present')

			if (filtersPresent)
				gwcms.filtersUnset();



			$('.filterRow').remove();
			gwcms.filtersChanged();
			$('#gwFilterTgglBtn').addClass('active');//workaround cause on prev line class is removed & later toogleClass works

		} else {
			//add all filters
			gwcms.addAllFilters();
		}
	},
	
	initImagePreview: function()
	{
		$('.gwPreview').popover({
			'trigger':'hover',
			 placement : 'bottom',
			'html':true,
			'content':function(){
				return "<img src='"+$(this).data('imageUrl')+"'>";
			}
		});	
	},
	
	initDropdowns: function ()
	{
		$(document).ready(function () {

			/* Clicks within the dropdown won't make
			 it past the dropdown itself */


			$('.gwcms-ajax-dd').click(function () {
				
				var drop = $(this).parents('.btn-group').find('.dropdown-menu');
				var trigg = $(this)
		

				if (trigg.data('isloaded') == 'loaded'){
					//console.log('data aready loaded');
					return false;
				}

				$.ajax({
					url: trigg.data('url'),
					success: function (data) {
						//console.log('data loaded 1st time');
						trigg.data('isloaded', 'loaded');
						drop.html(data);
						gw_adm_sys.initObjects();
						trigg.dropdown();//fix
					}
				});
				
			})

		});
		
		
	},
	
	showMessages: function(msg_arr, title)
	{
		var types = {'0':'success', '1':'warning', '2':'danger', '3':'info'}
		
		for(i in msg_arr)
		{
			var level = msg_arr[i]['type']
			var msg = msg_arr[i]['text']
			var title = msg_arr[i]['title']
			
			var data = {
							type: types[level],
							message: msg,
							container: 'floating',
							timer: 5000
						};
			if(title)
				data.title = title;
			
			$.niftyNoty(data);			
		}
	},
	
	beforeFormSubmit: function(obj)
	{
		
		$(obj).find(':disabled').removeAttr('disabled');
	}
	
}


var gw_checklist = {
	init: function ()
	{
		$("#checklist_toggle:not([data-initdone='1'])").click(function () {
			gw_checklist.toggle_items(this.checked)
			gw_checklist.show_actions()
		}).attr('data-initdone',1);
		
		$(".checklist_item:not([data-initdone='1'])")
			.click(gw_checklist.show_actions).change(gw_checklist.show_actions)
			.attr('data-initdone',1);			
	},
	show_actions: function ()
	{		
		if($('.checklist_item:checked').length > 0)			
			$('#checklist_actions').fadeIn();
		else
			$('#checklist_actions').fadeOut();
	},
	toggle_items: function (check)
	{
		if (check) {
			$('.checklist_item').prop('checked', 'checked')
			gw_checklist.show_actions()
		} else {
			$('.checklist_item').removeAttr("checked")
		}
	},
	submit: function (action)
	{
		$('#checklist_form').append('<input type="hidden" name="act" value="do:checklist_submit" />');
		$('#checklist_form').append('<input type="hidden" name="action" value="' + action + '" />');
		$('#checklist_form').submit();
	}
}




//atsargiai sitas per ajax veikia, jei includins galva mes errorus
function checked_action(actionOrUrl, redirect) {

	var selected = [];
	$.each($('.checklist_item:checked'), function () {
		selected.push($(this).val());
	});

	var url = actionOrUrl.indexOf('/')==-1 ? GW.app_base + GW.ln + '/' + GW.path + '/' + actionOrUrl : actionOrUrl
	
	url = gw_navigator.url(url, {'ids': selected.join(',')})
	
	if(redirect) {
		location.href = url
	} else {
		gw_dialog.open(url)
	}
}

function checked_action_postids(actionOrUrl, redirect) 
{

	var selected = [];
	$.each($('.checklist_item:checked'), function () {
		selected.push($(this).val());
	});

	var url = actionOrUrl.indexOf('/')==-1 ? GW.app_base + GW.ln + '/' + GW.path + '/' + actionOrUrl : actionOrUrl

	$.ajax({
	  type: "POST",
	  url: url,
	  data: { ids : selected.join(',') },
	  success: function(){

			if(redirect) {
				location.href = url
			} else {
				gw_dialog.open(url)
			}

	  }
	});

}

function checked_action2(action, title) {

	var selected = [];
	$.each($('.checklist_item:checked'), function () {
		selected.push($(this).val());
	});

	gwcms.open_dialog2({url: GW.app_base + GW.ln + '/' + GW.path + '/' + action + '?ids=' + selected.join(','), title: title});
}

function animateChangedRow(id,speed)
{
		var curr_bgcolor = $('#list_row_'+id).css("background-color");
		var curr_color = $('#list_row_'+id).css("color");
		
		$('#list_row_'+id).animate({backgroundColor: "#003311",color: "#fff"}, 300 );
				
		setTimeout(function(){
				$('#list_row_'+id).animate({backgroundColor: curr_bgcolor, color: curr_color}, speed ? speed/2 : 300 );
		}, (speed ? speed/2 : 300))
	
}


var gw_dialog = {
	options: {},
	is_open: function ()
	{
		return $('#gw_dialog').size();
	},
	open: function (url, options)
	{
		gw_dialog.options = options ? options : {}
		$.ajax({url: url, success: gw_dialog.__open});
	},
	close: function ()
	{
		$("#gw_dialog").remove();
	},
	__open: function (content, options)
	{
		$("#gw_dialog").remove();

		if (!$('#gw_dialog').size())
			$('body').append('<div id="gw_dialog" style="display:none"></div>');

		$('#gw_dialog').html(content);


		var buttons = {}

		var button_obj = $("#gw_dialog_buttons button");

		for (var i = 0; i < button_obj.size(); i++)
			buttons[$(button_obj[i]).text()] = button_obj[i].onclick

		var title = $('#gw_dialog #title').text();

		var options = {
			modal: true,
			autoOpen: false,
			buttons: buttons,
			width: 300,
			title: title
					//beforeclose: gw_login_dialog.cancel
		}

		$.extend(options, gw_dialog.options);

		$("#gw_dialog").dialog(options);

		$("#gw_dialog").dialog('open');
		
	},
}

var gw_sortable =
		{
				parse_id: function (str)
				{
						var splits = str.split('_');

						return {id: splits[splits.length - 1], index: splits[splits.length - 2] - 1 + 1}
				},
				apply: function (selector)
				{
						var serialize = $(selector).sortable('toArray');

						var info, params = {}, chang_ind = 0;

						for (i in serialize)
						{
								info = gw_sortable.parse_id(serialize[i]);
								if (info.index == i)
										continue;

								info.change = i - info.index;
								params['positions[' + info.id + ']'] = info.change;
						}

						params['act'] = 'do:set_positions';

						gw_navigator.jump(false, params);
				}
		}
		


jQuery.cookie = function (name, value, options) {
		if (typeof value != 'undefined') { // name and value given, set cookie
				options = options || {};
				if (value === null) {
						value = '';
						options.expires = -1;
				}
				var expires = '';
				if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
						var date;
						if (typeof options.expires == 'number') {
								date = new Date();
								date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
						} else {
								date = options.expires;
						}
						expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
				}
				// CAUTION: Needed to parenthesize options.path and options.domain
				// in the following expressions, otherwise they evaluate to undefined
				// in the packed version for some reason...
				var path = options.path ? '; path=' + (options.path) : '';
				var domain = options.domain ? '; domain=' + (options.domain) : '';
				var secure = options.secure ? '; secure' : '';
				document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
		} else { // only name given, get cookie
				var cookieValue = null;
				if (document.cookie && document.cookie != '') {
						var cookies = document.cookie.split(';');
						for (var i = 0; i < cookies.length; i++) {
								var cookie = jQuery.trim(cookies[i]);
								// Does this cookie string begin with the name we want?
								if (cookie.substring(0, name.length + 1) == (name + '=')) {
										cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
										break;
								}
						}
				}
				return cookieValue;
		}
};




function loadRowAfter(trobject, data, classn)
{		
		var id = trobject.attr('data-id');
		var name = 'list_row_' + id;
		
		classn = classn ? classn : 'inlineFormRow';
		
		
		trobject.after('<tr id="' + name + '_after" class="' + classn + '" data-id="' + id + '">' + data + '</tr>');
}

function triggerLoading(obj, state)
{
		if(state==1)
		{
				$(obj).attr('data-loading-restore-html', $(obj).html());
				$(obj).html('<i class="fa fa-spinner fa-pulse"></i>');
		}else{
				$(obj).html($(obj).attr('data-loading-restore-html'));
		}
}

function triggerExpanded(obj, state)
{
		if(state==1)
		{
				$(obj).addClass('expanded')
				$(obj).attr('data-expanded-restore-html', $(obj).html());
				$(obj).html('<i class="fa fa-caret-square-o-down mouseout"></i><i class="fa fa-caret-square-o-up mouseover" style="display:none"></i>'+$(obj).html());
				$(obj).hover(
						function(){$(this).find('.mouseover').show();$(this).find('.mouseout').hide();},
						function(){$(this).find('.mouseover').hide();$(this).find('.mouseout').show();}
				)
		}else{
				$(obj).removeClass('expanded fa fa-arrow-circle-down');
				$(obj).html($(obj).attr('data-expanded-restore-html'));
		}
}

function openIframeUnderThisTr(trig, url, afterclose, opts)
{
	if(event.ctrlKey)
	{
		window.open(url);
		return;
	}	
	
	var rowobj = $(trig).closest('tr');
	var id = $(rowobj).attr('data-id');
	var rowaftername = 'list_row_'+id+'_after';

	if(!opts)
		opts = {}

	var framewidth = opts.hasOwnProperty('width') ? opts.width : '100%';		
	var frameheight = opts.hasOwnProperty('height') ? opts.height : 'auto';		


	if($(trig).hasClass('expanded'))
	{
		triggerExpanded(trig, 0);
		$('#'+rowaftername).remove();

		if(afterclose)
				typeof afterclose === "function" ? afterclose() : eval(afterclose);

		return false;
	}

	triggerExpanded(trig, 1);
	triggerLoading(trig, 1);
	

	loadRowAfter(rowobj, "<td colspan='100'><iframe class='iframeunderrow iframe_auto_sz' src='"+url+"' style='width:"+framewidth+";height:"+frameheight+"'></td></iframe>", 'iframeunderrowcont');

	$('#'+rowaftername+' .iframeunderrow').load(function(){
			triggerLoading(trig, 0);
			$(trig).addClass('expanded');
	})

	$('.iframe_auto_sz').load(function(){
			//if($('.iframe_auto_sz').attr('data-ifrm_auto_sz_init'))
			//		return false;

				var ifrm = this
				var ifrmcont = $(this).contents();


				var iframe_content = $(this).contents().find('body');
				var src =  this.contentWindow.location.href

				if(src.indexOf('iframeclose=1')!=-1)
				{

						//this will close iframe
						openIframeUnderThisTr(trig, url, afterclose);
				}


				iframe_content.resize(function(){ 
					$(ifrm).height(ifrmcont.height()+20);
					
					//iframe_content.get(0).scrollHeight
				});

				iframe_content.resize();
				setTimeout(function(){
						iframe_content.resize()
				},1000);

			//$('.iframe_auto_sz').attr('data-ifrm_auto_sz_init'))
	})		

}


//jei ifreime leidziamas kodas vykdyt auksciausiam lange
function rootgwcms() {
	try {
		return window.self !== window.top ?  window.parent.gwcms : gwcms; 
	} catch (e) {
		return gwcms
	}
}


function fallbackCopyTextToClipboard(text) {
	var textArea = document.createElement("textarea");
	textArea.value = text;
	document.body.appendChild(textArea);
	textArea.focus();
	textArea.select();

	try {
		var successful = document.execCommand('copy');
		var msg = successful ? 'successful' : 'unsuccessful';
		console.log('Fallback: Copying text command was ' + msg);
		gw_adm_sys.notify('success',text+' copied.', { timer: 10000 });
	} catch (err) {
		console.error('Fallback: Oops, unable to copy', err);
		prompt("Select all & copy", text);
	}

	document.body.removeChild(textArea);
}
function copyTextToClipboard(text) {
	
	
	
	if (!navigator.clipboard) {
		fallbackCopyTextToClipboard(text);
		return;
	}
	navigator.clipboard.writeText(text).then(function () {
		console.log('Async: Copying to clipboard was successful!');
		gw_adm_sys.notify('success',text+' copied.', { timer: 10000 });
	}, function (err) {
		console.error('Async: Could not copy text: ', err);
		prompt("Select all & copy", text);
	});
}



$("body").keydown(function (event) {


		//ctrl + 1 = pereiti i kita environmenta
		if (event.which == 49 && event.ctrlKey) {
			location.href = GW.app_base + GW.ln + '/system/tools?act=doSwitchEnvironment&uri='+encodeURIComponent(location.href)
			event.preventDefault();
		}
		
		//ctrl + 2
		
		if (event.which == 50 && event.ctrlKey) {
			location.href = GW.app_base + GW.ln + '/system/tools?act=doPullProductionDB&uri='+encodeURIComponent(location.href)
			event.preventDefault();
		}		

		if (event.which == 51 && event.ctrlKey) {
			location.href = GW.app_base + GW.ln + '/system/tools?act=doDebugModeToggle&uri='+encodeURIComponent(location.href)
			event.preventDefault();
		}		
			
	
		//console.log(event.which)
});