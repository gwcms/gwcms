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
			location.href = GW.app_base  + 'admin/'+GW.ln+'/admin/system/tools?act=doDebugModeToggle&uri='+encodeURIComponent(location.href)
			event.preventDefault();
		}		


		if (event.which == 53 && event.ctrlKey) {
			var pid=GW.pageid;
			var ppid = GW.pagepid;
			
			location.href = GW.app_base  + 'admin/lt/sitemap/pages/'+pid+'/form?pid='+ppid
			event.preventDefault();
		}	
	});
	
	var helpstring=["[CTRL] + [Q] - Paryškinti vertimus","[CTRL]ARBA[SHIFT] + [Pelės pagr. mygt.] ant paryškinto vertimo - redaguoti vertimą"];
	var helpboxstyle = "background-color:brown;color:white;position:absolute;top:0px;left:0px;display:inline;padding:2px;border-radius:2px;font-size:9px;z-index:99999;";
	$('body').append('<a class="no-print" style="'+helpboxstyle+'" onclick=\'alert("'+helpstring.join('\\n')+'");return false\' href="#">ADM?</a>');
	$('body').append('<style>@media print{  .no-print, .no-print *{ display: none !important;}}</style>');

})
