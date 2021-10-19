{$sitedomain} vartotojas <b>{$usertitle}</b><br>
Siunčia užsakymo apmokėjimo nuorodą <a href='{$pay_link}'>{$pay_link}</a><br>
Paspaudę nuorodą galite pasirinkti savo banką.<br><br>

Sąskaita faktūrą galite  <a href='{$view_invoice_link}'>atsisiųsti</a> arba rasti prisegtą šiame laiške<br>	    
<br>

Užsakymo informacija: 
<hr> 
Užsakymo id: <b>#{$order->id}</b>
{$orderinfo}
<hr>

{if $order->get('keyval/otherpayee_msg')}
	<b>{$usertitle}</b> žinutė:<br>
	<i>{$order->get('keyval/otherpayee_msg')}</i>
	<hr>
{/if}



Mokėjimo autentiškumą galite patikrinti jei paspaudę nuorodą po nukreipimo<br>
    url adrese matysite https://bank.paysera.com/...<br>
    Pardavėjas skiltyje matysite {$sitedomain}, {$sitetitle}<br>
