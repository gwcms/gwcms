{if !$smarty.get.remote}
	{include "default_open_clean.tpl"}
{/if}

<style>
	#newsletter_subscribe *{
		font-family: "Trebuchet MS", Helvetica, sans-serif;
		font-size:12px;
	}
	
	.marginleft25{ margin-left:25px }
</style>

<script type="text/javascript">
	subscribeUrl = "{$smarty.server.REQUEST_URI}";
	/*
$(document).ready(function(e) {

    $("#subscribeForm").submit(function() {
        $.post(subscribeUrl, $("#subscribeForm").serialize()) //Serialize looks good name=textInNameInput&&telefon=textInPhoneInput---etc
        .done(function(data) {
		
		
		
		
		if (data.match(/subscribe_ok_confirm/))
		{
			$('#subscribefail').hide();
			$('#subscribeok').show();
			$('#subscribeForm').hide();
		}
		else {
			$("#subscribefail").text("Klaida!");   


		}
        });
        return false;
    })
});
*/
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
	Naujienlaiškio prenumerata<br />
	<input type="email" name="email" required="required" style="" placeholder="El@pašto.adresas">
	
	<input type="submit" value="Užsisakyti" />
	<!--<img src="https://cdn3.iconfinder.com/data/icons/rssesque/7.png">-->
</p>




</div>

<div id="step2" style="display:none">
<p>
Naujienų grupės:

<div class="marginleft25">
{foreach $options.groups as $id => $item}
	<input type="checkbox" name="groups[]" value="{$id}" checked="checked" /> {$item} <br />
{/foreach}
</div>




<p>
	
</p>
</div>

</form>

</div>