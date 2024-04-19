{include "default_open.tpl"}
{include "messages.tpl"}
<section class="container  g-pt-20 g-pb-80">


<div class="row justify-content-md-center">		
                  <div class="col-md-6">

<form action="{$smarty.server.REQUEST_URI}" method="post" id="passchange_new">
	<input type="hidden" name="act" value="do:PassChange2">
	<table class='resetform'>
		<tr>
			<td>{GW::ln('/m/NEW_PASSWORD')}</td>
			<td><input class="form-control"  type="password" name="login_id[]" /></td>	
		<tr>
		<tr>
			<td>{GW::ln('/m/PLEASE_REPEAT_PASSWORD')}</td>
			<td><input class="form-control"  type="password" name="login_id[]" /></td>	
		<tr>
		
		<tr>
			<td></td>
			<td><input type="submit" class="btn btn-ar btn-primary pull-right"  /></td>	
		<tr>
			
				
	</table>
	
	
</form>

	</div></div>
</section>
	
<style>		
	.resetform td{ padding:3px; }
</style>			

{include "default_close.tpl"}