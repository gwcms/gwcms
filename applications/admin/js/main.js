/**
 * GateWay CMS 2011
 */

///http://webhole.net/2010/07/10/jquery-cookies-example/
jQuery.cookie = function(name, value, options) {
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

var GW = {
	zero: function(num,count)
	{
		var add=count-String(num).length;
		while(add>0){num='0'+num;add--}
		return num;
	},

	path_parent: function()
	{
		return GW.ln+'/'+gw_navigator.dirname(GW.path)
	},
	
	dump: function(obj)
	{
		try{console.log(obj)}catch(err){}
	}
}

var gw_navigator =
{
	implode_url: function(url)
	{
		return url.base+(url.query ? '?'+$.param(url.query) : '')+(url.anchor ? '#'+url.anchor : '')
	},
	
	explode_url: function(url) {
		
		if(!url)
			url=location.href;
		
		var split = url.split(/[\?\#]/);
		 
		var tmp1 = {
			base: split[0],
			query:  split[1] ? gw_navigator.explode_args(split[1]) : {},
			anchor: split[2] ? split[2] : ''
		}
		
		return tmp1;
	},
	
	unescape: function(str) {
		return unescape(str.replace('+',' '));
	},
	
	explode_args: function(args) {
		if(!args) return {};
			
		args = args.split('&');
		var arr={}, tmp, i
		
		for(i in args) {
			tmp = args[i].split('=');
			arr[gw_navigator.unescape(tmp[0])] = tmp[1] ? gw_navigator.unescape(tmp[1]) : false;
		}
		return arr;
	},
	
	url: function(url, params)
	{
		if(!url)
			url=window.location.href;
		
		if(typeof params == 'object')
		{
			url = gw_navigator.explode_url(url);
			
			$.extend(url.query, params);
			url = gw_navigator.implode_url(url);
		}
		
		return url
	},
	
	jump: function(url, params)
	{
		window.location = gw_navigator.url(url, params);
		return false;
	},
	
	//http://planetozh.com/blog/2008/04/javascript-basename-and-dirname/
	dirname: function(path) {
	    return path.replace(/\\/g,'/').replace(/\/[^\/]*$/, '');
	}	
}


var gw_adm_sys =
{		
	paging_select_box: function(object,from_val,to_val)
	{
		var opt={}
		while(from_val<=to_val)
		{
			opt[from_val]=from_val;
			from_val++
		}
		$(object).addOption(opt, false);
	},
	
	
	change_page: function(page)
	{
		gw_navigator.jump(false, {'act':'doSetListParams','list_params[page]': page})
		return false;
	},
	
	order_add_level: function(count)
	{
		if(!count) count=1;
		var tmp1=$('#order-container').data('order_sel_html'), tmp2='';
		
		for(var i=0;i<count;i++)
			tmp2+=tmp1
			
        $('#order-container').append(tmp2);
	},
	
	order_change: function()
	{
		var items=$('.order-sel');
		var tmp=Array(),tmpk=0;
		
		for(var i=0;i<items.length;i++)
			if(items[i].value)
				tmp[tmpk++]=items[i].value;
			
		gw_navigator.jump(false, {'list_params[order]': tmp.join(',')})
		return false;		
	},
	
	order_init: function(order)
	{
		$('#order-container').data('order_sel_html',$('#order-container').html());
		
		if(order.length > 1)
			gw_adm_sys.order_add_level(order.length-1);
		
		var items=$('.order-sel');
		
		for(var i=0;i<order.length;i++)
			items[i].value=order[i];
	},
	
	init: function()
	{
		GW.init_time=new Date().getTime();
		$(document).ready(gw_adm_sys.init_after_load);
	},
	
	init_after_load: function()
	{
		//do actions on ready
		
		//menu----------------------------------------------
		//slides the element with class "menu_body" when paragraph with class "menu_head" is clicked 
		$("#firstpane p.menu_head").click(function()
		{	
			if($(this).next("div.menu_body").html())
				$(this).addClass('menu_head_active').next("div.menu_body").slideToggle(300).siblings("div.menu_body").slideUp("slow");
				
			document.location = $(this).find('a').attr('href');	
				
			$(this).siblings().removeClass('menu_head_active');
		});
		
		//timers--------------------------------------------
		
		gw_session.init();
		gw_server_time.init();
		
				
		
		//toolbar init
		$('.gw_toolbar .gw_button')
		.hover
		(
			function(){$(this).css('opacity', '1')},
			function(){$(this).css('opacity', '0.8')}
		)
		.addClass('ui-corner-all').css('opacity', '0.8')
		.mousedown(function(){$(this).addClass('gw_button-highlight')})
		.mouseup(function(){$(this).removeClass('gw_button-highlight')});
		
		
		//gw_login_dialog.open();
	},
	

}

var gw_checklist = 
{
		init: function()
		{
			$("#checklist_toggle").click(function(){gw_checklist.toggle_items(this.checked)})
			$('.checklist_item').click(gw_checklist.show_actions)
			
		},
		show_actions: function()
		{
			$('#checklist_actions').fadeIn();
		},
		toggle_items: function(check)
		{
			if(check){
				$('.checklist_item').attr('checked', 'checked')
				gw_checklist.show_actions()
			}else{
				$('.checklist_item').removeAttr("checked")
			}
		},
		submit :function(action)
		{
			$('#checklist_form').append('<input type="hidden" name="act" value="do:checklist_submit" />');
			$('#checklist_form').append('<input type="hidden" name="action" value="'+action+'" />');
			$('#checklist_form').submit();
		}
}

var gw_login_dialog = 
{
	page_path: '/users/login/dialog',
		
	is_open: function()
	{
		return $('#login_dialog').size();
	},
	
	open: function()
	{
		$.ajax({url:GW.ln+gw_login_dialog.page_path, success: gw_login_dialog.__open});		
	},
	
	submit: function()
	{
		$.ajax({
			url:GW.ln+gw_login_dialog.page_path+'?act=do:login', 
			type:"POST", 
			data:$('#login_dialog input').serialize(),
			success: gw_login_dialog.__open
		});	
		
		$('.loading-switch').toggle();
	},
	
	success: function()
	{
		setTimeout('$("#login_dialog").remove();',1000);
		gw_session.extend_success();
	},
	
	cancel: function()
	{
		location.href=location.href;	
	},
	
	__open: function(content)
	{
		$("#login_dialog").remove();

		if(!$('#temp_html').size())
			$('body').append('<div id="temp_html" style="display:none"></div>');
		
		$('#temp_html').html(content);

		buttons={}
		buttons[$("#login_dialog").attr('button_cancel')]=gw_login_dialog.cancel;
		buttons[$("#login_dialog").attr('button_ok')]=function(){gw_login_dialog.submit()}	
		
		$("#login_dialog").dialog({
			modal: true, 
			autoOpen: false, 
			buttons: buttons,
			width: 300,
			beforeclose: gw_login_dialog.cancel
		});
		
		$("#login_dialog").dialog('open');
	},
}

var gw_dialog = 
{
	options:{},
	
	is_open: function()
	{
		return $('#gw_dialog').size();
	},
	
	open: function(url, options)
	{
		gw_dialog.options = options ? options : {}
		$.ajax({url:url, success: gw_dialog.__open});		
	},
	

	close: function()
	{
		$("#gw_dialog").remove();
	},
	
	__open: function(content, options)
	{
		$("#gw_dialog").remove();

		if(!$('#gw_dialog').size())
			$('body').append('<div id="gw_dialog" style="display:none"></div>');
		
		$('#gw_dialog').html(content);
		
			
		var buttons={}
		
		var button_obj=$("#gw_dialog_buttons button");
		
		for(var i=0; i<button_obj.size(); i++)
			buttons[$(button_obj[i]).text()]=button_obj[i].onclick
		
		var title=$('#gw_dialog #title').text();
			
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


var gw_session = 
{
	upd_time:0,
	display_timer60: 0,
	display_timer1: 0,
	keep_timer: 0,
	exp: 1,	
	
	keep_infinity: function(){
		$.ajax({url: 'tools/session_keep'});
	},
	
	keep: function(left_secs)
	{
		if(!left_secs)
			return $.ajax({url: 'tools/session_keep', success: gw_session.keep});
		
		gw_session.time_left(left_secs);
		
		if(gw_session.exp < 0){
			clearInterval(gw_session.keep_timer);
			gw_session.dialog_open();
		}
	},
	
	time_left: function(set)
	{
		if(typeof(set)!='undefined')
		{
			gw_session.exp=set-1+1;
			gw_session.upd_time=new Date().getTime();
		}	
			
		return Math.floor((gw_session.upd_time+gw_session.exp*1000 - new Date().getTime() ) / 1000);
	},
	
	timer_update: function()
	{
		var left_secs = gw_session.time_left();
		
		if(left_secs < 180 && gw_session.display_timer60) // < 3 min
		{
			clearInterval(gw_session.display_timer60);
			gw_session.display_timer60 = 0;
			gw_session.display_timer1 = setInterval(gw_session.timer_update, 1000);
		}
		
		if(left_secs < 0){
			clearInterval(gw_session.display_timer1);
			setTimeout(gw_session.keep, 3000);
			left_secs=0;
		}
		

		var m=Math.floor(left_secs / 60);
		var s=left_secs-m*60;
		var str = (m ? m + ' m ':'')+(m < 3 ? s+' s':'') ;
			
		$('#session_exp_t').html(str);
	},
	
	dialog_open: function()
	{
		if(!gw_login_dialog.is_open())
			gw_login_dialog.open();
	},
	
	extend_success: function()
	{
		gw_session.keep(0);
		setTimeout('gw_session.init(1)', 2000);
	},
	
	init: function(extend)
	{
		if(!extend)
		{
			gw_session.upd_time=GW.init_time;
			gw_session.exp=GW.session_exp;
		}

		if(GW.session_exp!=-1)
		{
			gw_session.keep_timer = setInterval('gw_session.keep(0)', 5*60*1000);//5min
			gw_session.display_timer60 = setInterval(gw_session.timer_update, 60*1000);//1min
			gw_session.timer_update();
		}else{
			setInterval('gw_session.keep_infinity()', 5*60*1000);//5min
		}
	}
	
}

var gw_server_time = 
{
	diff: 0,
	server_time: 0,
	
	get_server_time: function()
	{
		var tmp = new Date();
		tmp.setTime(tmp.getTime()-gw_server_time.diff);
		return tmp;
	},

	update_server_clock: function()
	{
		var t = gw_server_time.get_server_time();
		$('#server_time').html(GW.zero(t.getHours(),2)+':'+GW.zero(t.getMinutes(),2)+':'+GW.zero(t.getSeconds(),2));
	},
	
	init: function()
	{
		gw_server_time.server_time=new Date(GW.server_time).getTime();
		gw_server_time.diff=GW.init_time-gw_server_time.server_time;
		setInterval(gw_server_time.update_server_clock, 1000);
	}
	
	
}


var gw_sortable = 
{
		parse_id: function(str)
		{
			var splits=str.split('_');
			
			return {id: splits[splits.length-1], index: splits[splits.length-2]-1+1}
		},
		
		
		apply: function(selector)
		{
			var serialize = $(selector).sortable('toArray');
			
			var info, params = {}, chang_ind=0;
			
			for(i in serialize)
			{
				info =  gw_sortable.parse_id(serialize[i]);
				if(info.index == i)
					continue;
				
				info.change = i-info.index;
				params['positions['+info.id+']']=info.change;
			}
			
			params['act']='do:set_positions';
			
			gw_navigator.jump(false, params);
		}
}


//var $clock_egg='<div id="clock_egg" style="height:10px;background-color:white;display:none"><embed src="http://flash-clocks.com/extdocs/custom-flash-clock-16.swf" width="100%" height="100%" wmode=transparent type=application/x-shockwave-flash></embed></div>';
//wmode=transparent - too high cpu load
var $clock_egg='<div id="clock_egg" style="height:300px;background-color:white;display:none;border-bottom:5px solid #4B4B4B"><embed src="http://flash-clocks.com/extdocs/custom-flash-clock-16.swf" width="100%" height="100%" type=application/x-shockwave-flash></embed></div>';


function clock_egg()
{
	if($clock_egg){
		$('#wrap').prepend($clock_egg);
		$clock_egg='';
	}
	
	$("#header").toggle();
	$("#clock_egg").toggle();
	
	$.cookie('clock_egg', $('#clock_egg').is(':visible') ? 1 : '', {path:GW.base});
}

$(document).ready(function(){
	$('#server_time').click(clock_egg)
	
	if($.cookie('clock_egg'))
		clock_egg();
	})
	
	
/* TOOLTIP 2011-12-15 plugin add to element class="tooltip" and tooltip text in title="demo" */
this.tooltip = function(){	
	/* CONFIG */		
		xOffset = 10;
		yOffset = 20;		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result		
	/* END CONFIG */		
	$(".tooltip").hover(function(e){											  
		this.t = this.title;
		this.title = "";									  
		$("body").append("<p id='tooltip'>"+ this.t +"</p>");
		var left = e.pageX + yOffset
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",left + "px")
			.css("max-width", $(window).width()-left )
			.fadeIn("fast");		
    },
	function(){
		this.title = this.t;		
		$("#tooltip").remove();
    });	
	$(".tooltip").mousemove(function(e){
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});			
};



// starting the script on page load
$(document).ready(function(){
	tooltip();
});

/* END of TOOLTIP*/

/* MODULE's_NOTES 2012-02-22 */
function open_notes(pageid){
	// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
	$( "#dialog:ui-dialog" ).dialog( "destroy" );

	$('#dialog-message').load(GW.ln+'/config/modules?act=do:get_notes&path='+GW.path);
	
	$( "#dialog-message" ).dialog({
		modal: true,
		buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
			},
			Edit: function() {
				location.href=GW.ln+'/config/modules/'+pageid+'/form?return_to='+GW.path;
			}				
		},
		width: $(document).width()/10*9,
		height: $(window).height()/10*9
	
	});
}

function open_dialog(conf)
{
	// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
	$( "#dialog:ui-dialog" ).dialog( "destroy" );
	dialog_cnt++;
	
	var id = 'ajaxdialog'+dialog_cnt;

	$('body').append('<div id="'+id+'"></div>');	
	
	if(conf.iframe){
		$('#'+id).html('<iframe frameborder=0 style="width:100%;height:95%" src="'+conf.url+'">');
	}else{
		$('#'+id).load(conf.url)
	}
		
	var dconf = {
		buttons: {
				close: function() {
				$( this ).dialog( "close" );
				$('#'+id).destroy();
			}			
		},
		width: $(document).width()/10*4,
		height: $(window).height()/10*4
	}
	
	$.extend(dconf, conf);
	
	$('#'+id).dialog(dconf);
}

function open_iframe(conf)
{
	conf.iframe=1
	open_dialog(conf);
}

function open_ajax(conf)
{
	conf.ajax=1
	open_dialog(conf);
}

var dialog_cnt=0;
function open_rtlogview(fileid)
{
	conf=
	{
		title:fileid+' LogWatch',
		url:GW.ln+'/config/logwatch/iframe?id='+fileid
	}
	open_iframe(conf)
}


/* END OF MODULE's_NOTES*/