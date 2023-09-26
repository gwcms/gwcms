
{function "bool_setting"}
	{if $title}<span class="noprint settlab">{$title}:</span>{/if} 
	{if $smarty.get.$key}
		<a  class="noprint btnsm" href="{$app->buildUri(false,[$key=>null]+$smarty.get)}">Taip</a> 
	{else} 
		<a  class="noprint btnsm" href="{$app->buildUri(false,[$key=>1]+$smarty.get)}">Ne</a> 
	{/if}
	&nbsp;&nbsp;&nbsp;
	{$GLOBALS.pgsett.$key=$smarty.get.$key|default:$default}
{/function}

{function "opt_setting"}
	{$GLOBALS.pgsett.$key=$smarty.get.$key|default:$default}
	<span class="settlab">{$title}:</span> 
		
		<select onchange="gw_navigator.jump(false,{ '{$key}':this.value })"  style="width:80px">
			<option> --- </option>
			{*  values=$options output=$options *}
			{html_options  selected=$smarty.get.$key options=$options}
		</select>
	

	&nbsp;&nbsp;&nbsp;
{/function}

{function "txt_setting"}
	{$GLOBALS.pgsett.$key=$smarty.get.$key|default:$default}
	<span class="settlab">{$title}:</span> 
		
		<input onchange="gw_navigator.jump(false,{ '{$key}':this.value })"  style="width:80px">
	
	&nbsp;&nbsp;&nbsp;
{/function}

{function "int_setting"}
    {$GLOBALS.pgsett.$key=$smarty.get.$key|default:$default}
    <span class="settlab">{$title}:</span> <a class="btnsm" href="{$app->buildUri(false,[$key=>$GLOBALS.pgsett.$key-1]+$smarty.get)}">-</a> {$GLOBALS.pgsett.$key} <a  class="btnsm" href="{$app->buildUri(false,[$key=>$GLOBALS.pgsett.$key+1]+$smarty.get)}">+</a>
    &nbsp;&nbsp;&nbsp;
{/function}


{function "page_settings"}
	{call "int_setting" title="marginLeft" key=marginLeft default=30}  
	{call "int_setting" title="marginRight" key=marginRight default=30} 
	{call "int_setting" title="pageFont" key=pageFont default=10} 
	{call "int_setting" title="clean" key=clean} 
{/function}


{function "conf_open"}
	<div class="noprint pagesettblo" {if $smarty.get.nosettings}style="display:none"{/if}>
    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
    <span>
{/function}

{function "conf_close"}
	</div>
{/function}


<style>
	
	.pagebreak_explain{ padding: 5px; background-color:#ddd; text-align:center;}  	
	
	.page {
		background-color:white;
		position: relative;
		width: 20.9cm;
		height:29.7cm;
		border-bottom:10px solid silver;
		border-right:10px solid silver;
	}    
	
	@media print {
	    .page { 
		    page-break-after: always; 
		    text-aling:center; 
		    border-bottom:0px; 
		    border-right:0px;
	    }
	    .notprintable { display: none; }
	    .pagebreak { page-break-after: always; }
	    .noprint{ display:none; }        
	}
	.pagebreak_explain{ padding: 5px; background-color:#ddd; text-align:center;}        


	body{ padding: 0;margin:0; }
	.settlab{ color:gray; }
	.btnsm{ padding: 2px 5px 2px 5px; border-radius:50%; background-color:#ddd;}	



	.pagesettblo { 
		font-size: 12px;		
		padding:5px;background-color:#f6eee4;margin-bottom:10px;
	}
	.settlab, .pagesettblo, .pagesettblo span, .pagesettblo a{ font-size: 12px;  }
</style>