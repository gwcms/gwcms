<style>
	#newsletter_subscribe *{
		font-family: "Trebuchet MS", Helvetica, sans-serif;
		font-size:14px;
	}
	
	.marginleft25{ margin-left:25px }
	.nl_title{ font-size: 14px !important;font-weight: bold }
	.nl_input_email{ width:160px }
	.error_field{ border: 1px solid red; }
	.success_field{ border: 1px solid green; }
</style>

<script type="text/javascript">
	subscribeUrl = "http://menuturas.lt/gwcms/site/lt/direct/newsletter/newsletter/newsubscribe_menuturas";
	
	$(document).ready(function(e) {

		$("#subscribeForm").submit(function() {
			$.ajax({
				type: "GET",
				url: subscribeUrl,
				data: $("#subscribeForm").serialize(),
				async:true,
				dataType : 'jsonp',   //you may use jsonp for cross origin request
				crossDomain:true,
				success: function(data, status, xhr) {
					if(data.success)
					{
						$('.nl_input_email').addClass('success_field');
						alert('Jūs užsiregistravote naujienlaiškiui, patikrinkite pašto dėžutę, kad patvirtinti prenumeratą');
					}else{
						$('.nl_input_email').addClass('error_field');
						alert('Toks el. paštas jau įregistruotas');
					}

				},
				error: function(jqXHR, textStatus, ex) {
				    alert(textStatus + "," + ex + "," + jqXHR.responseText);
				}		     
			})

			return false;
		});
		   
	});	
 </script>


<div id="newsletter_subscribe">
<div id="subscribefail" style="color:red"></div>
<div id="subscribeok" style="display:none;color:green;">
	Prenumerata sėkminga, patvirtinkite prenumeratą iš pašto dežutės
</div>

<form id="subscribeForm" method="post">
	<input type="hidden" name="act" value="do:new_subscribe" />

<div id="step1" style="">	
<p> 
	<span class="nl_title">Naujienlaiškio prenumerata</span><br />
	<input class="nl_input_email"  type="email" name="email" required="required" style="" placeholder="El@pašto.adresas">
	
	<input type="submit" value="&#9654;" />
	<!--<img src="https://cdn3.iconfinder.com/data/icons/rssesque/7.png">-->
</p>




</div>

</div>

</form>

</div>