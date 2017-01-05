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
		GW.init_time = new Date().getTime();
		$(document).ready(gw_adm_sys.init_after_load);
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
	notify: function (errlevel, text) {
		$.niftyNoty({
			type: errlevel,
			container: '#gwcms-dynamic-alerts-container',
			html: text,
			focus: false,
			timer: 3000
		});
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


						var m = Math.floor(left_secs / 60);
						var s = left_secs - m * 60;
						var str = (m ? m + ' m ' : '') + (m < 3 ? s + ' s' : '');

						$('#session_exp_t').html(str);
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

		var id = 'ajaxdialog' + gwcms.dialog_cnt;

		$('body').append('<div id="' + id + '"></div>');

		if (conf.iframe) {
			$('#' + id).get(0).innerHTML = '<iframe frameborder=0 style="width:100%;height:95%" src="' + conf.url + '">';
		} else {
			$('#' + id).load(conf.url)
		}

		var dconf = {
			buttons: {},
			width: $(document).width() / 10 * 6,
			height: $(window).height() / 10 * 6
		}

		dconf.buttons[translations.CLOSE] = function () {
			$(this).dialog("close");
			$('#' + id).dialog('destroy');
		}

		$.extend(dconf, conf);

		$('#' + id).dialog(dconf);
	},
	open_dialog2: function (conf)
	{
		gwcms.dialog_cnt++;
		
		var defaults = {
			 minHeight : 200,
			 minWidth : 200,
			 heightOffset : 0,
			 widthOffset:60
		 };
		 
		 var conf = $.extend({}, defaults, conf || {});		
		 
		 console.log(conf);

		
		require(["gwcms","js/jq/browser", "js/jq/jquery.iframe-auto-height.plugin"], function (test) {

			var modal_body = '<iframe id="gwDialogConfiFrm" src="' + conf.url + '" frameborder="0"></iframe>';

			$('body').append('<div class="modal fade" id="gwcmsDialog" role="dialog" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">\
					<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><i class="pci-cross pci-circle"></i></button><h4 class="modal-title">' + conf.title + '</h4></div><div class="modal-body" style="padding:0">' + modal_body + '\
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
	close_dialog2: function()
	{
		$('#gwcmsDialog').remove();
		$('.modal-backdrop').fadeOut();
		$('body').removeClass('modal-open');
	},
	
	close_dialog_all_types: function()
	{
		gwcms.close_dialog2();
	},
	
	initAutoresizeIframe: function(selector, cfg, callback)
	{
		require(["js/jq/browser", "js/jq/jquery.iframe-auto-height.plugin"], function () {
			if(!cfg)
				cfg = {minHeight: 200, maxHeight: $("body").innerHeight() - 250, minWidth: 200, maxWidth: $("body").innerWidth() - 250, heightOffset: 10, widthOffset: 60/*, debug:true */};
			
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
	addFilters: function (name) {
		$('#gwDropFiltersLoading').show();
		data = {act: 'doGetFilters', fieldname: name}
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
		$("#checklist_toggle").click(function () {
			gw_checklist.toggle_items(this.checked)
		})
		
		$('.checklist_item, #checklist_toggle').click(gw_checklist.show_actions).change(gw_checklist.show_actions)
					
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
function checked_action(actionOrUrl) {

	var selected = [];
	$.each($('.checklist_item:checked'), function () {
		selected.push($(this).val());
	});

	var url = actionOrUrl.indexOf('/')==-1 ? GW.app_base + GW.ln + '/' + GW.path + '/' + actionOrUrl : actionOrUrl
	
	gw_dialog.open(gw_navigator.url(url, {'ids': selected.join(',')}))
}

function checked_action2(action, title) {

	var selected = [];
	$.each($('.checklist_item:checked'), function () {
		selected.push($(this).val());
	});

	gwcms.open_dialog2({url: GW.app_base + GW.ln + '/' + GW.path + '/' + action + '?ids=' + selected.join(','), title: title});
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
		require(['gwcms'], function(){
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
		});
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




	