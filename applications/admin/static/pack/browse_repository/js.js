function getUrlParam(paramName)
{
	var reParam = new RegExp('(?:[\\?&])' + paramName + '=([^&]+)', 'i');
	var match = window.location.search.match(reParam);

	if (!match || match.length < 2)
		return null;

	return decodeURIComponent(match[1].replace(/\+/g, ' '));
}

var BrowseRepository = function (container, type)
{
	this.container = container;
	this.type = type;

	this.url = '';
	this.abspath = '';
	this.mode = null;			// 'input' arba null (ckeditor)
	this.browsereturn = null;	// input id/name
};

BrowseRepository.prototype.loadDir = function (container, done)
{
	container.find('.dircontents').remove();
	container.append('<div class="dircontents"><i class="fa fa-spinner fa-pulse fa-fw"></i></div>');

	var that = this;

	$.ajax({
		type: "GET",
		url: that.url,
		success: function (data)
		{
			container.find('.dircontents').html(data);
			that.initFolders();

			if (typeof done === 'function')
				done();
		},
		error: function ()
		{
			container.find('.dircontents').html('An error occured');

			if (typeof done === 'function')
				done(new Error('loadDir failed'));
		},
		data: {
			dir: container.data('dir'),
			ftype: that.type
		},
		cache: false,
		timeout: 60000
	});
};

BrowseRepository.prototype.normalizeStart = function (start)
{
	if (!start)
		return null;

	start = ('' + start).trim();

	// jei ateina pilnas URL
	start = start.replace(/^https?:\/\/[^\/]+/i, '');

	// nuimam "/repository/" prefiksą, jei toks yra
	start = start.replace(/^\/repository\//, '/');

	// užtikrinam, kad būtų su /
	if (!start.startsWith('/'))
		start = '/' + start;

	return start;
};

BrowseRepository.prototype.expandStartPath = function (start)
{
	if (!start)
		return;

	start = start.trim();

	if (!start.startsWith('/'))
		start = '/' + start;

	var parts = start.split('/').filter(Boolean);

	if (!parts.length)
		return;

	var fileName = parts.pop();
	var folders = parts;

	this._expandFoldersSequential(folders, function ()
	{
		this._selectFileByName(fileName);
	}.bind(this));
};

BrowseRepository.prototype._findFolderEl = function (scopeEl, nextDir)
{
	var candidates = [];
	var d = nextDir;

	if (!d.startsWith('/'))
		d = '/' + d;

	candidates.push(d);						// /flags
	candidates.push(d + '/');				// /flags/
	candidates.push(d.substring(1));		// flags
	candidates.push(d.substring(1) + '/');	// flags/

	for (var i = 0; i < candidates.length; i++)
	{
		var val = candidates[i];

		var el = scopeEl.find('.folder[data-dir="' + val + '"]').first();
		if (el.length)
			return el;
	}

	// fallback pagal tekstą (jei folderlink rodo tik pavadinimą)
	var name = d.split('/').filter(Boolean).pop();

	var fallback = scopeEl.find('.folderlink').filter(function ()
	{
		return ($(this).text() || '').trim() === name;
	}).first();

	if (fallback.length)
		return fallback.parents('.folder:first');

	return $();
};

BrowseRepository.prototype._expandFoldersSequential = function (folders, done)
{
	var that = this;

	var currentFolderEl = that.container;
	var currentDir = currentFolderEl.data('dir') || '/';

	function step(i)
	{
		if (i >= folders.length)
		{
			if (typeof done === 'function')
				done();
			return;
		}

		var folderName = folders[i];

		if (!currentDir.endsWith('/'))
			currentDir += '/';

		var nextDir = (currentDir === '/' ? '/' : currentDir) + folderName;

		var nextFolderEl = that._findFolderEl(currentFolderEl, nextDir);

		if (!nextFolderEl.length)
		{
			console.warn('Folder not found:', nextDir);

			// debug: parodyk data-dir variantus šiame lygyje
			try
			{
				var dirs = currentFolderEl.find('.folder[data-dir]').map(function ()
				{
					return $(this).attr('data-dir');
				}).get();

				console.log('Available data-dir in current level:', dirs);
			}
			catch (e) {}

			if (typeof done === 'function')
				done();
			return;
		}

		nextFolderEl.data('expanded', true);

		var icon = nextFolderEl.find('.fa-folder-o:first');
		icon.removeClass('fa-folder-o').addClass('fa-folder-open-o');

		that.loadDir(nextFolderEl, function ()
		{
			currentFolderEl = nextFolderEl;
			currentDir = nextFolderEl.data('dir') || nextDir;
			step(i + 1);
		});
	}

	step(0);
};

BrowseRepository.prototype._selectFileByName = function (fileName)
{
	if (!fileName)
		return;

	var that = this;

	var file = that.container.find('.file[data-file="' + fileName + '"]').first();

	if (!file.length)
		file = that.container.find('.file[data-file$="/' + fileName + '"]').first();

	if (!file.length)
		file = that.container.find('.file[src$="/' + fileName + '"]').first();

	if (!file.length)
		file = that.container.find('.file').filter(function ()
		{
			return ($(this).text() || '').trim() === fileName;
		}).first();

	if (!file.length)
	{
		console.warn('File not found:', fileName);
		return;
	}

	that.container.find('.selectedFile').removeClass('selectedFile');
	file.addClass('selectedFile');

	$('#filename').val(file.data('file') || fileName);
	$('.imageOptsEnabled').fadeIn();

	try
	{
		file.get(0).scrollIntoView({ block: 'center' });
	}
	catch (e) {}
};

BrowseRepository.prototype.progressHandling = function (event, that)
{
	var percent = 0;
	var position = event.loaded || event.position;
	var total = event.total;

	if (event.lengthComputable)
		percent = Math.ceil(position / total * 100);

	$(".progress-bar").css("width", +percent + "%");
	$(".status").text(percent + "%");

	if (percent == 100)
	{
		setTimeout(function ()
		{
			$(".progress-bar").css("width", 0);
			$(".status").text("");
		}, 2000);
	}
};

BrowseRepository.prototype.doUpload = function ()
{
	var input = $('#fileinput');
	var files = input.get(0).files;
	var that = this;
	var formData = new FormData();

	$.each(files, function (index, file)
	{
		formData.append('files[]', file, file.name);
	});

	var dir = $('.markselecteddir').data('dir');
	var url = gw_navigator.url(input.data('url'), { dir: dir });

	$.ajax({
		type: "POST",
		url: url,
		xhr: function ()
		{
			var myXhr = $.ajaxSettings.xhr();
			if (myXhr.upload)
			{
				myXhr.upload.addEventListener('progress', function (event)
				{
					that.progressHandling(event, that);
				}, false);
			}
			return myXhr;
		},
		success: function (data)
		{
			console.log(data);

			that.loadDir($('.markselecteddir').parents('.folder:first'));
		},
		error: function ()
		{
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

BrowseRepository.prototype.init = function ()
{
	var that = this;

	this.url = this.container.data('url');
	this.abspath = "";

	var start = getUrlParam('start');
	start = this.normalizeStart(start);

	this.loadDir(this.container, function ()
	{
		if (start)
			that.expandStartPath(start);
	});

	$('#fileinput').on("change", function ()
	{
		that.doUpload();
	});

	$('#returnBtn').click(function ()
	{
		var fileUrl = '';

		if (!that.container.find('.selectedFile').length)
		{
			alert('Select a file first');
			return;
		}

		if (that.type == 'image')
		{
			if ($('#width').val() && $('#height').val())
			{
				var size = $('#width').val() + 'x' + $('#height').val();
				fileUrl = that.abspath + gw_navigator.url($('.selectedFile').attr('src'), { size: size });
			}
			else
			{
				fileUrl = that.abspath + gw_navigator.url($('.selectedFile').attr('src'), { size: false });
			}
		}
		else
		{
			fileUrl = that.abspath + '/repository' + $('#filename').val();
		}

		// INPUT MODE
		if (that.mode === 'input' && that.browsereturn && window.opener)
		{
			var doc = window.opener.document;
			var el = doc.getElementById(that.browsereturn) ||
				doc.querySelector('[name="' + that.browsereturn.replace(/"/g, '\\"') + '"]');

			if (!el)
			{
				alert('Input not found in opener: ' + that.browsereturn);
				return;
			}

			el.value = fileUrl;

			try
			{
				el.dispatchEvent(new Event('input', { bubbles: true }));
				el.dispatchEvent(new Event('change', { bubbles: true }));
			}
			catch (e) {}

			window.close();
			return;
		}

		// CKEDITOR MODE
		var funcNum = getUrlParam('CKEditorFuncNum');
		if (funcNum && window.opener && window.opener.CKEDITOR)
		{
			window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl, function ()
			{
				var dialog = this.getDialog();

				if (dialog.getName() == 'image')
				{
					var element = dialog.getContentElement('info', 'txtAlt');
					if (element)
						element.setValue($('.selectedFile').attr('alt'));
				}
				else
				{
					var element = dialog.getContentElement('info', 'linkDisplayText');
					if (element)
						element.setValue($('.selectedFile').text());
				}
			});

			window.close();
			return;
		}

		alert('No return target (neither browsereturn nor CKEditorFuncNum)');
	});

	$("#reposNewFolder").click(function ()
	{
		var newfolder = window.prompt($(this).text());
		if (!newfolder)
			return false;

		var url = gw_navigator.url($(this).data('url'), { packets: 1, act: 'doMkDir', foldername: newfolder });

		$.post({
			url: url,
			dataType: "json",
			success: function (data)
			{
				gw_adm_sys.runPackets(data);
				that.loadDir(that.container);
			},
			async: true,
			cache: false,
			processData: false,
			timeout: 60000
		});
	});
};

BrowseRepository.prototype.initFolders = function ()
{
	var that = this;

	this.container.find(".folderlink:not([data-initdone='1'])").click(function ()
	{
		var obj = $(this).parents('.folder:first');

		if (obj.data('expanded'))
		{
			obj.find('.dircontents').remove();
			obj.data('expanded', false);

			$(this).find('.fa-folder-open-o:first').removeClass('fa-folder-open-o').addClass('fa-folder-o');
		}
		else
		{
			that.loadDir(obj);
			obj.data('expanded', true);

			$(this).find('.fa-folder-o:first').removeClass('fa-folder-o').addClass('fa-folder-open-o');
		}
	}).attr('data-initdone', 1);

	this.container.find(".file:not([data-initdone='1'])").click(function ()
	{
		that.container.find('.selectedFile').removeClass('selectedFile');
		$(this).addClass('selectedFile');

		$('#filename').val($(this).data('file'));
		$('.imageOptsEnabled').fadeIn();
	})
	.on('dblclick', function ()
	{
		
		// užtikrinam, kad failas pažymėtas
		that.container.find('.selectedFile').removeClass('selectedFile');
		$(this).addClass('selectedFile');

		$('#filename').val($(this).data('file'));

		// imituojam Select File mygtuko paspaudimą
		$('#returnBtn').trigger('click');
	})	
	.attr('data-initdone', 1);

	this.container.find(".addfiles:not([data-initdone='1'])").click(function ()
	{
		$('.markselecteddir').removeClass('markselecteddir');
		$('#fileinput').click();
		$(this).addClass('markselecteddir');
	}).attr('data-initdone', 1);
};
