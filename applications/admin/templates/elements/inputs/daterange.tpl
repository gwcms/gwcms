{$m->addIncludes("daterange/css", 'css', "`$app_root`static/pack/daterange/daterangepicker.css")}

{$tag_params.autocomplete="off"}

{include file="{$smarty.current_dir}/text.tpl" id=$id}

{if $value}
	{$value=explode(' - ',$value)}
{else}
	{*{$value=[date('Y-m-d'),date('Y-m-d')]}*}
{/if}


{*---------months12up------------------------------------------------------*}
{$year = (int)date('Y')}
{$month = (int)date('m')}
{if $months12up}	
	{for $i=1 to 12}
		{$date = "{$year}-{GW_String_Helper::zero($month)}-01"}
			{$title="{GW::l("/G/date/MONTHS/{intval($month)}")}"}

	     {$ranges["{$year} {$title}"]=[$date, date('Y-m-d',  strtotime('+1 DAY',strtotime("last day of {$date}") ) ) ]}	   

	     {if $month==12}
		     {$year=$year+1}
		     {$month=1}
	     {else}
		     {$month=$month+1}
	     {/if}
	{/for}		
{/if}
{*---------months12up------------------------------------------------------*}

<script type="text/javascript">
	
	{$year = (int)date('Y')}
	{$month = (int)date('m')}
		
	require(['gwcms'], function(){
		require(['moment'], function(){  require(['pack/daterange/daterangepicker'], function(){ 
			
			$('#{$id}').daterangepicker({

			    locale: {
				format: 'YYYY-MM-DD'
			    },
			    alwaysShowCalendars: true,
			    autoUpdateInput: false,
				{if $value.0}startDate: "{$value.0}",{/if}
				{if $value.1}endDate: "{$value.1}",{/if}	
				 autoApply: true,
			    ranges: {

			       //'LastMonth': ["{date('Y-m-d',strtotime('first day of last month'))}", "{date('Y-m-d',strtotime('last day of last month'))}"],
				{if !$nodefaultranges}
			       {for $i=1 to 5}
				       {$date = "{$year}-{GW_String_Helper::zero($month)}-01"}
					       {$title="{GW::l("/G/date/MONTHS/{intval($month)}")}"}
						       
						       {$enddate=date('Y-m-d',strtotime("last day of {$date}"))}
							{if $datetimefiltp1d}
								  {$enddate=date('Y-m-d',strtotime("{$enddate} +1 day"))}								
							{/if}
				    '{$year} {$title}':["{$date}","{$enddate}"],	   

				    {if $month==1}
					    {$year=$year-1}
					    {$month=12}
				    {else}
					    {$month=$month-1}
				    {/if}
				{/for}
					{/if}
					{foreach $ranges as $key => $range}
						'{$key}': ["{$range.0}","{$range.1}"],
					{/foreach}
			    'Today': ["{date('Y-m-d')}", "{date('Y-m-d')}"]	   

			       /*
			       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			       'This Month': [moment().startOf('month'), moment().endOf('month')],
			       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			       */
			    }	
			}).on("apply.daterangepicker", function (e, picker) {
				picker.element.val(picker.startDate.format(picker.locale.format)+' - '+picker.endDate.format(picker.locale.format));
			});;			
			
		}) });
	})
	

</script>