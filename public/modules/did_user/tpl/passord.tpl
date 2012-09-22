{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}



<div class="warp">

<!--bilde hovedside-->
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">
	
	<div class="overskrift">Passord Endring</div>
	{include file="messages.tpl"}<br />
	
	<div class="content_registrering">
	<form method="post" action="">	 
		<input type="hidden" name="act" value="do:changePass" />
		<fieldset>
		<legend>Endre passord</legend>
		    <p {if $itm['old_pass']}class="feil"{/if}><label>Gammel passord:</label><input class="registrering_tekstfelt" name="old_pass" type="password" /></p>
		    <p {if $itm['pass']}class="feil"{/if}><label>Ny passord:</label><input class="registrering_tekstfelt" name="pass" type="password" /></p>
			<p {if $itm['pass2']}class="feil"{/if}><label>Gjenta ny passord:</label><input class="registrering_tekstfelt" name="pass2" type="password" /></p>	 
		</fieldset>
		    <p>
		    <input id="signin_submit" value="Lagre" tabindex="6" type="submit">
		    </p>
		    <br/>
	</form>  
	</div>
	</div>
	
	<div class="contentbg_bot"></div>
	
</div>
{include file="footer.tpl"}