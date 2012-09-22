{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}

{function gw_format_time time="0000-00-00 00:00:00" time_format="%e.%m.%y kl. %H:%M"}
	{if $time != "0000-00-00 00:00:00"}
		{$time|date_format:$time_format}
	{/if}
{/function}

<div class="warp">
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">
	<div class="overskrift">Mine ordrer</div>
	{include file="messages.tpl"}<br />
		<div class="content_ordre">
		

<h3>
<table width="900" border="0" cellpadding-top="1" cellspacing="0">
  <tr align="center" >
    <th height="30px" width="100" align="left">Ordrenr.</th>
    <th width="100" align="left">Pris</th>
    <th width="150">Bestilt</th>
    <th width="150">Betalt</th>
    <th width="150">Produsert</th>
    <th width="150">Sendt</th>
    <th width="200"></th>
  </tr>
  {foreach from=$ordersList item=item}
  {$s = $item->status}
  <tr align="center">
    <td height="25px" align="left">{$item->id}</td>
    <td align="left">{$item->total_cost} kr</td>
    
    {*
    	<i id="ORDER_STATES_OPT">
		<i id="5">Canceled</i>
		<i id="10">Ordered</i>		
		<i id="20">Payed</i>
		<i id="25">Processing</i>
		<i id="30">Processed</i>
		<i id="40">Sent</i>
	</i>
    *}
    
    {if $s==5}
    	{for $i=0;$i<4;$i++}
    		<td class="ordre_slettet"></td>
    	{/for}
    {else}
    
	    <td class="ordre_aktivert">
	    	{gw_format_time time=$item->insert_time}
	    </td>
	    
	    <td class="ordre_{if $s>=20}aktivert{elseif $s>=10 && $s<20}transaksjon{else}deaktivert{/if}">
	    	{gw_format_time time=$item->pay_time}
	    	{if $s>=10 && $s<20}
	    		Venter på transaksjon
	    	{/if}
	    </td>
	    
	    <td class="ordre_{if $s>=30}aktivert{elseif $s>=20 && $s<30}transaksjon{else}deaktivert{/if}">
	    	{gw_format_time time=$item->processed_time}
	    	{if $s>=20 && $s<30}
	    		I produksjon
	    	{/if}	    	
	    </td>
	    <td class="ordre_{if $s>=40}aktivert{elseif $s>=30 && $s<40}transaksjon{else}deaktivert{/if}">
	    	{gw_format_time time=$item->sent_time}
	    	Klar til sending
	    	{if $s>=30 && $s<40}
	    		Klar til sending
	    	{/if}	    	
	    </td>
    {/if}
    
    {*
    <td class="ordre_{if $s == deleted}slettet">{else}">{/if}</td>
    <td class="ordre_{if $s == deleted}slettet">{elseif $s == 'payed' || $s == 'submited' || $s == 'sent'}aktivert">{gw_format_time time=$item->pay_time}{else}transaksjon">Venter på transaksjon{/if}</td>
    <td class="ordre_{if $s == deleted}slettet">{elseif $s == 'submited' || $s == 'sent'}aktivert">{elseif $s == 'payed'}transaksjon">I produksjon{else}deaktivert">{/if}</td>
    <td class="ordre_{if $s == deleted}slettet">{elseif $s == 'sent'}aktivert">{elseif $s== 'submited'}transaksjon">Klar til sending{else}deaktivert">{/if}</td>
    *}
    
     <td width="200" align="center"><div class="awesome2"><a href="">Se bestilling</a></div></td>
     
  </tr>
  {/foreach}
</table>
</h3>
        
	</div>
		
		
	</div>
	<div class="contentbg_bot"></div>
</div>

{include file="footer.tpl"}