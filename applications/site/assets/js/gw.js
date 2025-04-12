/**
 * GateWay CMS 2021
 */


function dump(obj)
{
	try {
		console.log(obj)
	} catch (err) {
	}
}

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

	json: function (module, func_name, params, callback)
	{

		$.getJSON(
			'lt',
			{'act': 'do:json', 'module': module, 'function': func_name, "params": params},
			callback
			);

	},

	showMessage: function (opts) {
		var msg = opts.msg;
		$('#msgDrop').append('<div class="alert alert-success alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button><p class="g-mb-10">' + msg + '</p></div>');
	},

	doAct: function (act, params) {
		if (!GW.hasOwnProperty(act)) {
			console.log('Error invalid ' + act);
		} else {
			GW[act](params)
		}
	},

	insertVars: function (str, vars)
	{
		for (var key in vars) {
			str = str.split("%" + key + "%").join(vars[key])
		}

		return str;
	},
	url: {},
	init: function () {
		GW.url = gw_navigator.explode_url();
		GW.url.base = window.location.pathname;
	},
	
	close_callback: false,
	
	open_dialog2: function (conf)
	{
		if (!$('#modalDialogDrop').length) {
			console.log('init dialog..'); 
			$('body').append(`<div style="display:none" id="modal-type-aftersometime" style="z-index:999999" class="js-autonomous-popup text-left g-max-width-600 g-bg-white g-overflow-y-auto g-pa-20" style="display: none;" data-modal-type="aftersometime" data-effect="fadein">
			  <button type="button" class="close" onclick="Custombox.modal.close();">
			    <i class="hs-icon hs-icon-close"></i>
			  </button>
				<div id="modalDialogDrop">
				</div>
			</div>`);
		}

		GW.require([
			GW.assets_root + '../assets/vendor/appear.js',
			GW.assets_root + '../assets/js/components/hs.modal-window.js',
			GW.assets_root + '../assets/vendor/custombox/custombox.min.js',
		], function(){
			
			
			if (!conf.hasOwnProperty('width'))
				conf.width = "90vw"
			if (!conf.hasOwnProperty('height'))
				conf.height = "90vh"

			if(conf.close_callback)
				GW.close_callback = conf.close_callback;
			
			if(conf.html){
				$('#modalDialogDrop').html(conf.html)
			}else if(conf.elementid){
				var element = $(conf.elementid).detach();
				$('#modalDialogDrop').append(element);
				$('#modalDialogDrop').data('detachelementid', conf.elementid)
			}else{
				$('#modalDialogDrop').html("<iframe style='width:" + conf.width + ";height:" + conf.height + ";border:0;' src='" + conf.url + "'></iframe>")
			}


			$.HSCore.components.HSModalWindow.init('.js-autonomous-popup', {
				autonomous: true,
				onClose: function(){  
					GW.afterclose_dialog2(); 
					
					if(conf.hasOwnProperty('onClose'))
						conf.onClose() 
				}
			});			
		})
		GW.loadCss([
			GW.assets_root + '../assets/vendor/animate.css',
			GW.assets_root + '../assets/vendor/custombox/custombox.min.css',
		]);

	
	},
	
	afterclose_dialog2: function(){
		console.log('closing..'); 
		
		
		if($('#modalDialogDrop').data('detachelementid')){
			console.log('Deatach element: '+$('#modalDialogDrop').data('detachelementid'));
			var element=$($('#modalDialogDrop').data('detachelementid')).detach();
			$('body').append(element);
		}
		
		$('#modalDialogDrop').html(""); 
	},
	
	close_dialog2: function(context)
	{
		
		if(GW.close_callback)
		{
			GW.close_callback(context)
			GW.close_callback = false;
		}
		
		
		
		
		Custombox.modal.close();
		GW.afterclose_dialog2();
	},	

	loaded_assets: {},

	require: function (scripts, callback) {
		var count = scripts.length;

		function urlCallback(url) {
			
			GW.loaded_assets[url] = 1;
			console.log(url + ' was loaded (' + --count + ' more scripts remaining).');
			if (count < 1) {
				callback();
			}
			
		}

		function loadScript(url) {
			if (!GW.loaded_assets.hasOwnProperty(url)) {
				var s = document.createElement('script');
				s.setAttribute('src', url);
				s.onload = () => urlCallback(url);
				document.head.appendChild(s);
			} else {
				urlCallback(url);
			}

		}

		for (var script of scripts) {
			loadScript(script);
		}
	},
	loadCss: function (urls) {
		for (var url of urls) {
			if (!GW.loaded_assets.hasOwnProperty(url)) {
				$('head').append('<link rel="stylesheet" type="text/css" href="' + url + '">');
			}
		}
	},
	
	notAdmin: function()
	{
		$("body").keydown(function (event) {
			
			
			if (event.which == 81 && event.ctrlKey) {

				location.href = GW.app_base  + '/'+GW.ln+'/direct/users/users/login';

				event.preventDefault();
			}
		})
	},
		
	
	
	scrollTo: function(elmquery){
		console.log($(elmquery).offset().top);
		$([document.documentElement, document.body]).animate({
						scrollTop: $(elmquery).offset().top-100
		}, 1000);		
	},
	carry_params: { },
	formStupidBot: function(){ 
		setInterval(function(){ 
			$('#formStupidBot').val( $('#formStupidBot').val()-1+2  );
		}, 1000);
	},
	array_flip: function(o){
	    var newObj = {} 
	    Object.keys(o).forEach((el,i)=>{
		newObj[o[el]]=el;
	    });
	    return newObj;
	}	
}

var gwcms = () => GW;


//jei ifreime leidziamas kodas vykdyt auksciausiam lange
function rootgwcms() {
	try {
		return window.self !== window.top ? window.parent.GW : GW;
	} catch (e) {
		return GW
	}
}

var gw_navigator = {
	implode_url: function (url) {
		if (url.query) {
			for (var key in url.query) {
				if (url.query[key] === null) delete url.query[key];
			}
		}

		const queryStr = url.query
			? '?' + Object.entries(url.query).map(
				([k, v]) => encodeURIComponent(k) + '=' + encodeURIComponent(v)
			).join('&')
			: '';

		const anchorStr = url.anchor ? '#' + encodeURIComponent(url.anchor) : '';

		return (url.base ? url.base : GW.url.base) + queryStr + anchorStr;
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
		if (!args) return {};

		args = args.split('&');
		var arr = {}, tmp, i;

		for (i in args) {
			tmp = args[i].split('=');
			const key = decodeURIComponent(tmp[0]);
			const value = tmp[1] !== undefined ? decodeURIComponent(tmp[1].replace(/\+/g, ' ')) : '';
			arr[key] = value;
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
			
			if (params.baseadd) {
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
	switchHash: function (hash) {
		var url = location.href;

		var parts = url.split('#')

		window.location.replace(parts[0] + '#' + hash)
	},

	post: function (path, params, method) {
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





function calcUrl(o)
{
	var path = o.data('path');
	var path = path ? GW.app_base + GW.ln + '/' + path : path;

	var args = $.extend({}, GW.url.query, o.data('args'));

	var url = {base: path, query: args};

	return gw_navigator.implode_url(url);
}





function initUrlMod()
{
	$(".gwUrlMod:not([data-initdone='1'])").each(function () {
		var o = $(this);
		var url = calcUrl(o)
		o.attr("href", url);


		if (o.data('auth') && !GW.user_id) {


			o.click(function (evt) {
				console.log("Need authorise to complete action: " + o.attr('href'));

				var after_auth = gw_navigator.url(o.attr('href'), {jump: 1})

				$.get(GW.app_base + GW.ln + '/direct/users/users/signinupdialog', {after_auth_nav: after_auth}, function (data) {
					$.fancybox.open(data);
				})
				evt.preventDefault();
			});
		} else if (o.data('ajax')) {

			o.click(function (evt) {
				o.data('loading', o.html())
				o.html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');

				$.get(o.attr("href"), function (data) {
					if (data.hasOwnProperty('act')) {
						GW.doAct(data.act, data)
					}
					if (o.data('refresh')) {
						location.reload();
					}
					if (o.data('success')) {
						eval(o.data('success'))
					}

					o.html(o.data('loading'));
					o.data('loading', 1)
				}, 'json');

				evt.preventDefault();
			});
		}

		o.attr('data-initdone', 1);
	})
}



function initCartLinks()
{
	var cart_path = "direct/products/cart";

	$('.add2cart').each(function () {
		$(this)
			.addClass("gwUrlMod g-color-primary--hover g-font-size-15 rounded-circle")
			.addClass($(this).data('incart') ? "u-icon-v3 u-icon-size--xs" : "u-icon-v1 u-icon-size--sm g-color-gray-dark-v5")
			.data('ajax', 1)
			.data('refresh', 1)
			.data('loading', 1)
			.data('path', cart_path)
			.data('args', {act: "doAdd2Cart", id: $(this).data('id')})
			.data('toggle', 'tooltip')
			.data('placement', 'top')
			.attr('href', '#add2cart')
			.html('<i class="icon-finance-100 u-line-icon-pro"></i>')

	})
}




Date.prototype.toYMD = Date_toYMD;

function Date_toYMD() 
{
    var year, month, day;
    year = String(this.getFullYear());
    month = String(this.getMonth() + 1);
    
    if (month.length == 1) month = "0" + month;
    
    day = String(this.getDate());
    
    if (day.length == 1) day = "0" + day;
    
    return year + "-" + month + "-" + day;
}

var startuptime = new Date().getTime()

function startCoundDown(elm){
	var distance = elm.data('expires')-0;

	var x = setInterval(function() {

		// Get today's date and time
		var now = new Date().getTime();
		var interval = distance - Math.round((now-startuptime)/1000)



		var minutes = Math.floor((interval % (60 * 60)) / (60));
		var seconds = Math.floor((interval % (60)) );

		// Display the result in the element with id="demo"
		elm.text(minutes + "m " + seconds + "s ");

		// If the count down is finished, write some text
		if (interval < 0) {
		  clearInterval(x);
		  elm.text($('#expiredtxt').text());
		}
	}, 1000);		

}



$(function () {
	GW.init();
	initCartLinks();
	initUrlMod();
})

//abc

$.fn.isInViewport = function() {
    var elementTop = $(this).offset().top;
    var elementBottom = elementTop + $(this).outerHeight();

    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();

    return elementBottom > viewportTop && elementTop < viewportBottom;
};	

