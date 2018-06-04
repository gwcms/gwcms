var Upload = function (input) {

	this.input = input;
	this.container = input.parents('.attachments_container');
};

Upload.prototype.getType = function () {
	return this.file.type;
};
Upload.prototype.getSize = function () {

	var size = 0
	$.each(this.files, function (index, file) {
		size += file.size;
	});

	return size;
};
Upload.prototype.doUpload = function () {

	var files = this.input.get(0).files;
	var that = this;
	var formData = new FormData();

	$.each(files, function (index, file) {
		formData.append(that.input.data('name'), file, file.name)
	});


	formData.append("upload_file", true);
	formData.append('act', 'doStore')


	var url = gw_navigator.url(this.input.data('url'), {packets: 1});

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
		dataType: "json",
		success: function (data) {
			console.log(data);
			gw_adm_sys.runPackets(data);
			that.reinitControls();
			//that.refresh();
			that.reenableSelect();
		},
		error: function (error) {
			// handle error
			that.reenableSelect();
		},
		async: true,
		data: formData,
		cache: false,
		contentType: false,
		processData: false,
		timeout: 60000

	});
};

Upload.prototype.progressHandling = function (event, that) {
	var container = that.container;
	var percent = 0;
	var position = event.loaded || event.position;
	var total = event.total;

	if (event.lengthComputable) {
		percent = Math.ceil(position / total * 100);
	}
	// update progressbars classes so it fits your code
	container.find(".progress-bar").css("width", +percent + "%");
	container.find(".status").text(percent + "%");

	if (percent == 100)
	{
		setTimeout(function () {
			container.find(".progress-bar").css("width", 0);
			container.find(".status").text("");
		}, 2000)
	}
};

Upload.prototype.reinitControls = function () {
	var that = this;

	this.container.find(".btn-remove:not([data-initdone='1'])").click(function () {
		
		if(!confirm(this.title+'?'))
			return false;

		var url = gw_navigator.url(that.input.data('url'), {packets: 1, act: 'doDelete', id: $(this).data('id')});

		$.get({
			url: url,
			dataType: "json",
			success: function (data) {
				gw_adm_sys.runPackets(data);
				that.refresh();
			},
			async: true,
			cache: false,
			processData: false,
			timeout: 60000
		});
	}).attr('data-initdone',1);
	
	
	gw_adm_sys.initObjects(); 

	$('.sortContainer').sortable({ items: '.sortItm', update: function(){ 
			
		
			
		var positions =  [];
		var index = 0;

		$(this).find('.sortItm').each(function(){
			positions.push($(this).data('id'))
		});
			
		var url = gw_navigator.url(that.input.data('url'), {packets: 1, act: 'doSetPositions'});

		
		$.post({
			url: url,
			dataType: "json",
			success: function (data) {
				gw_adm_sys.runPackets(data);
			},
			data: { positions: positions.join(',') },
			cache: false,
			timeout: 60000
		});			
			
	} })
		.disableSelection();	
}

Upload.prototype.refresh = function () {
	var that = this;
	
	that.container.find('.attachments_drop').append('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
	
	$.ajax({
		type: "GET",
		url: this.input.data('url'),
		success: function (data) {
			that.container.find('.attachments_drop').html(data);
			that.reinitControls();
		},
		timeout: 60000
	});
	
}

Upload.prototype.init = function () {
	var that = this;
	
	this.input.on("change", function (e) { that.doUpload();	});	

	that.container.find('.select_attachments_btn').click(function (event) {
		if($(this).data('disabled'))
		{
			return event.preventDefault();
		}
		
		$(this).append('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
		$(this).attr('data-disabled', true);
		
		
		$(this).parents('.attachments_container').find('.gwfileinput').click();
		event.preventDefault();
	})
	
	$(window).focus(function(e){
		$(".select_attachments_btn:not([data-disabled='1'])").each(function(){
			that.reenableSelect($(this))
		})	
	});

	this.refresh();
};

Upload.prototype.reenableSelect = function(obj) {
	
	if(!obj)
		obj = this.container.find('.select_attachments_btn');
	
	obj.attr('data-disabled', false).find('.fa-spinner').remove();
}
