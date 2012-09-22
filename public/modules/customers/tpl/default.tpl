{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}



<div class="warp">

<!--bilde hovedside-->
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">
	
	<div class="overskrift">Registrer ny bruker</div>
	{include file="messages.tpl"}
	
	{if $success || $smarty.request['success']}
	<div class="content">
		<h2>Sjekk {$user->email} for aktiverings brev...</h2><br>
		<form method="post" action="{$request->ln}/registrer/{$user->id}/activate" style="margin-bottom:0px;">
			<h3>Type inn activation code: <input class="registrering_tekstfelt" name="key" type="text" />&nbsp;<input id="signin_submit" value="Submit" type="submit"></h3>
		</form>
	{else}
	<div class="content_registrering">
	<p><h4>Felter markert med <font color="red">*</font> må fylles ut.</h4></p>
	<form method="post" action="no/registrer">	 
		<input type="hidden" name="act" value="do_register" />   
		<div class="registrering_fordeler">
			<fieldset>
			<legend>{$m->lang.cutomer_benefits}</legend>
			<p><label><img alt="" src="tools/img.php?id=9e27e3522a124cb37995444287131a65"> {$m->lang.free_to_register}</label>&nbsp;</p>
			<p><label><img alt="" src="tools/img.php?id=9e27e3522a124cb37995444287131a65"> {$m->lang.no_delivery_costs}</label>&nbsp;</p>
			<p><label><img alt="" src="tools/img.php?id=9e27e3522a124cb37995444287131a65"> {$m->lang.easy_to_create}</label>&nbsp;</p>
			<p><label><img alt="" src="tools/img.php?id=9e27e3522a124cb37995444287131a65"> {$m->lang.quality_assurance}</label>&nbsp;</p>
			<p><label><img alt="" src="tools/img.php?id=9e27e3522a124cb37995444287131a65"> {$m->lang.orders_view}</label>&nbsp;</p>
			<p><label><img alt="" src="tools/img.php?id=9e27e3522a124cb37995444287131a65"> {$m->lang.save_to_shoppingcart}</label>&nbsp;</p>
			
			
			</fieldset>
		</div>
		<fieldset>
		<legend>{$m->lang.personal_info}</legend>
			
		    <p {if $itm['first_name']}class="feil"{/if}><label>{$m->lang.first_name}:</label><input class="registrering_tekstfelt" name="item[first_name]" type="text" value="{$smarty.request['item']['first_name']}"/><font color="red" size="-1">*</font></p>
		    <p {if $itm['second_name']}class="feil"{/if}><label>{$m->lang.second_name}:</label><input class="registrering_tekstfelt" name="item[second_name]" type="text" value="{$smarty.request['item']['second_name']}"/><font color="red" size="-1">*</font></p>
		    <p {if $itm['email']}class="feil"{/if}><label>{$m->lang.email}:</label><input class="registrering_tekstfelt" name="item[email]" type="text" value="{$smarty.request['item']['email']}"/><font color="red" size="-1">*</font></p>
			<p {if $itm['phone']}class="feil"{/if}><label>{$m->lang.phone}:</label><input class="registrering_tekstfelt" name="item[phone]" type="text" value="{$smarty.request['item']['phone']}"/><font color="red" size="-1">*</font></p>
			<p {if $itm['mob_phone']}class="feil"{/if}><label>{$m->lang.mob_phone}:</label><input class="registrering_tekstfelt" name="item[mob_phone]" type="text" value="{$smarty.request['item']['mob_phone']}"/></p>
		    <p {if $itm['pass']}class="feil"{/if}><label>{$m->lang.pass}:</label><input class="registrering_tekstfelt" name="item[pass]" type="password" value="{$smarty.request['item']['pass']}"/><font color="red" size="-1">*</font></p>
		    <p {if $itm['pass']}class="feil"{/if}><label>{$m->lang.pass2}:</label><input class="registrering_tekstfelt" name="pass2" type="password" value="{$smarty.request['pass2']}"/><font color="red" size="-1">*</font></p>	 
		</fieldset>
		
		    <br/>
		    
		<fieldset>
		<legend>{$m->lang.delivery_address}</legend>
		    <p  {if $itm['address']}class="feil"{/if}><label for="name">{$m->lang.address}:</label><input class="registrering_tekstfelt" name="item[address]" type="text" value="{$smarty.request['item']['address']}"/></p>
		    <p {if $itm['post_index']}class="feil"{/if}><label for="name">{$m->lang.post_index}:</label><input class="registrering_tekstfelt" name="item[post_index]" type="text" value="{$smarty.request['item']['post_index']}"/></p>
		    <p {if $itm['city']}class="feil"{/if}><label for="name">{$m->lang.city}:</label><input class="registrering_tekstfelt" name="item[city]" type="text" value="{$smarty.request['item']['city']}"/></p>
		    <p {if $itm['land']}class="feil"{/if}><label for="name">{$m->lang.country}:</label><input class="registrering_tekstfelt" name="land" type="text" readonly="readonly" value="Norge" /></p>
		</fieldset>
		            
		    <div class="tekstlink"><h4><input type="checkbox" name="license" /><font color="red">*</font> Ja, jeg godtar<a href="#"> våre kjøpebetingelser.</a> {if $itm['license']}<p class="feil">Du må akseptere vilkårene.</p>{/if}</h4></div>
		    <h4><input type="checkbox" name="item[news]" 
		    {if isset($smarty.request['itm'])}
		    	{if $smarty.request['item']['news'] == 'on'}
		    		checked="checked"
		    	{else}
		    	{/if}
		    {else}
		    	checked="checked"
		    {/if}/> Ja, jeg ønsker å motta nyhetsbrev per e-post. </h4>
		    <p>
		    <input id="signin_submit" value="Registrer" tabindex="6" type="submit">
		    </p>
		    <br/>
	</form>
	
	{/if}    
	</div>
	</div>
	
	<div class="contentbg_bot"></div>
	
</div>
{include file="footer.tpl"}