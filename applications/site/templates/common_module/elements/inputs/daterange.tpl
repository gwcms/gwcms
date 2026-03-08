{if !$value && $default}
	{$value=$default}
{/if}

{$tag_params.autocomplete="off"}

{include file="{$smarty.current_dir}/text.tpl" id=$id}
{if $nights}<span class="nightsCountText" id="{$id}_nights"></span>{/if}

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


	{if !$gwcmssite_input_datereange_loaded}
		{$m->addIncludes("daterange/css", 'css', "applications/admin/static/pack/daterange/daterangepicker.css")}
		
		
		
<script type="text/javascript" src="{$app->sys_base}applications/admin/static/pack/daterange/moment.min.js?v={$GLOBALS.version_short}"></script>
<script type="text/javascript" src="{$app->sys_base}applications/admin/static/pack/daterange/daterangepicker.js?v={$GLOBALS.version_short}"></script>
	{/if}
	{assign var=gwcmssite_input_datereange_loaded value=1 scope=global}	

<script type="text/javascript">
	
	
	{$year = (int)date('Y')}
	{$month = (int)date('m')}
		
	{*require(['gwcms'], function(){
		require(['moment'], function(){  require(['pack/daterange/daterangepicker'], function(){ 
	*}		
	$(document).ready(function () {

		function updateNights_{$id}(start, end) {
			
			var $out = $('#{$id}_nights');

			if (!start || !end) {
				$out.text('');
				return;
			}

			var nights = end.clone().startOf('day')
				.diff(start.clone().startOf('day'), 'days');

			if (isNaN(nights)) {
				$out.text('');
				return;
			}

			$out.text(nights + ' night' + (nights === 1 ? '' : 's'));
			$('#{$id}').data('nights', nights);
			
			{if $nightchangefunction}
				
				{$nightchangefunction}(nights)
			{/if}
		}

		function tryUpdateFromInput_{$id}() {
			var val = ($('#{$id}').val() || '').trim();

			if (!val) {
				updateNights_{$id}(null, null);
				return;
			}

			var parts = val.split(' - ');
			if (parts.length !== 2) return;

			var s = moment(parts[0], 'YYYY-MM-DD', true);
			var e = moment(parts[1], 'YYYY-MM-DD', true);

			if (s.isValid() && e.isValid())
				updateNights_{$id}(s, e);
		}

		var $inp = $('#{$id}');

		$inp.daterangepicker({
			locale: {
				format: 'YYYY-MM-DD'
			},
			alwaysShowCalendars: true,
			autoUpdateInput: false,
			{if $value.0}startDate: "{$value.0}",{/if}
			{if $value.1}endDate: "{$value.1}",{/if}
			autoApply: true
				{if !$noranges}
			,ranges: {
				
	
				{if !$nodefaultranges}
					{for $i=1 to 5}
						{$date = "{$year}-{GW_String_Helper::zero($month)}-01"}
						{$title="{GW::l("/G/date/MONTHS/{intval($month)}")}"}
						{$enddate=date('Y-m-d',strtotime("last day of {$date}"))}
						{if $datetimefiltp1d}
							{$enddate=date('Y-m-d',strtotime("{$enddate} +1 day"))}
						{/if}
			
							{$ranges["{$year} {$title}"]=[$date, $enddate]}
								
						{if $month==1}
							{$year=$year-1}
							{$month=12}
						{else}
							{$month=$month-1}
						{/if}
					{/for}
					
						{$ranges['Today']=["{date('Y-m-d')}", "{date('Y-m-d')}"]}
				{/if}
					
				{foreach $ranges as $key => $range}
					'{$key}': ["{$range.0}", "{$range.1}"]{if !$range@last},{/if}
				{/foreach}	
				
				
			}
				{/if}
		})
		.on("apply.daterangepicker", function (e, picker) {

			picker.element.val(
				picker.startDate.format(picker.locale.format) +
				' - ' +
				picker.endDate.format(picker.locale.format)
			);

			updateNights_{$id}(picker.startDate, picker.endDate);
		});

		var drp = $inp.data('daterangepicker');

		if (drp && drp.startDate && drp.endDate) {
			updateNights_{$id}(drp.startDate, drp.endDate);
		} else {
			tryUpdateFromInput_{$id}();
		}

	});
		
		{*
		});
	})
	*}
	

</script>