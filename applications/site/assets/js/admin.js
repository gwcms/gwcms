var gw_adm_sys = {
	url: {},
	
	init: function ()
	{
		gw_adm_sys.url = gw_navigator.explode_url(window.location.href);		
		gwcms_project.init();
		GW.init_time = new Date().getTime();
		$(document).ready(gw_adm_sys.init_after_load);
		gw_adm_sys.initObjects();
		
		
	},
	init_after_load: function ()
	{
		gw_session.init();
		gw_server_time.init();
		/*USED*/

		//gw_login_dialog.open();
	},
	updateNotifications: function (count)
	{

	},
	notify: function (errlevel, text, opts) {
		$.niftyNoty({
			type: errlevel,
			container: '#gwcms-dynamic-alerts-container',
			html: text,
			focus: false,
			container: 'floating',
			timer: (opts && opts.hasOwnProperty('timer') ? opts.timer: 3000)
		});
	},
	init_iframe_open: function(){
		$(".iframeopen:not([data-initdone='1'])").click(function(event){
			
			if(event.ctrlKey){
				event.stopPropagation();
				var url = gw_navigator.url($(this).attr('href'), { 'clean': false })
				window.open(url, '_blank').focus();
				return;
			}
			
			var title = $(this).attr('title');
			if(!title){
				var tmp = $(this).find('.alabel').text();
				if(tmp){
					title = tmp;
				}else{
					title = $(this).text();
				}
			}
			
			//hold ctrl key & moouse click  to exit iframe
			title = "<span  data-url='"+$(this).attr('href')+"' onclick='if(window.event.ctrlKey){ window.open($(this).data(\"url\")) }else{ return false }'>"+title+'</span>';
			
			var dialogwidth = $(this).data('dialog-width') ? $(this).data('dialog-width') : 800;
			var opts = { url: $(this).attr('href'), iframe:1, title:title, widthOffset:0, minWidth:dialogwidth };
			
			if($(this).data('dialog-minheight'))
				opts.minHeight=$(this).data('dialog-minheight');
			
			rootgwcms().open_dialog2(opts)
			event.stopPropagation();
			return false;
		}).attr('data-initdone',1);
	},
		
	list_items_count:0,
	
	init_list: function()
	{
		$('.setListParams').on('submit', function(){
			var args = {act:'doSetListParams' }
			args[$(this).attr('name')] = $(this).val()
			gw_navigator.jump(false, args)
		}).keypress(function(e){
			var keyCode = e.keyCode || e.which;
			if (keyCode === 13) { 
				
				$(this).trigger( "submit" );
				e.preventDefault();
				return false;
			}			
		});
		
		$('.gwActiveTable td').mousedown(function (event) {
			if (event.ctrlKey && $(event.target).parents('a').length == 0) {
				
				//alert($(this).data('key') + event.button);
				var field = this.className.replace(/dl_cell_/,'');
				
				
				gwcms.addFilters(field, $(this).text().trim());
				
				
				//var searchmod = $(this).hasClass('transover') ? 'translations_over': 'translations';

				//window.open("/admin/"+gw_ln+"/datasources/"+searchmod+"/?transsearch=" + encodeURIComponent($(this).data('key')))

				event.preventDefault();
			}


		});
		
		$('.gwColumn').mousedown(function(e) {
			switch (e.which) {
			    case 3:
				
				console.log($(this).position());
				e.preventDefault();
				gw_adm_sys.contextMenu($(this),  gw_navigator.url(this.href, { act:'doColumnMenu', field:$(this).data('field') }))
			break;
			    case 2:
				alert('Middle Mouse button pressed.');
			break;
			
			}
		}).contextmenu(function() {
			return false;
		});
		
		$('body').click(function(){
			if(gw_adm_sys.contextMenuPresent){
				gw_adm_sys.clearContextMenu();
			}
		})
		
		initSearchReplace();
	},
	
	contextMenu: function(el,url, detach){
		gw_adm_sys.clearContextMenu();
		
		$.get(url, function(data){
			gw_adm_sys.contextMenuPresent=true;
			el.addClass('labelForContextMenu');
			el.append('<div class="contextMenu dropdown-menu2"><ul>'+data+'</ul></div>')
			
			if(detach){
				var rect = $('.contextMenu').get(0).getBoundingClientRect()
				el = $('.contextMenu').detach();
				el.css('position','absolute')
				el.css('top', rect.y);
				el.css('left', rect.x);
				$('body').append(el);
				$('body').click(function(){ gw_adm_sys.clearContextMenu() })
				
			}
			
		})
	},
	clearContextMenu: function()
	{
		$('.contextMenu').remove();
		gw_adm_sys.contextMenuPresent=true;
		$('.labelForContextMenu').removeClass('labelForContextMenu');
	},
	
	gwws: false,
	
	initWS: function(config){
				
		gw_adm_sys.gwws = new GW_WS()
		
		var gwws = gw_adm_sys.gwws;
			
		gwws.registerEvent('connect', 'main', function () {
			gwws.authorise({ username: config.user, pass: config.apikey })
		});
		
		gwws.registerEvent('authorise', 'main', function (data) {			
			console.log('WSC Authorised!')				
		});
		
		gwws.registerMessageCallback('messageprivate','main', function(data){ 
			console.log({"Private_message_received":data}); 
			
			//perduodama json uzkoduota zinute su parametrais title,text
			var decoded = JSON.parse(data.data);
			
			if(!decoded.hasOwnProperty('action'))
				decoded.action = 'notification';
			
			gw_adm_sys.runPackets([decoded]);
			
		});
		
		gwws.connect("wss://"+config.host+":"+config.port+"/irc");
	},
	
	
	bgTaskOpen:function(data){
		
		var str = `<div id="demo-wg-server" class="hide-small mainnav-widget-content">
		<ul class="list-group">
		
							
			<li class="mar-btm backgroundTask" id="backgroundtask_`+data.bgtaskid+`" title="id: ">
				<span style="display:none" class="startTime">`+data.starttime+`</span>
				<span class="label label-primary pull-right timeGoing">0 s</span>
				<p>`+data.title+`</p>
								<span style="display:none" class="expectedDuration">`+data.expectedDuration+`</span>
				<div class="progress progress-sm">
					<div class="progress-bar progress-bar-mint" style="width: 1%;">
						<span class="sr-only">1%</span>
					</div>
				</div>
							</li>
			</ul>
		</div>`;
		
		console.log(str);
		
		$('#backgroundTasks').append(str);
		
		console.log("open bg task vars:");
		console.log(data);
	},
	
	bgTaskComplete: function(id)
	{
		$('#backgroundtask_'+id).fadeOut("slow", function() {
			
			if($('.backgroundTask:visible').length==0)
			{
				clearInterval(gw_adm_sys.bgTaskRunCountersInterval);
				//$('#backgroundTasks').fadeOut()
			}
			
			
		 })
	},
	
	bgTaskRunCountersInterval:false,
	
	bgTaskRunCounters: function(servertime)
	{
		serverBrowserTimeDiff = servertime - Math.floor(Date.now() / 1000)
		
		gw_adm_sys.bgTaskRunCountersInterval = setInterval(function(){
			
			var nowsecs = Math.floor(Date.now() / 1000)-serverBrowserTimeDiff;
			$('.backgroundTask:visible').each(function(){
				var starttime = $(this).find('.startTime').text()
				var duration = nowsecs - starttime;
				$(this).find('.timeGoing').text(GW.uptime(duration))
				var expecteddur=$(this).find('.expectedDuration').text();
				
				if(expecteddur){
					var progress = Math.round(duration/expecteddur*100);
					progress = progress <= 100 ? progress : 100;
					$(this).find('.progress-bar').css({'width':progress+'%'}).find('.sr-only').text(progress+'%');
					
				}
					
				
			})			
			
		}, 1000);
	},
	
	
	updintervals: {},


	runPackets: function(data){
		for(var packetidx in data)
		{
			var packet = data[packetidx]

			if(gw_adm_sys.packetactions.hasOwnProperty(packet.action)){
				gw_adm_sys.packetactions[packet.action](packet);
			}else{
				console.log(["Action "+packet.action+" not supported", packet])
			}
		}		
	},
	
	runUpdaters: function(id, url, args, intervalSecs, instant){
		var f=function(){
			$.ajax({ url: url, type: "GET", dataType: "json", success: function (data) { gw_adm_sys.runPackets(data) }});
		};
		gw_adm_sys.updintervals[id]=setInterval(f, intervalSecs)
		
		if(instant==1)
			f();
	},

	packetactions: {
		updateProgress: function(data){			
			$('#progess_'+data.id).find('.valuedrop').text(data.progress+'%');
			$('#progess_'+data.id).find('.progress-bar').css({'width':data.progress+'%'})
		},
		clearInterval: function(data){
			console.log(['Clear interval debug', data]);
			clearInterval(gw_adm_sys.updintervals[data.id]);
		},
		notification: function(data)
		{
			gw_adm_sys.notification(data);
		},
		bgtask_open: function(data){
			gw_adm_sys.bgTaskOpen(data)
		},		
		bgtask_close: function(data){
			gw_adm_sys.bgTaskComplete(data.bgtaskid)
		},
		
		update_row: function(data){
			gw_adm_sys.updateRow(data.id)
		},	
		delete_row: function(data){
			gw_adm_sys.deleteRow(data.id)
		},
		update_container: function(data){
			gw_adm_sys.updateContent(data.id, data.value, data)
		},
		update_containers: function(data){
			
			for(var key in data.data)
				gw_adm_sys.updateContent(key, data.data[key])
		}		
	},
	
	updateRow: function(id){
		$.get(location.href, { act:'',background:'',ajax_row:id }, function(data){
			var row=$('#list_row_' + id);
			var prevrow=row.prev();
			row.remove();
			prevrow.after(data);
			
			animateChangedRow(id, 2000);
			
			gw_adm_sys.initObjects();
			
			//if inline edit
			try{initActiveListRows();}catch(err){}
		})
		
	},
	updateContent: function(id, value, opts)
	{
		$('#'+id).html(value);
	},
	
	deleteRow: function(id){
		$('#list_row_' + id).remove();
	},
	
	
	reinitfunctions: [],
	
	registerReinitFunction: function(funct)
	{
		gw_adm_sys.reinitfunctions.push(funct)
	},
		
	initObjects: function()
	{
		$('.shiftbtn').click(function(e){
			
			if(e.shiftKey){ 
				location.href=gw_navigator.url(this.href,{ 'shift_key':1 }); 
				e.preventDefault();
			};			
		})
		
		$(".ajax-link:not([data-initdone='1'])").click(function(event){			
			$(this).data('ownsrc', $(this).html());
			$(this).html('<i class="fa fa-spinner fa-pulse"></i>');
			var obj = $(this);
			var url = gw_navigator.url(this.href, { packets:1 })
			$.ajax({ url: url , type: "GET", dataType: "json", success: function (data) { 
					gw_adm_sys.runPackets(data);
					obj.html(obj.data('ownsrc'));
				}});
			
			event.stopPropagation();
			return false;
			
		}).attr('data-initdone',1);
		
		$(".iframe-under-tr:not([data-initdone='1'])").click(function(){
			var obj = $(this);
						
			var afterc = obj.data('iframe-after-close') ? obj.data('iframe-after-close') : false;
			

			var opt = obj.data('iframeopt') ? obj.data('iframeopt') : {};
			
			openIframeUnderThisTr(this, this.href, afterc, opt)
			event.preventDefault();
		}).attr('data-initdone',1);
		
		
		$(".add-popover:not([data-initdone='1'])").popover().attr('data-initdone',1);
		
		for(var key in gw_adm_sys.reinitfunctions){
			gw_adm_sys.reinitfunctions[key]();
		}
		
		
		$(".iframe-autosize:not([data-initdone='1'])").each(function(){
			gwcms.initAutoresizeIframe($(this), { minHeight: 100, heightOffset: 0, fixedWidth:true, interval:1000})
			$(this).attr('data-initdone',1);
		})
		
		gw_adm_sys.init_iframe_open();
		//gw_checklist.init();
	},
	resetInitState: function(parent){
		console.log('reset '+parent.find("[data-initdone='1']").length);
		parent.find("[data-initdone='1']").attr('data-initdone',0);
	},
	
	notification: function(data){
		var typetrans = {0:'success', 1: "warning", 2: "danger", 3: "info", 4: "dark"}
		//primary info success warning danger mint purple pink dark

		var nndata = {
			type: data.hasOwnProperty('type') ? (typetrans.hasOwnProperty(data.type) ? typetrans[data.type] : 'info')  : 'info',
			message: data.text,
			container: 'floating',
			timer: data.hasOwnProperty('time') ? data.time : 10000,
		};
		
		
		if(data.hasOwnProperty('footer'))
			nndata.message+="<span class='alert-footer'>"+data.footer+'</span>';
		

		if(data.hasOwnProperty('title') && data.title!=false)
			nndata.title = data.title;

		$.niftyNoty(nndata);		
	},
	
	addscript: function(url){
		var head=document.getElementsByTagName('head')[0];
		var newscript=document.createElement('script');
		newscript.async=1;
		newscript.src=url;
		head.appendChild(newscript);	
	},
	initDropdowns: function ()
	{
		$(document).ready(function () {

			/* Clicks within the dropdown won't make
			 it past the dropdown itself */


			$('.gwcms-ajax-dd').click(function () {
				
	
				
				var drop = $(this).parents('.btn-group').find('.dropdown-menu');
				var trigg = $(this)
		

				if (trigg.data('isloaded') == 'loaded'){
					//console.log('data aready loaded');
					return false;
				}

				$.ajax({
					url: trigg.data('url'),
					success: function (data) {
						//console.log('data loaded 1st time');
						trigg.data('isloaded', 'loaded');
						drop.html(data);
						gw_adm_sys.initObjects();
						trigg.dropdown();//fix
					}
				});
				
			})

		});
	},	
	
	
}

$(function () {
	$(".lnresult").mousedown(function (event) {
		var searchmod = $(this).hasClass('transover') ? 'translations_over': 'translations';
		var endpoint =GW.app_base+"admin/"+GW.ln+"/datasources/"+searchmod;
		
		if (event.ctrlKey) {
			//alert($(this).data('key') + event.button);
			
			
			
			window.open(endpoint+"?transsearch=" + encodeURIComponent($(this).data('key')))
			
			event.preventDefault();
		}
		
		if(event.shiftKey){
			var new_val = window.prompt("Please enter new val for "+$(this).data('key'), $(this).html());
			var obj = $(this);
			
			if(new_val != null){
				$.post(endpoint, { act:"doSaveTrans", key:$(this).data('key'), prev_val: $(this).html(), new_val: new_val, ln: GW.ln}, function(data){
					if(data.hasOwnProperty('error')){
						alert(data.error);
					}else if(data.hasOwnProperty('status') && data.status=="ok"){
						obj.html(new_val);
					}else{
						alert('unknown error');
					}
				},'json')
				
				
			}
			event.preventDefault();
		}


	})

	if (gw_lang_results_active)
		$(".lnresult").toggleClass('lnresulthighl');

	$("body").keydown(function (event) {
            console.log(event.which);

		if (event.which == 81 && event.ctrlKey) {
			$(".lnresult").toggleClass('lnresulthighl');

			location.href = gw_navigator.url(location.href, {"toggle-lang-results-active": 1});

			event.preventDefault();
		}
		
		
		
		//ctrl + 1 = pereiti i kita environmenta
		if (event.which == 49 && event.ctrlKey) {
			
			location.href=(GW.app_base  + 'admin/'+GW.ln+'/system/tools?act=doSwitchEnvironment&uri='+encodeURIComponent(location.href))
			
			event.preventDefault();
		}
		
		//ctrl + 2
		
		if (event.which == 50 && event.ctrlKey) {
			location.href = GW.app_base  + 'admin/'+GW.ln+'/system/tools?act=doPullProductionDB&uri='+encodeURIComponent(location.href)
			event.preventDefault();
		}		

		if (event.which == 51 && event.ctrlKey) {
			location.href = GW.app_base  + 'admin/'+GW.ln+'/system/tools?act=doDebugModeToggle&app=SITE&uri='+encodeURIComponent(location.href)
			event.preventDefault();
		}		


		if (event.which == 53 && event.ctrlKey) {
			var pid=GW.pageid;
			var ppid = GW.pagepid;
			
			location.href = GW.app_base  + 'admin/lt/sitemap/pages/'+pid+'/form?pid='+ppid
			event.preventDefault();
		}	
	});
	
	var helpstring=[
		"[CTRL] + [Q] - Paryškinti vertimus",
		"[CTRL]ARBA[SHIFT] + [Pelės pagr. mygt.] ant paryškinto vertimo - redaguoti vertimą",
		"[CTRL] + [5] - peršokti į puslapio redagavimą - struktūra ir tekstai modulyje",
		"[CTRL] + [3] - debug(dev)"
	];
	var helpboxstyle = "background-color:brown;color:white;position:absolute;top:0px;left:0px;display:inline;padding:2px;border-radius:2px;font-size:9px;z-index:99999;";
	
	
	var debug=GW.hasOwnProperty('debugmode')?'|DEBUG':'';
	
	$('body').append('<a class="no-print" style="'+helpboxstyle+'" onclick=\'alert("'+helpstring.join('\\n')+'");return false\' href="#">ADM?'+debug+'</a>');
	$('body').append('<style>@media print{  .no-print, .no-print *{ display: none !important;}}</style>');
	gw_adm_sys.initDropdowns();
	
})






///---------------------------CKEDIT inline------------------------------------------------------------------------------
function starInlineEditing(el)
{
	const dataId = el.dataset.pageid || '';
	const dataKey = el.dataset.contentkey || '';

	fetch('/admin/' + GW.ln + '/sitemap/pages/' + dataId + '/form?json=1&inpname=' + dataKey)
		.then(res => res.text())
		.then(response => {
			try{
				var pagedata = JSON.parse(response)
				var inpdata = pagedata['input_data'][dataKey];

				el.data_parent_id = pagedata.parent_id;

				startEdit(el, inpdata['value'], inpdata['multilang']);
			}catch(err){
				console.log(err)


				if (confirm("Admin authorisation required. Do you want to open the admin window?")) {
				    // Open a new window with specific size
				    window.open(
					"/admin",           // URL to open
					"AdminWindow",      // Window name
					"width=500,height=500" // Window features
				    );
				}								

				//GW.open_dialog2({ url: '/admin', iframe:1, title:"Login is required" })
			}
			//inlineEditNotification(el, ' response received! ');
		})
}

function initProtected(id)	
{


}			

function findContainer(el) 
{
	while(el) {
	  if(el.classList && el.classList.contains('ckedit-container')) return el;
	  el = el.parentNode;
	}
	return null;
}

function inlineEditNotification(el, str)
{
	const container =findContainer(el) 
	if(!container)
		alert(str)

	const msg = document.createElement('div');
	msg.className = 'save-message';
	msg.textContent = str;
	container.appendChild(msg);

	// Remove after animation
	msg.addEventListener('animationend', () => msg.remove());
}

function cancelInlineEditing(el)
{
	var editor = CKEDITOR.instances[el.id];
	editor.destroy();
	el.innerHTML = el.originalHTML; // restore old content
	inlineEditNotification(el, 'Editing canceled');	
	el.setAttribute('contenteditable', 'false');
	changeEditBtnText(el,"Edit")
}

function changeEditBtnText(el, str)
{
	var c = findContainer(el)

	const editbtn = c.querySelector('.edit-inline-btn');
	console.log(editbtn)
	editbtn.textContent = str;
}


const restoreSmarty = (html) => {
	html = html.replace(/<span[^>]*data-protected="([^"]+)"[^>]*>(.*?)<\/span>/g,
		function (match, code, innerContent) {
		return innerContent; // Remove span but keep content
		}
	);
	html = html.replace(/&gt;/g, '>').replace(/&lt;/g, '<');

	return html
};

function startEdit(el, content, multilang, index) {


	changeEditBtnText(el,"Escape")

	const dataId = el.dataset.pageid || '';
	const dataKey = el.dataset.contentkey || '';
	// Assign a unique ID


	el.setAttribute('contenteditable', 'true');


	// Save original HTML to restore on cancel
	el.originalHTML = el.innerHTML;

	//content is replaced with unprocessed one
	if (content)
		el.innerHTML = content;

	var config = {
		allowedContent: true,
		extraAllowedContent: '*(*);*{*}',
		contentsCss: [],
		removePlugins: 'maximize,resize',

		toolbar: [
			{name: 'document', items: ['CustomSave', 'EditInAdmin', '-', 'Source']},
			{ name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
			{ name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
			{ name: 'styles', items: ['Format', 'Font', 'FontSize'] },
			{ name: 'colors', items: ['TextColor', 'BGColor'] },
			{ name: 'links', items: ['Link', 'Unlink'] },
			{ name: 'insert', items: ['Image', 'Table'] }
		]
	};
	config.protectedSource = [];
	config.protectedSource.push( /\{[\s\S]*?\}/g );

	config.extraPlugins = 'dialog,codemirror,filebrowser,showprotected1'

	config.entities = false;
	config.basicEntities = false;
	config.entities_greek = false;
	config.entities_latin = false;	
	config.filebrowserBrowseUrl = '/admin/'+GW.ln+'/sitemap/repository/fileselect';
	config.filebrowserImageBrowseUrl = config.filebrowserBrowseUrl


	// Create CKEditor inline instance
	const editor = CKEDITOR.inline(el.id, config);

	initProtected(el.id);


	//kad parodyt toolbar?
	el.focus();

	inlineEditNotification(el, 'Editing start');
	// Register custom Save command
	editor.addCommand('customSaveCmd', {
		exec: function (editor) {

			//
			var html = editor.getData();
			html = restoreSmarty(html);

			// Prepare URL-encoded POST data
			const formData = new URLSearchParams();
			formData.append('act', 'do:save');
			formData.append('item[id]', dataId);

			//
			formData.append('item[input_data][' + dataKey + (multilang ? '_' + GW.ln : '') + ']', html);
			formData.append('item[parent_id]', el.data_parent_id);


			fetch('/admin/' + GW.ln + '/sitemap/pages/' + dataId + '/form?dialog=1', {// ← your PHP endpoint
				method: 'POST',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				body: formData.toString()
			})
				.then(res => res.text())
				.then(response => {
					console.log('Server response:', response);
					editor.destroy();

					inlineEditNotification(el, ' Content saved! ');

					console.log('TODO: reiktu patikrint ar tikrai pavyko savinimas tik tada perkrauti');
					location.reload();
				})
				.catch(err => {
					console.error('Save error:', err);
					alert('Failed to save content!');
				});


			// Temporary visual feedback
			//inlineEditNotification(' Content saved! ');
		}
	});

	editor.addCommand('EditInAdminCmd', {
		exec: function (editor) {
			var dataId = el.dataset.pageid || '';
			location.href = '/admin/' + GW.ln + '/sitemap/pages/' + dataId + '/form';
		}
	});					



	// Add custom Save button with icon
	editor.ui.addButton('CustomSave', {
		label: 'Save',
		command: 'customSaveCmd',
		toolbar: 'document',
		icon: '   https://cdn-icons-png.flaticon.com/512/489/489707.png' // floppy disk icon
	});

	editor.ui.addButton('EditInAdmin', {
		label: 'Edit in admin',
		command: 'EditInAdminCmd',
		toolbar: 'document',
		icon: 'https://cdn-icons-png.flaticon.com/512/1548/1548672.png' // floppy disk icon
	});
	// Listen for ESC key to cancel editing
	editor.on('contentDom', function () {
		editor.document.on('keydown', function (event) {
			if (event.data.getKey() === 27) { // ESC
				event.data.preventDefault();
				cancelInlineEditing(el);

			}
		});
	});
}


document.querySelectorAll('.ckedit').forEach((el, index) => {

	  const container = document.createElement('div');
		container.classList.add('ckedit-container');
		el.parentNode.insertBefore(container, el);
		container.appendChild(el);

		  const btn = document.createElement('button');
		btn.textContent = 'Edit';
		btn.className = 'edit-inline-btn';
		container.appendChild(btn);

		el.id = 'inlineeditor_' + index;


		btn.addEventListener('click', (e) => {
			if (el.getAttribute('contenteditable') === 'true') {
				cancelInlineEditing(el);
			}else{
				starInlineEditing(el);
			}
		})

	el.addEventListener('click', (e) => {						

		const dataId = el.dataset.pageid || '';
		const dataKey = el.dataset.contentkey || '';

		if(e.ctrlKey){
			location.href = '/admin/' + GW.ln + '/sitemap/pages/' + dataId + '/form';
			return;
		}

		if (!e.shiftKey)
			return;
		
		if (el.getAttribute('contenteditable') === 'true') 
			return;

		e.preventDefault();

		starInlineEditing(el);
	});
});

///---------------------------CKEDIT inline END------------------------------------------------------------------------------