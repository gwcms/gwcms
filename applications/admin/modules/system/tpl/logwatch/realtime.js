

rt_watch_count=0;

function rt_logwatch(conf) 
{
	
    this.timer = null;
    this.id = ++rt_watch_count;
	this.file = conf.file;

   	this.LOGWATCH_TRIM_LINES=1000;


   	this.waiting_response = false;
   	this.last_offset = 0;


   	this.container_id = 'rt_watch_'+this.id;
   	this.config = conf;
   	
   	console.log(conf);


		conf.container.html("\
			<div class='rt_logwatch_status' id='"+this.container_id+"status'><span>Loading...</span></div>\
			<div class='rt_logwatch_ta' id='"+this.container_id+"' ></div>\
			");

	
		
	this.textarea_j= $('#'+this.container_id);
	this.textarea=this.textarea_j.get(0);
		
	this.statusObj=$('#'+this.container_id+"status");
	this.autoscroll=true;
   	
	this.autoScrollCheck = function()
	{
		var scroll_height =  this.textarea_j[0].scrollHeight;
		var scroll_top=this.textarea_j.scrollTop();
		var scroll_up=scroll_top-scroll_height+this.textarea_j.height();		
		
		//debug
		//this.statusObj.append('<span>scoll: '+scroll_up+'</span>');		
		
		if(scroll_up < -100 && this.autoscroll){
			this.autoScrollChange(false);
		}else if(scroll_up > -100 && !this.autoscroll){
			this.autoScrollChange(true);
		}
	}
	
	this.autoScrollChange = function(val)
	{
		this.statusObj.append('<span>Auto scoll: '+(val?'on':'off')+'</span>');
		this.autoscroll=val;
	}
	
	this.autoScroll = function(first)
	{
		var scroll_height =  this.textarea_j[0].scrollHeight;
	
		if(first || this.autoscroll )
			this.textarea_j.scrollTop(scroll_height+1000);
	}
	
	this.update1 = function(str, first)
	{		
		
		this.textarea_j.children('span:last-child').css({"background-color":"#fff"})
		this.statusObj.children('span').fadeOut();
		this.statusObj.children(":hidden").remove();
		
		if(!str)
			return false;
		
		if(first)
		{
			this.textarea_j.html('');
			str = str.split('\n').join('</span>\n<span>');
		}
		
		var childs = this.textarea_j.children('span');
		
		
		//remove over
		if(childs.length > this.LOGWATCH_TRIM_LINES+10)
		{
			var cut_lines = childs.length - this.LOGWATCH_TRIM_LINES;
			for(var i=0; i < cut_lines; i++)
				$(childs.get(i)).remove();
			
			this.statusObj.append('<span>Shift off: '+cut_lines+'</span>');
		}

		this.autoScrollCheck();
	
		this.textarea_j.append('<span style="display:none">'+str+'</span>');
		
		this.textarea_j.children('span:last-child').fadeIn();
		
		this.autoScroll(first);
	}

    this.update = function() 
    {

    	var _this = this;
		$.get(rt_watch_url,
			{
				'act':'do:get_updates',
				'id':this.file,
				'offset':this.last_offset,
				'uniq': new Date().getTime()
			}, 
			function(data) {
				eval('var $resp='+data);
				
				_this.update1($resp.data, _this.last_offset==0);
				_this.last_offset=$resp.offset;
			});	       
    }

    this.start = function() {
        var _this = this;
        this.timer = setInterval(function(){ _this.update(); }, this.config.time);
    }
    
    this.stop = function() {
        clearInterval(this.timer);
    }
    
    this.destroy = function() {
    	this.stop();
    }

    this.start();
    this.update();
} 


function substr_count( haystack, needle, offset, length ) {

    var pos = 0, cnt = 0;

    haystack += '';
    needle += '';
    if(isNaN(offset)) offset = 0;
    if(isNaN(length)) length = 0;
    offset--;

    while( (offset = haystack.indexOf(needle, offset+1)) != -1 ) {
        if(length > 0 && (offset+needle.length) > length){
            return false;
        } else{
            cnt++;
        }
    }

    return cnt;
}

$(function(){
	/*
	$('head').append('\
			<style>\
				.rt_logwatch_ta{ display:block; resize:both; padding:0; }\
				.rt_logwatch_ta span:last-child { background-color: orange; } \
				.rt_logwatch_status{padding:5px;border:1px solid silver;background-color:#ddd;position:fixed;top:auto;left:auto;}\
			</style>\
			');
	*/		
})
