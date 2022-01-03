

{assign var=itax_short_names scope=global value=['client'=>'C','purchase'=>'P','invoice'=>'I']}
{assign var=itax_status_color scope=global value=[0=>red,6=>red,7=>green,5=>orange,8=>violet]}
{assign var=itax_status_title scope=global value=[0=>"Hmmm",6=>"Klaida kuriant",7=>"Sukurta",5=>"Negalimas sukurti",8=>"Pašalintas"]}
{assign var=itax_links scope=global value=["purchase"=>"https://www.itax.lt/purchases/", "client"=>"https://www.itax.lt/clients/","invoice"=>"https://www.itax.lt/invoices/"]}

{capture append="footer_hidden"}
	<style>
		.itaxbadge{ 
			color:white !important;
			display: inline-block; 
			min-width: 10px; 
			padding: 3px 6px; 
			border-radius:10px; 
			font-size: .9em;
			 font-weight: 600;
			text-align: center;
			white-space: nowrap;
			vertical-align: middle;
			line-height: 1;
		}
		.itaxbadge:hover{
			background-color: silver !important; 
			color:black !important;
		} 
		
		.itax_stat_0{ background-color: red; }
		.itax_stat_5{ background-color: orange; color:white !important;}
		.itax_stat_5:hover{ background-color: orange !important; color:white !important; }
		.itax_stat_6{ background-color: red; }
		.itax_stat_7{ background-color: green; }
		.itax_stat_8{ background-color: violet; }
	</style>
{/capture}

{function "itax_status"}
<a class="iframeopen" href="{$m->buildUri('itaxnotes',['id'=>$item->id,'clean'=>2])}">
		{$stat=json_decode($item->itax_status_ex, true)}
		{foreach $stat as $longname => $status}
				<a class="itax_stat_{$status} itaxbadge " {if $status==7}href="{$itax_links[$longname]}{$item->get("extra/itax_`$longname`_id")}" target='_blank'{elseif $status==6}href="{$m->buildUri(false,[act=>doItaxShowFailDetails,id=>$item->id,which=>$longname])}"{/if} 
				   title='{$longname}: {$itax_status_title[$status]|default:$status}'
				   >
			{if isset($itax_short_names[$longname])}{$itax_short_names[$longname]}{else}{$longname}{/if}
				</a>
		{/foreach}
</a>
{/function}
