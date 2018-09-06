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

})
