{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}

<div class="warp">
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">
	<div class="overskrift">Oversikt</div>
	{include file="messages.tpl"}<br />
		<div class="content_user">
			<fieldset>
			<legend>Bruker info</legend>
				Din bruker ID er: <b>{$user->id}</b><br>
				Du vart registrert <b>{$user->insert_time|date_format:"%e.%m.%y kl. %H:%M"}</b> med e-postadressen: <b>{$user->email}</b><br>
			</fieldset>
			<br><br>
			<fieldset>
			<legend>Leverings info</legend>
			Fornavn: <b>{$user->first_name}</b><br>
			Etternavn: <b>{$user->second_name}</b><br>
			Kontakt Telefon: <b>{$user->phone}</b><br>
			Mobil Telefon: <b>{$user->mob_phone}</b><br>
			<br>
			Adressen: <b>{$user->address}</b><br>
			Post nummer: <b>{$user->post_index}</b><br>
			By / Sted: <b>{$user->city}</b><br>
			Land: <b>Norge</b><br>
			Motta Nyhetsbrev: <b>{if $user->news}Ja{else}Nei{/if}</b>
			<br><br>
			<a href="{$request->ln}/bruker/innstillinger">Endre info</a>&nbsp;&nbsp;
			<a href="{$request->ln}/bruker/passord">Endre passord</a>			
			</fieldset>
			<br><br>
			
			<fieldset>
			<legend>Ordre info</legend>
			Det ligger <b>{$miniCartInfo['nr_items']} varer</b> fra <b>{$miniCartInfo['nr_products']} design</b> i handlekurven, siste vart lagt til <b>{$miniCartInfo['nr_products']|date_format:"%e.%m.%y kl. %H:%M"}</b>.
			<br><br>
			Du har <b>totalt {$order_info['total']}</b> bestillinger. <br>
			<b>{$order_info['payed']}</b> som er <b>betalt</b> og i produksjon.<br>
			<b>{$order_info['ordered']}</b> som <b>venter</b> på transaksjon.<br>
			<b>{$order_info['sent']}</b> som er <b>ferdig</b> og sendt.<br>
			Til sammen har du bestilt x invitasjoner og y bordkort.
			<br><br>
			<a href="{$request->ln}/bruker/orders">Se på Ordrer</a>
			</fieldset>
			<br><br>
			<fieldset>
			<legend>Annet Info</legend>
			Siste nyhetsbrev fra dropin kom 00.00.00 og kan sees her.
			<br><br>
			Du har lagt til 0 design som favoritter.<br>
			(vis bilde av favorittene her)

			</fieldset>
		</div>
	</div>
	<div class="contentbg_bot"></div>
</div>

{include file="footer.tpl"}