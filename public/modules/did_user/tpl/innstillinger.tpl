{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}



<div class="warp">

<!--bilde hovedside-->
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">
	
	<div class="overskrift">Innstillinger</div>
	{include file="messages.tpl"}<br />
	
	<div class="content_registrering">
	<form method="post" action="">	 
		<input type="hidden" name="act" value="do:save" />
		<fieldset>
		<legend>Personlige opplysninger</legend>
		    <p {if $itm['first_name']}class="feil"{/if}><label>Fornavn:</label><input class="registrering_tekstfelt" name="item[first_name]" type="text" value="{$user->first_name}"/></p>
		    <p {if $itm['second_name']}class="feil"{/if}><label>Etternavn:</label><input class="registrering_tekstfelt" name="item[second_name]" type="text" value="{$user->second_name}"/></p>
			<p {if $itm['phone']}class="feil"{/if}><label>Kontakt Telefon:</label><input class="registrering_tekstfelt" name="item[phone]" type="text" value="{$user->phone}"/></p>	 
			<p {if $itm['mob_phone']}class="feil"{/if}><label>Mobil Telefon:</label><input class="registrering_tekstfelt" name="item[mob_phone]" type="text" value="{$user->mob_phone}"/></p>
		</fieldset>
		
		    <br/>
		    
		<fieldset>
		<legend>Leveringsadresse</legend>
		    <p  {if $itm['address']}class="feil"{/if}><label for="name">Adresse:</label><input class="registrering_tekstfelt" name="item[address]" type="text" value="{$user->address}"/></p>
		    <p {if $itm['post_index']}class="feil"{/if}><label for="name">Postnr:</label><input class="registrering_tekstfelt" name="item[post_index]" type="text" value="{$user->post_index}"/></p>
		    <p {if $itm['city']}class="feil"{/if}><label for="name">Poststed:</label><input class="registrering_tekstfelt" name="item[city]" type="text" value="{$user->city}"/></p>
		    <p {if $itm['land']}class="feil"{/if}><label for="name">Land:</label><input class="registrering_tekstfelt" name="land" type="text" readonly="readonly" value="Norge" /></p>
		</fieldset>
		    <h4><input type="checkbox" name="item[news]" {if $user->news} checked="checked"{/if} /> Ja, jeg ønsker å motta nyhetsbrev per e-post. </h4>
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