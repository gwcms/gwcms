/**
 * GateWay CMS 2019
 */

function dump(obj)
{
	try{console.log(obj)}catch(err){}
}

var GW = {
	zero: function(num,count)
	{
		var add=count-String(num).length;
		while(add>0){num='0'+num;add--}
		return num;
	},
	
	json: function(module, func_name, params, callback)
	{
		
		$.getJSON(
				'lt', 
				{'act':'do:json', 'module':module, 'function': func_name, "params":params},
				callback
				);
	
	}	

}


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
