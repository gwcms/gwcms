function getUrlParam( paramName ) {
    var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' );
    var match = window.location.search.match( reParam );

    return ( match && match.length > 1 ) ? match[1] : null;
}		
		
var BrowseRepository = function (container, type) {

	this.container = container;
	this.type = type;
	
};

BrowseRepository.prototype.loadDir = function (container) {
	container.find('.dircontents').remove();
	container.append('<div class="dircontents"><i class="fa fa-spinner fa-pulse fa-fw"></i></div>');
	var that = this;
	
	
	$.ajax({
		type: "GET",
		url: that.url,
		success: function (data) {
			container.find('.dircontents').html(data);
			
			that.initFolders();
		},
		error: function (error) {
			container.find('.dircontents').html('An error occured');
		},
		data: { dir:container.data('dir'), ftype: that.type  },
		cache: false,
		timeout: 60000

	});
};

////////////////////////// TODO
BrowseRepository.prototype.progressHandling = function (event, that) {

	var percent = 0;
	var position = event.loaded || event.position;
	var total = event.total;

	if (event.lengthComputable) {
		percent = Math.ceil(position / total * 100);
	}
	// update progressbars classes so it fits your code
	$(".progress-bar").css("width", +percent + "%");
	$(".status").text(percent + "%");

	if (percent == 100)
	{
		setTimeout(function () {
			$(".progress-bar").css("width", 0);
			$(".status").text("");
		}, 2000)
	}
};

////////////////////////// TODO
BrowseRepository.prototype.doUpload = function () {
	
	

	var input = $('#fileinput');
	var files = input.get(0).files;
	var that = this;
	var formData = new FormData();

	$.each(files, function (index, file) {
		formData.append('files[]', file, file.name)
	});



	var dir = $('.markselecteddir').data('dir');
	
	var url = gw_navigator.url(input.data('url'), {dir: dir});


	$.ajax({
		type: "POST",
		url: url,
		xhr: function () {
			var myXhr = $.ajaxSettings.xhr();
			if (myXhr.upload) {
				myXhr.upload.addEventListener('progress', function (event) {
					that.progressHandling(event, that)
				}, false);
			}
			return myXhr;
		},
		success: function (data) {
			console.log(data);
			
			that.loadDir(
				$('.markselecteddir').parents('.folder:first')
				)
			
		},
		error: function (error) {
			// handle error
			alert('error');
		},
		async: true,
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		timeout: 60000

	});
};




BrowseRepository.prototype.init = function () {
	var that = this;
	
	
	this.url = this.container.data('url')
	this.loadDir(this.container);
	
	
	
	$('#fileinput').on("change", function (e) { that.doUpload();	});
	
	
	
	$('#returnBtn').click(function(){
	
		var funcNum = getUrlParam( 'CKEditorFuncNum' );
            
	    
	    if(that.type == 'image'){
		    var size=$('#width').val()+'x'+$('#height').val()
		    var fileUrl = gw_navigator.url($('.selectedFile').attr('src'), { size: size });
	    }else{
		    var fileUrl = '/repository'+$('#filename').val();
	    }
		    
	    
	    
	    
            window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl, function() {
                // Get the reference to a dialog window.
                var dialog = this.getDialog();
                // Check if this is the Image Properties dialog window.
                if ( dialog.getName() == 'image' ) {
                    // Get the reference to a text field that stores the "alt" attribute.
                    var element = dialog.getContentElement( 'info', 'txtAlt' );
                    // Assign the new value.
                    if ( element )
                        element.setValue( $('.selectedFile').attr('alt') );
		
		
		//tipo galima pasalint jei thumbnaila suformuociau

		
		
		
                }else{
			
                    var element = dialog.getContentElement( 'info', 'linkDisplayText' );
                    // Assign the new value.
                    if ( element )
                        element.setValue( $('.selectedFile').text() );
		


			
			
	
		}
                // Return "false" to stop further execution. In such case CKEditor will ignore the second argument ("fileUrl")
                // and the "onSelect" function assigned to the button that called the file manager (if defined).
                // return false;
            } );
            window.close();	
	})
	
	$("#reposNewFolder").click(function () {
		
		
	
		var newfolder=window.prompt($(this).text());
		if(!newfolder)
			return false;
			
		var url = gw_navigator.url($(this).data('url'), {packets: 1, act: 'doMkDir', foldername: newfolder});

		$.post({
			url: url,
			dataType: "json",
			success: function (data) {
				gw_adm_sys.runPackets(data);
				that.loadDir(that.container);
			},
			async: true,
			cache: false,
			processData: false,
			timeout: 60000
		});
		
	})	
	
};	

BrowseRepository.prototype.initFolders = function () {
	var that = this;
	
	this.container.find(".folderlink:not([data-initdone='1'])").click(function () {
		
		var obj = $(this).parents('.folder:first');
		
		if(obj.data('expanded'))
		{
			obj.find('.dircontents').remove();;
						
			obj.data('expanded', false);
			
			$(this).find('.fa-folder-open-o:first').removeClass('fa-folder-open-o').addClass('fa-folder-o');
		}else{
			that.loadDir(obj);
			obj.data('expanded', true);
			
			$(this).find('.fa-folder-o:first').removeClass('fa-folder-o').addClass('fa-folder-open-o');
		}
		
		
	}).attr('data-initdone',1);
	
	this.container.find(".file:not([data-initdone='1'])").click(function () {
		
		that.container.find('.selectedFile').removeClass('selectedFile');
		
		$(this).addClass('selectedFile');
		
		$('#filename').val($(this).data('file'));
		$('.imageOptsEnabled').fadeIn();
		
	}).attr('data-initdone',1);
	
	
	this.container.find(".addfiles:not([data-initdone='1'])").click(function(){
		$('.markselecteddir').removeClass('markselecteddir');
		$('#fileinput').click();
		$(this).addClass('markselecteddir');
	}).attr('data-initdone',1);
	
	
};