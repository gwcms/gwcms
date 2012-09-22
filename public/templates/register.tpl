{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}

<div class="warp">

<!--bilde hovedside-->
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">
	<div class="content_registrering">
	
	{if isset($smarty.request['act'])}
		{$itm['error'] = 0}
		
		{if $smarty.request['act'] == "do_register"}
		
			{if $smarty.request['item']['phone'] == ""}
					{$itm['error'] = 1}
					{$itm['phone'] = 1}
					{$itm['message'] = 'Mangler Telefon.'}
			{/if}
			{if $smarty.request['item']['email'] == ""}
					{$itm['error'] = 1}
					{$itm['email'] = 1}
					{$itm['message'] = 'Mangler E-post.'}
			{/if}
			{if $smarty.request['item']['second_name'] == ""}
					{$itm['error'] = 1}
					{$itm['second_name'] = 1}
					{$itm['message'] = 'Mangler Etternavn.'}
			{/if}
		
			{if $smarty.request['item']['first_name'] == ""}
					{$itm['error'] = 1}
					{$itm['first_name'] = 1}
					{$itm['message'] = 'Mangler Fornavn.'}
			{/if}
		
			{if $smarty.request['land'] != "Norge"}
					{$itm['error'] = 1}
					{$itm['land'] = 1}
					{$itm['message'] = 'Vi aksepterer kunder kunn fra Norge.'}
			{/if}
			{if strlen($smarty.request['pass2']) <= 3}
					{$itm['error'] = 1}
					{$itm['pass'] = 1}
					{$itm['message'] = 'Pasord er for kort.'}
			{/if}
			{if strlen($smarty.request['pass2']) > 16}
					{$itm['error'] = 1}
					{$itm['pass'] = 1}
					{$itm['message'] = 'Pasord er for lang.'}
			{/if}
			{if $smarty.request['item']['pass'] != $smarty.request['pass2']}
					{$itm['error'] = 1}
					{$itm['pass'] = 1}
					{$itm['message'] = 'Pasordene er ikke like.'}
			{/if}
			{if !$smarty.request['license']}
				{$itm['error'] = 1}
				{$itm['license'] = 1}
				{$itm['message'] = 'Du må akseptere vilkårene.'}
			{/if}
			
			{php}
				//check if email is inuse
				include_once GW::$dir['MODULES'].'/customers/gw_user.class.php';
				$data = new GW_User();
				//todo escape
				$user = $data->getByUsername($_REQUEST['item']['email']);
				GW::$smarty->assign('user', $user);
			{/php}
			{dump($user->email)}
			{if $user->email == $smarty.request['item']['email']}
				{$itm['error'] = 1}
				{$itm['email'] = 1}
				{$itm['message'] = 'Denne email er alerede i bruk.'}
			{/if}
			
		{else}
			{$itm['error'] = 1}
			{$itm['message'] = 'unknown command'}
		{/if}
	{/if}
		
	{if isset($itm) && $itm['error'] == 0}
		{php}
				//create user
				include_once GW::$dir['MODULES'].'/customers/gw_user.class.php';
				$data = new GW_User();
				//todo escape
				$success = $data->createNewUser();
				GW::$smarty->assign('success', $success);
		{/php}
		{if $success}
			Brukernavn var opprettet!
		{else}
			Feil under oppretning. Kontakt administrator!
		{/if}
	{else}
		
    
		    <form method="post" action="no/registrer">		
		    <input type="hidden" name="act" value="do_register" />
		    {if $itm['message']}
		    <div class="tekstlink"><h2><p class="feil">
		    	{$itm['message']}
		    </p>
		    </h2>
		    </div>
		    {/if}
		    <h1>Registrer ny bruker</h1>
		    <br />
		    <h2>Personlige opplysninger</h2><br />
		
		<fieldset>
		    <p {if $itm['first_name']}class="feil"{/if}><label for="name">Fornavn:</label><input class="registrering_tekstfelt" name="item[first_name]" type="text" value="{$smarty.request['item']['first_name']}"/></p>
		    <p {if $itm['second_name']}class="feil"{/if}><label for="name">Etternavn:</label><input class="registrering_tekstfelt" name="item[second_name]" type="text" value="{$smarty.request['item']['second_name']}"/></p>
		    <p {if $itm['email']}class="feil"{/if}><label for="name">E-post:</label><input class="registrering_tekstfelt" name="item[email]" type="text" value="{$smarty.request['item']['email']}"/></p>
		<p {if $itm['phone']}class="feil"{/if}><label for="name">Telefon:</label><input class="registrering_tekstfelt" name="item[phone]" type="text" value="{$smarty.request['item']['phone']}"/></p>
		    <p {if $itm['pass']}class="feil"{/if}><label for="name">Passord:</label><input class="registrering_tekstfelt" name="item[pass]" type="password" value="{$smarty.request['item']['pass']}"/></p>
		    <p {if $itm['pass']}class="feil"{/if}><label for="name">Gjenta passord:</label><input class="registrering_tekstfelt" name="pass2" type="password" value="{$smarty.request['pass2']}"/></p> 
		</fieldset>
		
		    <br/>
		    <h2>Leveringsadresse</h2><br />
		<fieldset>
		    <p  {if $itm['address']}class="feil"{/if}><label for="name">Adresse:</label><input class="registrering_tekstfelt" name="item[address]" type="text" value="{$smarty.request['item']['address']}"/></p>
		    <p {if $itm['post_index']}class="feil"{/if}><label for="name">Postnr:</label><input class="registrering_tekstfelt" name="item[post_index]" type="text" value="{$smarty.request['item']['post_index']}"/></p>
		    <p {if $itm['city']}class="feil"{/if}><label for="name">Poststed:</label><input class="registrering_tekstfelt" name="item[city]" type="text" value="{$smarty.request['item']['city']}"/></p>
		    <p {if $itm['land']}class="feil"{/if}><label for="name">Land:</label><input class="registrering_tekstfelt" name="land" type="text" readonly="readonly" value="Norge" /></p>
		</fieldset>
		            
		    <div class="tekstlink"><h4><input type="checkbox" name="license" />Ja, jeg godtar<a href="#"> våre kjøpebetingelser.</a> {if $itm['license']}<p class="feil">Du må akseptere vilkårene.</p>{/if}</h4></div>
		    <h4><input type="checkbox" name="item[news]" 
		    {if isset($smarty.request['itm'])}
		    	{if $smarty.request['item']['news'] == 'on'}
		    		checked="checked"
		    	{else}
		    	{/if}
		    {else}
		    	checked="checked"
		    {/if}/>Ja, jeg ønsker å motta nyhetsbrev per e-post. </h4>
		    <p>
		    <input id="signin_submit" value="Registrer" tabindex="6" type="submit">
		    </p>
		    <br/>
		    </form>
		    
		</div>
	{/if}
	
	</div>
	
	<div class="contentbg_bot"></div>
	
</div>
{include file="footer.tpl"}