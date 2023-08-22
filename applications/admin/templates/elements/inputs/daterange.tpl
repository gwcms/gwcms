{$m->addIncludes("daterange/css", 'css', "`$app_root`static/pack/daterange/daterangepicker.css")}
 {$id="test"}
{include file="{$smarty.current_dir}/text.tpl" id=$id}

{if $value}
	{$value=explode(' - ',$value)}
{else}
	{$value=[date('Y-m-d'),date('Y-m-d')]}
{/if}
<script type="text/javascript">
	
	{$year = (int)date('Y')};
	{$month = (int)date('m')};
		
		
	
	
	
	require(['gwcms'], function(){
		require(['moment'], function(){  require(['pack/daterange/daterangepicker'], function(){ initDateRangeInput() }) });
	})
	
	
	function initDateRangeInput(){
	
	
    $('#{$id}').daterangepicker({

        locale: {
            format: 'YYYY-MM-DD'
        },
	alwaysShowCalendars: true,
	autoUpdateInput: true,
	    startDate: "{$value.0}",
	    endDate: "{$value.1}",	
	     autoApply: true,
	ranges: {
           
           //'LastMonth': ["{date('Y-m-d',strtotime('first day of last month'))}", "{date('Y-m-d',strtotime('last day of last month'))}"],
	   
	   {for $i=1 to 5}
		   {$date = "{$year}-{GW_String_Helper::zero($month)}-01"}
			   {$title="{GW::l("/G/date/MONTHS/{intval($month)}")}"}
			   
		'{$year} {$title}':["{$date}","{date('Y-m-d',strtotime("last day of {$date}"))}"],	   
			
		{if $month==1}
			{$year=$year-1}
			{$month=12}
		{else}
			{$month=$month-1}
		{/if}
	    {/for}
	'Today': ["{date('Y-m-d')}", "{date('Y-m-d')}"]	   

	   /*
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	   */
        }	
    });
	
	
	
	}

</script>