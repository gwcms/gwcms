;(function($){

var $$;

$$ = $.fn.imageScroller = function(options) {
	
	var defaults = {		
		scrollerClass: "image_scroller",
		onBeforeScroll: function() {},	
		onScroll: function() {},
		imageWidth: 106,
		duration: 200,
		jumpDuration: 200,
		size: 7,
		fastSteps: 3
	};
	
	var opts = $.extend(defaults, options);
	
	for (var i in opts) {
		$.imageScroller[i]  = opts[i];
	}
	
	$(this).addClass($.imageScroller.scrollerClass);
};

$$.scrollImages = function(direction, steps) {
	if (steps > 0) {	
		var el = $("." + $.imageScroller.scrollerClass);		
		var pos = Math.round(el.position().left);
		var scrollLeft = Math.round(-pos/$.imageScroller.imageWidth);
		var scrollRight = $.imageScroller.size + scrollLeft + 1;
		
		
		var active = el.find(".active");
		var activeNr = active.parent().find("> *").index(active) + 1;
		var jump = false;	
		
		if(direction == "right") {
			if (active.is(":first-child")) {
		 		pos = 0;
		 		jump = true;
		 	} else if (active.is(":nth-child(" + scrollRight + ")")) {
		 		pos -= $.imageScroller.imageWidth; 		
		 	} else if (steps > 1) {
		 		if (activeNr < scrollLeft) {
			 		if (steps > $.imageScroller.size) {		 			
						pos = -($.imageScroller.imageWidth * (activeNr - 1));
			 		} else {
			 			pos = 0;	
			 		}
					jump = true;
			 	} else if (activeNr > scrollRight) {
		 			pos -= ($.imageScroller.imageWidth * (activeNr - scrollRight + 1));
		 		}
			} else {		
		 		$.imageScroller.onScroll();
		 		return;
			}
		} else {
			if (active.is(":last-child")) {
				pos = (el.parent().width() - el.width());
				jump = true;
			} else if (active.is(":nth-child(" + scrollLeft + ")")) {			
				pos += $.imageScroller.imageWidth;
			} else if (steps > 1) {
				if (activeNr > scrollRight) {					
					if (steps > $.imageScroller.size) {
						pos = -($.imageScroller.imageWidth * (activeNr - 1));
					} else {
						pos = (el.parent().width() - el.width());
					}
					jump = true;
				} else if (activeNr < scrollLeft) {
					pos += ($.imageScroller.imageWidth * (scrollLeft - activeNr + 1));
				}
			} else {
			 	$.imageScroller.onScroll();
			 	return;
		 	}
		}
		
		var d = jump ? $.imageScroller.jumpDuration : $.imageScroller.duration;
		el.stop().animate({ left:pos + "px" }, d, "linear", $.imageScroller.onScroll());
	}
};

$.extend({imageScroller : {
	fastScrollRight : function() {
		$.imageScroller.onBeforeScroll();
		$$.scrollImages("right", $.imageScroller.fastSteps);
	},
	scrollRight : function() {
		$.imageScroller.onBeforeScroll();
		$$.scrollImages("right", 1);		
	},
	fastScrollLeft : function() {
		$.imageScroller.onBeforeScroll();
		$$.scrollImages("left", $.imageScroller.fastSteps);
	},
	scrollLeft : function() {
		$.imageScroller.onBeforeScroll();
		$$.scrollImages("left", 1);
	}
}
});

})(jQuery);