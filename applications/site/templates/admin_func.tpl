{if ($app->user && $app->user->is_admin) || GW::$devel_debug || GW::s('DEVELOPER_PRESENT')}	
        <style>
                .lnresulthighl{ background-color: brown !important; color: white !important; }
                .transover{ background-color: blue !important; }
        </style>
        <script>
                var gw_lang_results_active = {intval($app->sess['lang-results-active'])};
                var gw_ln = "{$app->ln}";
        </script>
        <script src="{$app_root}assets/js/admin.js?v={$GLOBALS.version_short}"></script>
		
		
		<script src="https://cdn.ckeditor.com/4.10.0/standard-all/ckeditor.js"></script>
		
<script>
		/*
		// The inline editor should be enabled on an element with "contenteditable" attribute set to "true".
		// Otherwise CKEditor will start in read-only mode.
		var introduction = document.getElementById( 'introduction' );
		//introduction.setAttribute( 'contenteditable', true );

				*/
</script>


		
{/if}
