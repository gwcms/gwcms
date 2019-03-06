
var gw_sms = {
	//required content charset=utf-8
	_7bitchars : "@ !\"#%&'()*+-,./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz\n",
	_7bitcharsE : "^{}\\[~]|€",
	maxPartSize : Array(Array(152,66), Array(160,70)),
	target_regex : Array(/(370|8)?(6|7)([0-9]{7})/gi),
	max_parts: 5,

	init: function()
	{
		this._7bitchars_pass = this._7bitchars + this._7bitcharsE;
	},

	_7bit_pass: function(str)
	{		
		var ichars='';
		for(var i=0;i<str.length;i++)
			if(this._7bitchars_pass.indexOf(str.charAt(i))==-1)
				ichars+=str.charAt(i);
		
		return ichars;
	},

	_7bit_msg_length: function(str)
	{
		var strl=0;
		for(var i=0;i<str.length;i++)
			strl+= this._7bitcharsE.indexOf(str.charAt(i))!=-1 ? 2 : 1;
		return strl;
	},

	sms_info: function(str, ddv)
	{
		var inf = {}
		inf.error=0;
		inf.unicode_simbols=this._7bit_pass(str)
		var enc7 = inf.unicode_simbols.length==0;
		inf.length = enc7 ? this._7bit_msg_length(str) : str.length;
		
		if(this.max_parts==1)
			ddv=0;
	
		
		var maxPartSize=this.maxPartSize[ddv?0:1][enc7?0:1];
		inf.parts_count=Math.ceil(inf.length/maxPartSize);
	
		if(!inf.parts_count){
			inf.parts_count=1;
		}else if(inf.parts_count>this.max_parts){
			inf.error=1;inf.parts_count=this.max_parts;
		}else if(inf.parts_count==2){
			if(inf.length <= this.maxPartSize[1][enc7?0:1])
				inf.parts_count=1;
		}
	
		inf.max_length = maxPartSize*this.max_parts;
		inf.encoding = enc7 ? 7 : 16
		inf.chars_left = inf.max_length-inf.length;

		return inf;
	},

	target_count: function(targets)
	{
		var cnt=0;
		for(var i=0;i<this.target_regex.length;i++)
			while(this.target_regex[i].exec(targets) != null)
				cnt++;
		return cnt;
	},
	
	ltChars: 'ĄČĘĖĮŠŲŪŽąčęėįšųūž–„“',
	replaceLtChars: 'ACEEISUUZaceeisuuz-""',

	hasLTChars: function(str)
	{	
		var re = new RegExp('['+this.ltChars+']','i');
		
		return str.match(re);
	},
	
	removeLTChars: function(src)
	{
		  var arr=this.ltChars,arr1=this.replaceLtChars;
		  src=src.split('');for(x=0;x<src.length;x++){i=arr.indexOf(src[x]);
		  if(i!=-1)src[x]=arr1.charAt(i)}return(src.join(''))
	}
	
}


var gw_sms_form = 
{
	timed_exec_delay: 100,
	process_delay_time: 100,
	process_allow: { on_sms_text_changed:1, on_targets_change:1 },
	inputs: {},
	text_suffix: '',
	ddv: true,
	auto_trim: true,
	last_info: [],
	last_target_count: 0,
	onchange: [],

	register_output: function(outp_id,element_id)
	{
		this.outputs[outp_id]=document.getElementById(element_id);
	},

	register_sms_input: function(id)
	{
		this.inputs.text = document.getElementById(id);

		this.inputs.text.onchange = 
		this.inputs.text.onkeypress = 
		this.inputs.text.onkeydown = function(){gw_sms_form.on_sms_text_changed()}
	},

	register_targets_input: function(id)
	{
		this.inputs.targets = document.getElementById(id);
		
		this.inputs.targets.onchange = 
		this.inputs.targets.onkeypress = 
		this.inputs.targets.onkeydown = function(){gw_sms_form.on_targets_change()}
	},
	
	process_delayed: function(funct)
	{
		if(this.process_allow[funct]==-1)
			return false;

		if(this.process_allow[funct]==2)
		{
			this.process_allow[funct]=1;
			return true;
		}

		this.process_allow[funct]=-1;

		setTimeout('gw_sms_form.process_allow["'+funct+'"]=2;gw_sms_form["'+funct+'"]()',this.process_delay_time);
	},

	trigger_onchange: function()
	{
		//run all onchange trigers
		for (x in this.onchange)
			this.onchange[x]();
	},

	on_sms_text_changed: function(instant)
	{

		if(instant!==1 && !this.process_delayed('on_sms_text_changed'))
			return;

		var text = this.inputs.text.value;
		this.last_info = gw_sms.sms_info(text+this.text_suffix, this.ddv);

		if(this.last_info.chars_left < 0 && this.auto_trim)
		{
			this.inputs.text.value=this.trim_text(text);
			this.on_sms_text_changed();
			return;
		}

		this.trigger_onchange();
	},

	on_targets_change: function(instant)
	{
                if(!this.inputs.targets)
			return false;

		if(instant!==1 && !this.process_delayed('on_targets_change'))
			return;


		this.last_target_count=gw_sms.target_count(this.inputs.targets.value);


		this.trigger_onchange()
	},

	total_sms: function()
	{
		if(this.last_info)
			return this.last_info.parts_count*this.last_target_count;
	},


	trim_text: function(text)
	{
		var textl=text.length
		if(textl>1000)
		{
			for(var i=1;i<=textl;i++)
				if(gw_sms.sms_info(text.substr(0,i)+this.text_suffix,this.ddv).error)
					return text.substr(0,i-1);
		}else{
			for(var i=textl;i>0;i--)
				if(!gw_sms.sms_info(text.substr(0,i)+this.text_suffix,this.ddv).error)
					return text.substr(0,i);
		}

		return '';//this should not happen
	},


	info_english: function()
	{
		var str = '';
		
		if(gw_sms.max_parts != 1)
			str += 'Parts count: '+this.last_info.parts_count+'\n'
		
		str += 'Max message length: '+this.last_info.max_length+'\n'+
		//'Error: '+this.last_info.error+'\n'+
		'Chars Left: '+this.last_info.chars_left+'\n'+
		
		'Message type: '+(this.last_info.encoding==7?'LONG':'SHORT')+'\n';

		if(this.last_info.encoding!=7)	
			str+='Simbols which makes message SHORT:\n'+this.last_info.unicode_simbols+'\n';	
		
		return str;
	},
	
	removeLtChars: function()
	{

		this.inputs.text.value = gw_sms.removeLTChars(this.inputs.text.value);
	},
	
	init: function()
	{
		gw_sms.init();
		this.on_sms_text_changed(1);
		this.on_targets_change(1);
	}
}


/*

//use exmpl

gw_sms_form.text_suffix='aoa[]55';
gw_sms_form.register_sms_input('sms_inp');
gw_sms_form.register_targets_input('targets_inp');



gw_sms_form.onchange.push(
	function()
	{
		document.getElementById('test').value = gw_sms_form.info_english();
		document.getElementById('sms_cnt').innerHTML = gw_sms_form.last_info.parts_count;
		document.getElementById('chars_left').innerHTML=gw_sms_form.last_info.chars_left;
		document.getElementById('chars_max').innerHTML=gw_sms_form.last_info.max_length;
		document.getElementById('target_cnt').innerHTML=gw_sms_form.last_target_count;
		document.getElementById('total_sms_cnt').innerHTML=gw_sms_form.total_sms();
	}
)

gw_sms_form.init();

*/

// contacts

function updateDropdown(search)
{
	if(!search)
		return $('#dropdown').hide(); 
	
	$.get(GW.app_base+GW.ln+'/recipients/contacts/ajax', { search:search }, 
		function(data){
			$('#dropdown .results').html(data);initDropdown();
		})
}

function initDropdown()
{
	$('#dropdown .results a').click(
		function(){ 
			addTarget($(this).text());
			$('#dropdown').hide(); 
			return false 
		}).attr('href','#')

		
	var foundresults = $('#dropdown .results a').length
		
	$('#dropdown .result_count').text(foundresults);

	if(foundresults>0)
		$('.foundresults').show();
	else
		$('.foundresults').hide();
	
	$('#dropdown').show(); 
}

function addAll()
{
	$('#dropdown .results a').each(function(){ addTarget($(this).text()) })
	$('#dropdown').hide(); 
}

//gavejai
function addTarget(target)
{
	var t=$('#item__recipients__');
	var x=t.val();
	t.val( x+(x?'\n':'') + target);
	t.trigger('change');
}

function RaddTarget(obj)
{
	addTarget(obj.options[obj.selectedIndex].text);
	obj.selectedIndex=0;			
}

function addGroupContacts(obj)
{		
	$.get(GW.app_base+GW.ln+'/recipients/contacts/ajaxByGroup', { group_id:obj.value }, 
		function(data){
			addTarget(data)
		})
		
	obj.selectedIndex=0;
}
