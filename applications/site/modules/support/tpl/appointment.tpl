{include "default_open.tpl"}

{if $page->getContent('trackvisits')}	
	{if $smarty.server.REMOTE_ADDR != '84.15.236.87'}
		{$country = geoip_country_code_by_name($smarty.server.REMOTE_ADDR)}
		{$host=gethostbyaddr($smarty.server.REMOTE_ADDR)}
		{$body="{date('Y-m-d H:i:s')} | {$smarty.server.REMOTE_ADDR} | {$host} | {$country} | {$smarty.server.HTTP_USER_AGENT} | {$smarty.server.REQUEST_URI}\n\n"}

		{$tmp=[subject=>"{$page->title}({$page->path}) visit", 'body'=>$body]}
		{$stat = GW_Mail_Helper::sendMailAdmin($tmp)}
	{/if}
{/if}	


{include file="inputs/inputs.tpl"}

<br />
<div class="row">

	<div class="col-md-4">
		{$page->getContent('text')}
		
	</div>
	<div class="col-md-8">

		{if $smarty.get.success}
			<p class="alert alert-border alert-success">
				{$page->getContent('submit_text')|nl2br}
			</p>
		{else}
			<form role="form" action="{$smarty.server.REQUEST_URI}" method="post">
				<input type="hidden" name="act" type="hidden" value="do:message" />
				<div class="row" style="max-width: 600px">
					
					
					
					<div class="col-md-12">

						<div class="panel panel-primary animated fadeInDown">
							<div class="panel-heading">{gw::ln('/m/APPOINTMENT_FORM')}</div>

							<div class="panel-body">
								<form role="form">
									
									
									<div class="row">
									<div class="col-md-6">
									
{call name=input field=subject type=select options=$subject_opt empty_option=1 required=1 value=$smarty.get.topic}
									</div>
									</div>
									
									<br><br>
									
									<div class="row">
									<div class="col-md-6">
										<span class="badge">2</span> Pasirinkite datą *
										
	<link href="{$app_root}assets/pack/datepicker/bootstrap-datepicker3.standalone.min.css" rel="stylesheet">

	<script src="{$app_root}assets/pack/datepicker/bootstrap-datepicker.js"></script>	
	<script src="{$app_root}assets/pack/datepicker/lang/bootstrap-datepicker.{$app->ln}.min.js"></script> 											
	<div id="calendarblock"></div>
	<input type="hidden" id="date" name="item[date]" required="1">

											
<script>	
	$(function(){
	
$('#calendarblock').datepicker({
	maxViewMode: 0,
	todayHighlight: true,
	format: "yyyy-mm-dd",
	defaultDate: new Date(2021, 8, 1),
	datesDisabled: disabledDates,
	startDate: "+1 day",
	toggleActive: true,
	language: language,
	minDate: curdate,
	//maxDate: ,
	//maxDate: '+2M',
	//numberOfMonths: 2,
	

	beforeShowDay: function (date) {
		var thisdate = date.toYMD();//gw.js
		if (thisdate == curdate) {
			return {
				classes: 'active'
			};
		} 
		

              var day = date.getDay();
	      var datestr = date.toISOString().split('T')[0];
	      
		//console.log($.datepicker.formatDate("yy-mm-dd", date));
		// console.log(DPGlobal.formatDate(date, "yy-mm-dd", 'lt'))

              //day != 0 and day != 6 disable weekends
	      //antr ir ketv
              return day != 0 && day != 6 && day != 1 && day !=5 && day !=3 && datestr < maxdate;                  
          		
	}
});

if (curdate) {
	$('#calendarblock').datepicker('setDate', curdate);
}

$('#calendarblock').datepicker().on('changeDate', function () {
	var value = $('#calendarblock').datepicker('getFormattedDate');
	
	$('#date').val(value);
	
})	
	
	})
var curdate = "{$date}";
var language = "{$ln}";
var disabledDates  = ['2024-02-16'];
var maxdate = "{date('Y-m-d',strtotime('+1 MONTH'))}";

</script>
<style>
	.datepicker .disabled{ color:#ddd !important; }
	.badge{ background-color: orange; color: white; padding: 5px; font-weight: bold;  }
</style>
										
										
									</div>
									<div class="col-md-6">
										{call name=input field=time type=radios options=['11:00','12:00','13:00','16:00','17:00'] options_fix=1 separator='<br><br>' required=1}
									</div>

									</div>
									<br><br>

									
										<span class="badge">4</span> Jūsų kontaktiniai duomenys *
										
										<div class="row">
											<div class="col-md-4">
												{call "input" field=email required=1 type=email}
											</div>		
											<div class="col-md-4">
												{call "input" field=phone required=1 type=text}
											</div>												
											<div class="col-md-4">
												{call "input" field=name required=1 type=text}
											</div>								
										</div>
							{if !$app->user}				
			  {if $m->cfg->recapPublicKey}
				  {$recapPublicKey=$m->cfg->recapPublicKey}
				  <div class="row">
					<div class="col-md-6">
				     <div class="g-recaptcha" data-sitekey="{$recapPublicKey}" style="margin-bottom:5px;"></div>
					     </div>	  

				     <script src='https://www.google.com/recaptcha/api.js' async defer></script>
				     <script>
					 $('#supportForm').submit(function(event) {

					     var response = grecaptcha.getResponse();

					     if(response.length == 0){
						     event.preventDefault();
						     alert('{GW::ln('/G/validation/RECAPTCHA_FAILED')|escape:'javascript'}');
					     }
				       });
				     </script>
				</div>				
			{/if}											
									{/if}

						    
									<br><br>
									<div class="row">
										<div class="col-md-6">
											<button type="submit" class="btn btn-ar btn-primary pull-left">{gw::ln('/m/REGISTER')}</button>
										</div>
									</div>

							</div>
						</div>
					</div>
				</div>


			</form>
		{/if}

	</div>	
</div>

<br ><br >




{include "default_close.tpl"}