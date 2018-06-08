var require_config = {
	waitSeconds: 200,
	shim : {
		bootstrap : { "deps" :['jquery'] },
		jqueryui: { "deps" : ['jquery'] },
		nifty: { "deps" : ['jquery','bootstrap'] },
		gwcms: { "deps" : ['jquery','bootstrap','nifty','jqueryui', 'project'] },
		forms: { "deps" : ['gwcms'] },
		sortable: { "deps": ['gwcms'] },
		browser: { "deps" : ['gwcms'] },
		iframeautoheight: { "deps" : ['browser'] }
	},			
	paths: {
                jquery: 'js/jquery-2.2.4.min',
		bootstrap :  "js/bootstrap.min",
		jqueryui: "vendor/jqueryui/jquery-ui.min",
		nifty: "js/nifty.min",
		gwcms: "js/gwcms",
		forms: "js/forms",
		project: "js/gwcms_project",
		sortable: "js/jq/jquery-sortable",
		browser: "js/jq/browser",
		iframeautoheight: "js/jq/jquery.iframe-auto-height.plugin"
	}
}