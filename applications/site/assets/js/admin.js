$(function () {
	$(".lnresult").mousedown(function (event) {
		if (event.ctrlKey) {
			//alert($(this).data('key') + event.button);
			
			var searchmod = $(this).hasClass('transover') ? 'translations_over': 'translations';
	
			window.open("/admin/"+gw_ln+"/datasources/"+searchmod+"/?transsearch=" + encodeURIComponent($(this).data('key')))
			
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

	});
	
	
	var helpstring=["[CTRL] + [Q] - Paryškinti vertimus","[CTRL] + [Pelės pagr. mygt.] ant paryškinto vertimo - atverti redagavimą admin sistemoje"];
	var helpboxstyle = "background-color:brown;color:white;position:absolute;top:0px;left:0px;display:inline;padding:2px;border-radius:2px;font-size:9px;z-index:99999;";
	$('body').append('<a style="'+helpboxstyle+'" onclick=\'alert("'+helpstring.join('\\n')+'");return false\' href="#">ADM?</a>');

})
