function formatSelect2Result(item) {
	if (item.loading)
		return item.text;

	if(item.html)
		return item.html;

		if(item.hasOwnProperty('footer')){
			var markup = "<div class='select2-result-repository clearfix'>" +
					"<div class='select2-result-repository__meta'>" +
		"<div class='select2-result-repository__title'>" + item.title + "</div>";			
			markup += "<small class='text-muted'>"+item.footer+"<small>";
			
			markup += 	"</div>" +
		"</div></div>";
			return markup;
			
		}else{
			return item.title;
		}
}	

function formatSelect2Selection(item) {
	return item.title || item.text;
}
	
	
function initSelectAll(obj, opts)
{
	$.fn.select2.amd.require([
	  'select2/utils',
	  'select2/dropdown',
	  'select2/dropdown/attachBody'
	], function (Utils, Dropdown, AttachBody) {
	  function SelectAll() { }

	  SelectAll.prototype.render = function (decorated) {
		var $rendered = decorated.call(this);
		var self = this;

		var $selectAll = $(
		  '<div class="selectallContain" style="display:none;margin:3px"> <span class="resultinfo"></span> | <a class="selectallbnt" style="margin:2px;" href="#">['+translate_selectall+'<span class="loadeditems"></span>]</a></div>'
		);

		this.$element.data('$rendered', $rendered)

		$rendered.find('.select2-dropdown').append($selectAll);
		console.log('Prideta selec2 pasirinkti visus');

		$selectAll.on('click', function (e) {
			e.preventDefault();
			
			var $results = $rendered.find('.select2-results__option[aria-selected=false]');

			// Get all results that aren't selected
			$results.each(function () {
			  var $result = $(this);

			  // Get the data object for it
			  var data = $result.data('data');

			  // Trigger the select event
			  self.trigger('select', {
				data: data
			  });
			});

			$rendered.find('.selectallContain').hide();
			self.trigger('close');
		});

		return $rendered;
	  };	
	
	
	var dropdownAdapter = Utils.Decorate(
		  Utils.Decorate(
			Dropdown,
			AttachBody
		  ),
		  SelectAll
		);
			
	
		opts.dropdownAdapter= dropdownAdapter;
		finishInitSelAjax(obj, opts);
		//alert();
	})
}
	
function initSelect2Inputs(){
	require(['vendor/select2/js'], function () {
		//$('.gwselect2').select2(); 

		$(".GWselectAjax").each(function(){

			var obj = $(this)
			var urlArgsAddFunc = obj.data('urlargsaddfunc');


			var opts = {	};
						
			if(obj.data('source')){
				opts.ajax = {
					url: obj.data('source'),
					dataType: 'json',
					delay: 250,
					data: function (params) {

						var tmp = {
							q: params.term, // search term
							page: params.page
						};
						if(urlArgsAddFunc){
							$.extend(tmp, eval(urlArgsAddFunc)	);
						}
						
						return tmp;

					},
					processResults: function (data, params) {
						// parse the results into the format expected by Select2
						// since we are using custom formatting functions we do not need to
						// alter the remote JSON data, except to indicate that infinite
						// scrolling can be used
						params.page = params.page || 1;
						
						obj.trigger('resultsload', [params, data]);

						return {
							results: data.items,
							pagination: {
								more: (params.page * 30) < data.total_count
							}
						};
					},
					cache: true
				}
				
				opts.templateResult = formatSelect2Result; // omitted for brevity, see the source of this page
				opts.templateSelection = formatSelect2Selection; // omitted for brevity, see the source of this page
				opts.escapeMarkup = function (markup) {
							return markup;
				}		
			}
			

			if(obj.data('dontcloseonselect'))
			{
				opts.closeOnSelect = false;
			}		
			
			//1 - single select
			//0 || x - multiselect
			var maximumSelectionLength = obj.data('maximumselectionlength')-0
			if(maximumSelectionLength > 1)
			{
				opts.maximumSelectionLength = maximumSelectionLength;
			}				
			
			obj.on('fillitems', function(event, items, append){
					if(append){
						var current = $(this).val();

						for(var index in items){
							if(current.indexOf(items[index].id)!=-1){
								console.log('select_ajax: fillitems append. Remove duplicate id:'+id+', title: '+items[index].title);

								delete index[id];
							}
						}			

					}else{
						$(this).empty();
					}

					var that = this;

								
					var multiselect = obj.data('maximumselectionlength') != '1';
					var keys = {}
					
					$.each(items, function(index, item){
						//console.log(item.title+':'+item.id)
						keys[item.id] = item.id;
						
						$(that).append(new Option(item.title, item.id, multiselect, multiselect));
					} );
					
					if(!multiselect){
						//alert($(this).data('value'));
						//alert($(this).val());
						var prevval = JSON.parse($(this).data('value'));					
						
						if( obj.find("option[value='"+prevval+"']").length > 0 ){
							obj.val(prevval)
						}else{
							alert(obj.data('objecttitle')+" id("+prevval+') Not available');
						}
					}

					$(this).trigger('change');		
				}).on('inittitles', function(event, ids, append){
					
					if(!ids)
						ids = $(this).val()

					var that = this;
					
					$.post($(this).data('source'), { ids: JSON.stringify(ids) }, function(data){						
						if(data.hasOwnProperty('items'))
						{
							$(that).trigger('fillitems', [data.items, append]);
						}else{
							console.log("select_ajax: Items not received. Received content: "+data);
						}

					}, 'json')
				}).on('resultsload', function(event, params, results){
						
					if(obj.data('$rendered')){
						obj.data('$rendered').find('.selectallContain').fadeIn();
						console.log([params, results])
						//console.log(obj.data('bybis'));

						setTimeout(function(){
							var count = obj.data('$rendered').find('.select2-results__option[aria-selected=false], .select2-results__option[aria-selected=true]').length
							obj.data('$rendered').find('.loadeditems').text(' ('+count+')');						
						}, 300)
						
						obj.data('$rendered').find('.resultinfo').text(translate_foundresults+': '+results.total_count);
					}
					
				});


			if(obj.data('onchangeFunc')){
				var f = obj.data('onchangeFunc');
				obj.change(function(){

					if(!$(this).data('init-done')){					
						$(this).data('init-done', 1)
						$(this).data('prev-val', $(this).val())
					}else{
						if($(this).data('prev-val') != $(this).val()){
							f(true);
							$(this).data('prev-val', $(this).val());
						}else{
							f(false);
						}
					}
				}
				).change();			
			}
			
			obj.change(function(){
				$(this).data('value', JSON.stringify($(this).val()));
			});
			
			if(obj.data('btnselectall'))
			{
				initSelectAll(obj, opts);
				//console.log(opts)
			}else{
				finishInitSelAjax(obj, opts);
			}			
			
		});
	

	});	
}

function finishInitSelAjax(obj, opts)
{
	obj.select2(opts);
				//uzkrauti antrastes
	if(obj.data('preload') && obj.data('value')){
			obj.trigger('inittitles');
	}	
}


function addEditControls(obj)
{
	this.name = obj.data('name');					
	this.addBtn = obj.find('.addBtn');
	this.editBtn = obj.find('.editBtn');

	this.valueIdsBtn = obj.find('.valueIdsBtn');
	this.valueRowsBtn = obj.find('.valueRowsBtn');

	this.multiple = obj.data('multiple')-0;	
	this.inputctrl = $('#itemform select[name="item['+this.name+']'+(this.multiple?'[]':'')+'"]');

	var ctrl = this;


	this.selected = function (context)
	{
		if(context.item)
		{
			var item = context.item
			
			if(!ctrl.multiple)
			{
				ctrl.inputctrl.data('value', item.id)
			}
			
			ctrl.inputctrl.trigger('fillitems', [[item], true]);
		}
	}
	
	this.updateOpts = function(){
		ctrl.inputctrl.trigger('inittitles');				
	}

	this.addBtn.click(function(){
		rootgwcms().open_dialog2({ url: $(this).data('url'), iframe:1, title: this.title, close_callback: ctrl.selected })
	})

	this.editBtn.click(function(e){	
		
		var closecallback = e.shiftKey ? ctrl.updateOpts : ctrl.selected ;
		var src = $(this).data(e.shiftKey ? 'listurl' : 'url');
				
		var id = ctrl.inputctrl.val();		
		if(!id)
			return false;

		var url = gw_navigator.url(src, { id: id })
		rootgwcms().open_dialog2({ url: url, iframe:1, title:this.title, close_callback: closecallback })
	})


	this.valueIdsBtn.click(function(){
		var dconf = { width:400,height:250, title: ctrl.valueIdsBtn.attr('title'), buttons: { } }
		var ids = ctrl.inputctrl.val() ? ctrl.inputctrl.val().join(',') : '';
		dconf.html = "<textarea style='width:100%;height:100%' id='importids'>"+ids+"</textarea>";
		dconf.buttons[translate_submit] = function () {
			var ids = $('#importids').val();
			$(this).dialog("close");
			$(this).remove();						
			ids = ids.split(',');
			ctrl.inputctrl.trigger("inittitles", [ids]);
		}

		rootgwcms().open_dialog(dconf)

	});

	this.valueRowsBtn.click(function(){
		var ids = ctrl.inputctrl.val() ? ctrl.inputctrl.val().join(',') : '';

		$.post(obj.data('export_url'), { ids: ids }, function(data){

			$('#importrows, #badrowscont').remove();
			
			var dconf = { title: ctrl.valueRowsBtn.attr('title'), buttons: { } }
			var badrowsconthtml = "<div id='badrowscont' style='margin-bottom:10px;display:none;'>Check bad rows: <textarea style='width:100%;heigh:50px;'></textarea></div>";
			dconf.html = badrowsconthtml+"<textarea id='importrows' style='width:100%;height:100%'>"+data+"</textarea>";
			
			
			dconf.buttons[translate_submit] = function () {
				var rows = $('#importrows').val();

				var dialg=$(this);
				$('#badrowscont').fadeOut();

				$.post(obj.data('import_url'), { rows: rows }, function(data){

					data = JSON.parse(data)

					console.log([data.failcnt, (data.failcnt-0)==0])


					if((data.failcnt-0)==0)
					{
						dialg.dialog("close");
						dialg.remove();	

					}else{
						$('#badrowscont').fadeIn();
						$('#badrowscont textarea').val(data.failedrows);
					}



					if(data.errors)
						gw_adm_sys.notification({ text: data.errors.split("\n").join('<br>'), type:2, time: 1000000  });


					gw_adm_sys.notification({ text: data.message })

					ctrl.inputctrl.trigger("inittitles", [data.ids], true);									
				});



			}						

			rootgwcms().open_dialog(dconf)						
		});	

	});					


	this.resetInput = function()
	{
		ctrl.inputctrl.html("")
		ctrl.inputctrl.val("").trigger("change"); 
	}
	//ctrl.on('chageevent', function(){ console.log('change'); })


}
