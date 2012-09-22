/**
 * GateWay CMS 2012
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


