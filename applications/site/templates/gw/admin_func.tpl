{if ($app->user && $app->user->is_admin) || GW::$devel_debug || GW::s('DEVELOPER_PRESENT')}	
        <style>
                .lnresulthighl{
			background-color: brown !important;
			color: white !important;
		}
                .transover{
			background-color: blue !important;
		}
        </style>
        <script>
		var gw_lang_results_active = {intval($app->sess['lang-results-active'])};
		var gw_ln = "{$app->ln}";
        </script>
        <script src="{$app_root}assets/js/admin.js?v={$GLOBALS.version_short}"></script>


	<script src="/vendor/ckeditor422/ckeditor.js"></script>
	{*TODO:::   perkelt js i ckedit_inline.js, padaryt redirecta i login jei is admin gaunamas need autj - igyvendint  *}
	{literal}

		
		
		<style>

		</style>
	{/literal}


{/if}
