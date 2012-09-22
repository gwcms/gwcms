$(function() {
	$("#thumbnails_container").imageScroller({
		onBeforeScroll: function() { $.galleria.stop() },
		onScroll: function() { $.galleria.start() }, 
		duration: 120, 
		imageWidth: 106, 
		size: 7,
		fastSteps: 5
	});
	
	$(".thumbnails_unstyled").addClass("thumbnails");
	$("ul.thumbnails").galleria({
		history: false,
		clickNext: true,
		insert: "#main_image",
		onImage: function(image, caption, thumb) {
			image.css("display", "none").fadeIn(500);
			caption.css("display", "none").fadeIn(500);
			var _li = thumb.parents("li");
			_li.siblings().children("img.selected").fadeTo(500, 0.3);
			thumb.fadeTo("fast", 1).addClass("selected");
			image.attr("title", "Next image");
			var original = thumb.data("original");
			if (original) {
				var originalLink = $("<a></a>").attr("href", original).text("Download original");
				caption.append(" (").append(originalLink).append(")");
			}
		},
		onThumb: function(thumb) {
			var _li = thumb.parents("li");
			var _fadeTo = _li.is(".active") ? "1" : 0.3;
			thumb.css({display: "none", opacity: _fadeTo}).fadeIn(1500);
			thumb.hover(
				function() { 
					thumb.fadeTo("fast", 1);		
				},
				function() {
					_li.not(".active").children("img").fadeTo("fast", 0.3);
				}
			)
		},
		preloads: 3,
		fastSteps: 5,
		onPrev: function() {
			$.imageScroller.scrollLeft();
		},
		onNext: function() {
			$.imageScroller.scrollRight();
		},
		onPrevFast: function() {
			$.imageScroller.fastScrollLeft();
		},
		onNextFast: function() {
			$.imageScroller.fastScrollRight();
		}
	});
	
	$.galleria.loader = $("<div></div>").addClass("loader").append($(new Image()).attr("src","images/loader.gif").attr("title","Loading..."));
	
	prepareArrow = function(arrow) {
		arrow.css({display: "none", opacity: 0.5, "padding-top": "28px"}).fadeIn( 1000);			
		arrow.hover(
			function() {
				arrow.fadeTo("fast", 1);
			},
			function() {
				arrow.fadeTo("fast", 0.5);			
			}
		);	
	}
	
	var leftArrow = $("#left_arrow");
	prepareArrow(leftArrow);
	leftArrow.click(function() {
		$.galleria.prev();	
	});
	
	var rightArrow = $("#right_arrow");
	prepareArrow(rightArrow);
	rightArrow.click(function() {
		$.galleria.next();
	});
	
	var fastNavigation = false;
	if (fastNavigation) {
		var leftFastArrow = $("#left_fast_arrow");
		prepareArrow(leftFastArrow);
		leftFastArrow.click(function() {
			$.galleria.prevFast();
		});
		
		var rightFastArrow = $("#right_fast_arrow");
		prepareArrow(rightFastArrow);
		rightFastArrow.click(function() {
			$.galleria.nextFast();
		});
	}
});

$(document).bind("keydown", "left", function() {
	if (!KeyboardNavigation.widgetHasFocus()) {
		$.galleria.prev();
	}
});
$(document).bind("keydown", "right", function() {
	if (!KeyboardNavigation.widgetHasFocus()) {
		$.galleria.next();
	}
});

var KeyboardNavigation = {
	widgetHasFocus: function() {
		if(typeof _jaWidgetFocus != 'undefined' && _jaWidgetFocus) {
			return true;
		}
		return false;
	}
}