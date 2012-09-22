<div class="toplinebg">
		
		<div id="topnav" class="topline">
			{if !GW::$user}
			<div style="float:left;"><a href="{$request->ln}/registrer"> Registrer ny bruker!</a>
			{else}
			<div style="float:left;"> Du er nå logget inn som: {GW::$user->first_name} {GW::$user->second_name} <a href=" ?pre_act=do_logout">Logg ut</a>
			{/if}
			</div>
    		<div>
    		<a href="{$request->ln}/contacts" class="signout"><span>Kontakt oss</span></a>
    		{if !GW::$user}
    			<a href="{$request->ln}/registrer" class="signout"><span>Registrer</span></a>
				<a href="login" class="signin"><span>Logg inn</span></a>
				&nbsp;&nbsp;&nbsp;
				<a href="en/{$request->path}" style="float:right;"><img src="images/flagg_eng.gif" alt="English" title="English"></a>
				<a href="no/{$request->path}" style="float:right;"><img src="images/flagg_nor.gif" alt="Norsk" title="Norsk">&nbsp;</a>
				
    		</div>
			
				<fieldset id="signin_menu">
				{include file="login_form.tpl"}
				</fieldset>
			{else}
				<a href="mysite" class="mysite"><span>Min konto</span></a>
				<a href="{$request->ln}/{$request->path}?pre_act=do_logout" class="signout"><span>Logg ut</span></a>
				&nbsp;&nbsp;&nbsp;
				<a href="en/{$request->path}" style="float:right;"><img src="images/flagg_eng.gif" alt="English" title="English"></a>
				<a href="no/{$request->path}" style="float:right;"><img src="images/flagg_nor.gif" alt="Norsk" title="Norsk">&nbsp;</a>
			</div>
			{/if}

			<fieldset id="mysite_menu">
			<form method="post" id="mysite" action="">
			<br />
			<table width="300" border="0" cellspacing="5">
				<tr align="center">
					<td width="50"><a href="{$request->ln}/bruker"><img src="images/ordrer_icon.png" alt="test" /></a></td>
					<td width="50"><a href="{$request->ln}/bruker/orders"><img src="images/knappetest.png" alt="test" /></a></td>
					<td width="50"><a href="{$request->ln}/bruker/innstillinger"><img src="images/innstillinger_icon.png" alt="test" /></a></td>
					<td width="50"><a href="{$request->ln}/bruker/passord"><img src="images/passord.png" alt="test" /></a></td>
					
				</tr>
				<tr align="center">
					
					<td width="50"><a href="{$request->ln}/bruker">Oversikt</a></td>
					<td width="50"><a href="{$request->ln}/bruker/orders">Ordrer</a></td>
					<td width="50"><a href="{$request->ln}/bruker/innstillinger">Innstillinger</a></td>
					<td width="50"><a href="{$request->ln}/bruker/passord">Endre Passord</a></td>
				</tr>
			</table>
			</form>
			</fieldset>
		</div>
	</div>
	<div class="testside"></div>
	<div class="testside2"></div>
	<div class="header">
		<div class="logo"><a href="{$request->ln}"><img src="tools/img.php?id=072650d56fbe4e61419c639251264759"/></a></div>
	</div>