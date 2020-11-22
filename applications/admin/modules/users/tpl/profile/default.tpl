{include file="default_open.tpl"}

<div style="max-width:600px;">


{$labels=GW::l('/m/FIELDS')}
{$width_title="30%"}


<form action="{$smarty.server.REQUEST_URI}" method="post">

<input type="hidden" name="act" value="do:update_my_profile" />

<h3>{GW::l('/m/CHANGE_PROFILE_DATA')}</h3>

<table class="gwTable">

{include "elements/input_func.tpl"}
{call e field=email}
{call e field=name}
{call e field=surname}


<tr><td></td><td><input class="btn btn-primary" type="submit" value="{GW::l('/g/SAVE')}"/></td></tr>
</table>

</form>

<h3>{GW::l('/m/CHANGE_PASS')}</h3>

<form action="{$smarty.server.REQUEST_URI}" method="post">

<input  type="hidden" name="act" value="do:update_my_pass" />

<table class="gwTable">



{call e field=pass_old type="password"}
{call e field=pass_new type="password"}
{call e field=pass_new_repeat type="password"}


<tr><td></td><td><input class="btn btn-primary" type="submit" value="{GW::l('/g/SAVE')}"/></td></tr>
</table>

</form>





<script>
require(['gwcms'], function(){
	GW_SW.initBtns();
	

})

</script>

<br />
<button id="subscribe_btn" data-enable="Enable push messages" data-disable="Disable push messages" style="display:none">Push messages ...</button>
<button id="test_subscribe_btn" style="display:none" onclick="$.ajax('{$m->buildUri(false,[act=>doTestSubscription])}')">Test push message</button>
<br /><br />



{if !$app->user->ext->adminfbid}
	<a class="btn btn-primary" href="{$m->buildUri(false,[act=>doLinkWithFb])}">Link With Facebook account</a>
{else}
	{if $app->user->ext->adminfbid}<img src="https://graph.facebook.com/{$app->user->ext->adminfbid}/picture?type=small" style="border-radius: 50%;height:30px;" class="mx-1">{/if}
	
	<a class="btn btn-primary" href="{$m->buildUri(false,[act=>doUnLinkWithFb])}">Unlink Facebook account</a>
	
{/if}


{include file="extra_info.tpl" exta_fields=['id','login_time','login_count','insert_time','update_time']}



</div>

{include file="default_close.tpl"}