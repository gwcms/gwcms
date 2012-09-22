/**
 * Galleria (http://monc.se/kitchen)
 *
 * Galleria is a javascript image gallery written in jQuery. 
 * It loads the images one by one from an unordered list and displays thumbnails when each image is loaded. 
 * It will create thumbnails for you if you choose so, scaled or unscaled, 
 * centered and cropped inside a fixed thumbnail box defined by CSS.
 * 
 * The core of Galleria lies in it's smart preloading behaviour, snappiness and the fresh absence 
 * of obtrusive design elements. Use it as a foundation for your custom styled image gallery.
 *
 * MAJOR CHANGES v.FROM 0.9
 * Galleria now features a useful history extension, enabling back button and bookmarking for each image.
 * The main image is no longer stored inside each list item, instead it is placed inside a container
 * onImage and onThumb functions lets you customize the behaviours of the images on the site
 *
 * Tested in Safari 3, Firefox 2, MSIE 6, MSIE 7, Opera 9
 * 
 * Version 1.0
 * Februari 21, 2008
 *
 * Copyright (c) 2008 David Hellsing (http://monc.se)
 * Licensed under the GPL licenses.
 * http://www.gnu.org/licenses/gpl.txt
 **/

;(function($){

var $$;


/**
 * 
 * @desc Convert images from a simple html <ul> into a thumbnail gallery
 * @author David Hellsing
 * @version 1.0
 *
 * @name Galleria
 * @type jQuery
 *
 * @cat plugins/Media
 * 
 * @example $('ul.gallery').galleria({options});
 * @desc Create a a gallery from an unordered list of images with thumbnails
 * @options
 *   insert:   (selector string) by default, Galleria will create a container div before your ul that holds the image.
 *             You can, however, specify a selector where the image will be placed instead (f.ex '#main_img')
 *   history:  Boolean for setting the history object in action with enabled back button, bookmarking etc.
 *   onImage:  (function) a function that gets fired when the image is displayed and brings the jQuery image object.
 *             You can use it to add click functionality and effects.
 *             f.ex onImage(image) { image.css('display','none').fadeIn(); } will fadeIn each image that is displayed
 *   onThumb:  (function) a function that gets fired when the thumbnail is displayed and brings the jQuery thumb object.
 *             Works the same as onImage except it targets the thumbnail after it's loaded.
 *
**/

$$ = $.fn.galleria = function($options) {
	
	// check for basic CSS support
	if (!$$.hasCSS()) { return false; }
	
	// init the modified history object
	$.historyInit($$.onPageLoad);
	
	// set default options
	var $defaults = {
		insert      : '.galleria_container',
		history     : true,
		clickNext   : true,
		onImage     : function(image,caption,thumb) {},
		onThumb     : function(thumb) {},
		onPrev		: function() {},
		onNext		: function() {},
		onPrevFast	: function() {},
		onNextFast	: function() {},
		preloads	: 3,
		fastSteps	: 3
	};
	

	// extend the options
	var $opts = $.extend($defaults, $options);
	
	// bring the options to the galleria object
	for (var i in $opts) {
		$.galleria[i]  = $opts[i];
	}
	
	// if no insert selector, create a new division and insert it before the ul
	var _insert = ( $($opts.insert).is($opts.insert) ) ? 
		$($opts.insert) : 
		jQuery(document.createElement('div')).insertBefore(this);
		
	// create a wrapping div for the image
	var _div = $(document.createElement('div')).addClass('galleria_wrapper');
	
	// create a caption span
	var _span = $(document.createElement('span')).addClass('caption');
	
	// inject the wrapper in in the insert selector
	_insert.addClass('galleria_container').append(_div).append(_span);
	
	//-------------
	
	return this.each(function(){
		
		// add the Galleria class
		$(this).addClass('galleria');
		
		// loop through list
		$(this).children('li').each(function(i) {
			
			// bring the scope
			var _container = $(this);
			                
			// build element specific options
			var _o = $.meta ? $.extend({}, $opts, _container.data()) : $opts;
			
			// remove the clickNext if image is only child
			_o.clickNext = $(this).is(':only-child') ? false : _o.clickNext;
			
			/************************** Modified to get faster loads ******************************************/
			
			// try to fetch an anchor
			var _a = $(this).find('a');
			
			// reference the original image as a variable and hide it
			var _img = $(this).children('img').css('display','none');
			
			// extract the original source
			var _src = _a.attr('href');
			
			// Added for faster loads
			if (i < _o.preloads) {
				$.galleria.queue.push(_src);
			}
			
			// find a title
			var _title = _a.attr('title');

			// check url and activate container if match
			if (_o.history && (window.location.hash && window.location.hash.replace(/\#/,'') == _src)) {
				_container.siblings('.active').removeClass('active');
				_container.addClass('active');
			}
			
			var _thumb = _a.find('img').addClass('thumb').css('display','none');
			_thumb.data('original', _thumb.attr('rel'));
			_a.replaceWith(_thumb);
			
			// center thumbnails.
			_thumb.css({
				marginLeft: (_thumb.width > 0 ? (-( _thumb.width() - _container.width() )/2) : 0), 
				marginTop: (_thumb.height > 0 ? (-( _thumb.height() - _container.height() )/2) : 0) 
			});
			
			// add the rel attribute
			_thumb.attr('rel',_src);
			
			// add the title attribute
			_thumb.attr('title',_title);
			
			// add the click functionality to the _thumb
			_thumb.click(function() {
				$.galleria.activate(_src);
			});
			
			// hover classes for IE6
			_thumb.hover(
				function() { $(this).addClass('hover'); },
				function() { $(this).removeClass('hover'); }
			);
			_container.hover(
				function() { _container.addClass('hover'); },
				function() { _container.removeClass('hover'); }
			);

			// prepend the thumbnail in the container
			_container.prepend(_thumb);
			
			// show the thumbnail
			_thumb.css('display','block');
			
			// call the onThumb function
			_o.onThumb(jQuery(_thumb));
			
			// check active class and activate image if match
			if (_container.hasClass('active')) {
				$.galleria.activate(_src);
			}
			
			// finally delete the original image
			_img.remove();
				
			/********************************************************************/									
		});
		
		// Added for faster loading. Init loading.
		var _first = $.galleria.nextInQueue();
		if (_first != null) {
			$.galleria.preload(_first);
		}
	});
};

/**
 *
 * @name NextSelector
 *
 * @desc Returns the sibling sibling, or the first one
 *
**/

$$.nextSelector = function(selector) {
	return $(selector).is(':last-child') ?
		   $(selector).siblings(':first-child') :
    	   $(selector).next();
    	   
};

/**
 *
 * @name previousSelector
 *
 * @desc Returns the previous sibling, or the last one
 *
**/

$$.previousSelector = function(selector) {
	return $(selector).is(':first-child') ?
		   $(selector).siblings(':last-child') :
    	   $(selector).prev();
    	   
};

/**
 * Added to enable fast stepping
 *
**/
$$.stepSelector = function(selector, steps) {
	var _s = $(selector).parent().find('> li');
	var _i = _s.index(selector) + steps;
	var _l = _s.length;
	if (_i > (_l - 1)) {
		_i -= _l;
	} else if (_i < 0) {
		_i += _l;	
	}
	return $(selector).parent().find(':nth-child(' + (_i + 1) + ')');
};

/**
 *
 * @name hasCSS
 *
 * @desc Checks for CSS support and returns a boolean value
 *
**/

$$.hasCSS = function()  {
	$('body').append(
		$(document.createElement('div')).attr('id','css_test')
		.css({ width:'1px', height:'1px', display:'none' })
	);
	var _v = ($('#css_test').width() != 1) ? false : true;
	$('#css_test').remove();
	return _v;
};

/**
 *
 * @name onPageLoad
 *
 * @desc The function that displays the image and alters the active classes
 *
 * Note: This function gets called when:
 * 1. after calling $.historyInit();
 * 2. after calling $.historyLoad();
 * 3. after pushing "Go Back" button of a browser
 *
**/

$$.onPageLoad = function(_src) {	
	
	// get the wrapper
	var _wrapper = $('.galleria_wrapper');
	
	// get the thumb
	// Modified to work with latest version of jQuery
	var _thumb = $('.galleria img[rel="'+_src+'"]');
	
	if (_src) {
		
		// new hash location
		if ($.galleria.history) {
			window.location = window.location.href.replace(/\#.*/,'') + '#' + _src;
		}
		
		// alter the active classes
		_thumb.parents('li').siblings('.active').removeClass('active');
		_thumb.parents('li').addClass('active');
	
		// define a new image
		// Modified for faster loads
		var _loader = $.galleria.loader;
		if (!$.galleria.isLoaded(_src) && _loader != null) {
			_wrapper.empty().append(_loader.css("display", "none").fadeIn("fast"));
			_wrapper.siblings('.caption').empty();
		}
		var _img = $.galleria.loadImage(_src, function() {
			// empty the wrapper and insert the new image
			_wrapper.empty().append($(this));
	
			// insert the caption
			// Modified to allow html in captions
			_wrapper.siblings('.caption').html(_thumb.attr('title'));
			
			// fire the onImage function to customize the loaded image's features
			$.galleria.onImage($(this),_wrapper.siblings('.caption'),_thumb);
			
			// add clickable image helper
			if($.galleria.clickNext) {
				$(this).css('cursor','pointer').click(function() { $.galleria.next(); })
			}
			
			// Added to deal with weird resize bug in IE
			$(this).removeAttr('width');
			$(this).removeAttr('height');			
		}).addClass('replaced');
	} else {
		
		// clean up the container if none are active
		_wrapper.siblings().andSelf().empty();
		
		// remove active classes
		$('.galleria li.active').removeClass('active');
	}

	// place the source in the galleria.current variable
	$.galleria.current = _src;
};

/**
 *
 * @name jQuery.galleria
 *
 * @desc The global galleria object holds four constant variables and four public methods:
 *       $.galleria.history = a boolean for setting the history object in action with named URLs
 *       $.galleria.current = is the current source that's being viewed.
 *       $.galleria.clickNext = boolean helper for adding a clickable image that leads to the next one in line
 *       $.galleria.next() = displays the next image in line, returns to first image after the last.
 *       $.galleria.prev() = displays the previous image in line, returns to last image after the first.
 *       $.galleria.activate(_src) = displays an image from _src in the galleria container.
 *       $.galleria.onImage(image,caption) = gets fired when the image is displayed.
 *
**/

$.extend({galleria : {
	current : '',
	onImage : function(){},
	activate : function(_src) { 
		if ($.galleria.history) {
			$.historyLoad(_src);
		} else {
			$$.onPageLoad(_src);
		}
	},
	// Added to enable fast stepping
	getByStepping : function(steps) {
		return $($$.stepSelector($('.galleria img[rel="'+$.galleria.current+'"]').parents('li'), steps)).find('img').attr('rel');
	},
	// Added for faster loads
	getNext : function() {
		return $($$.nextSelector($('.galleria img[rel="'+$.galleria.current+'"]').parents('li'))).find('img').attr('rel');
	},
	// Added to enable fast stepping
	nextFast : function() {
		if (!$.galleria.stopped) {
			$.galleria.activate($.galleria.getByStepping($.galleria.fastSteps));
			$.galleria.onNextFast();
		}
	},
	next : function() {		
		// Added to keep thumbnail scroller in sync with main image		
		if (!$.galleria.stopped) {
		
			// Modified to work with latest version of jQuery
			$.galleria.activate($.galleria.getNext());
			
			// Added additional callback
			$.galleria.onNext();
		}
	},
	getPrev : function() {
		return $($$.previousSelector($('.galleria img[rel="'+$.galleria.current+'"]').parents('li'))).find('img').attr('rel');
	},
	// Added to enable fast stepping
	prevFast : function() {
		if (!$.galleria.stopped) {
			$.galleria.activate($.galleria.getByStepping(-$.galleria.fastSteps));
			$.galleria.onPrevFast();
		}
	},
	prev : function() {
		// Added to keep thumbnail scroller in sync with main image				
		if (!$.galleria.stopped) {
		
			// Modified to work with latest version of jQuery
			$.galleria.activate($.galleria.getPrev());
			
			// Added additional callback
			$.galleria.onPrev();
		}
	},	
	// Added to enable faster loads
	queue : new Array(),
	loaded : new Array(),
	isQueued : function(_src) {
		return $.inArray(_src, $.galleria.queue) > -1;	
	},
	isLoaded : function(_src) {
		return $.inArray(_src, $.galleria.loaded) > -1;
	},
	nextInQueue : function() {
		if ($.galleria.queue.length > 0) {
			var _next = $.galleria.queue.shift();
			if ($.galleria.isLoaded(_next)) {
				return $.galleria.nextInQueue();
			}
			return _next;
		}
		return null;
	},
	preload : function(_src) {
		return $.galleria.loadImage(_src, function() {				
			if ($.galleria.queue.length > 0) {
				var _next = $.galleria.nextInQueue();
				if (_next != null) {
					$.galleria.preload(_next);
				}
			}
		});
	},
	loadImage : function(_src, _onLoad) {
		$.galleria.loaded.push(_src);
		var _img = $(new Image());
		if (typeof _onLoad == 'function') {
			_img.load(_onLoad);
		}
		return _img.attr('src',_src);
	}, 
	// Added to keep thumbnail scroller in sync with main image	
	stopped : false,
	start : function() {
		$.galleria.stopped = false;
	},
	stop : function() {
		$.galleria.stopped = true;
	},
	loader : null
}
});

})(jQuery);


/**
 *
 * Packed history extension for jQuery
 * Credits to http://www.mikage.to/
 *
**/


jQuery.extend({historyCurrentHash:undefined,historyCallback:undefined,historyInit:function(callback){jQuery.historyCallback=callback;var current_hash=location.hash;jQuery.historyCurrentHash=current_hash;if(jQuery.browser.msie){if(jQuery.historyCurrentHash==''){jQuery.historyCurrentHash='#'}$("body").prepend('<iframe id="jQuery_history" style="display: none;"></iframe>');var ihistory=$("#jQuery_history")[0];var iframe=ihistory.contentWindow.document;iframe.open();iframe.close();iframe.location.hash=current_hash}else if($.browser.safari){jQuery.historyBackStack=[];jQuery.historyBackStack.length=history.length;jQuery.historyForwardStack=[];jQuery.isFirst=true}jQuery.historyCallback(current_hash.replace(/^#/,''));setInterval(jQuery.historyCheck,100)},historyAddHistory:function(hash){jQuery.historyBackStack.push(hash);jQuery.historyForwardStack.length=0;this.isFirst=true},historyCheck:function(){if(jQuery.browser.msie){var ihistory=$("#jQuery_history")[0];var iframe=ihistory.contentDocument||ihistory.contentWindow.document;var current_hash=iframe.location.hash;if(current_hash!=jQuery.historyCurrentHash){location.hash=current_hash;jQuery.historyCurrentHash=current_hash;jQuery.historyCallback(current_hash.replace(/^#/,''))}}else if($.browser.safari){if(!jQuery.dontCheck){var historyDelta=history.length-jQuery.historyBackStack.length;if(historyDelta){jQuery.isFirst=false;if(historyDelta<0){for(var i=0;i<Math.abs(historyDelta);i++)jQuery.historyForwardStack.unshift(jQuery.historyBackStack.pop())}else{for(var i=0;i<historyDelta;i++)jQuery.historyBackStack.push(jQuery.historyForwardStack.shift())}var cachedHash=jQuery.historyBackStack[jQuery.historyBackStack.length-1];if(cachedHash!=undefined){jQuery.historyCurrentHash=location.hash;jQuery.historyCallback(cachedHash)}}else if(jQuery.historyBackStack[jQuery.historyBackStack.length-1]==undefined&&!jQuery.isFirst){if(document.URL.indexOf('#')>=0){jQuery.historyCallback(document.URL.split('#')[1])}else{var current_hash=location.hash;jQuery.historyCallback('')}jQuery.isFirst=true}}}else{var current_hash=location.hash;if(current_hash!=jQuery.historyCurrentHash){jQuery.historyCurrentHash=current_hash;jQuery.historyCallback(current_hash.replace(/^#/,''))}}},historyLoad:function(hash){var newhash;if(jQuery.browser.safari){newhash=hash}else{newhash='#'+hash;location.hash=newhash}jQuery.historyCurrentHash=newhash;if(jQuery.browser.msie){var ihistory=$("#jQuery_history")[0];var iframe=ihistory.contentWindow.document;iframe.open();iframe.close();iframe.location.hash=newhash;jQuery.historyCallback(hash)}else if(jQuery.browser.safari){jQuery.dontCheck=true;this.historyAddHistory(hash);var fn=function(){jQuery.dontCheck=false};window.setTimeout(fn,200);jQuery.historyCallback(hash);location.hash=newhash}else{jQuery.historyCallback(hash)}}});
