{include file="default_open.tpl"}

<div style="max-width:600px;">


{$labels=$m->lang.FIELDS}
{$width_title="30%"}


<form action="{$smarty.server.REQUEST_URI}" method="post">

<input type="hidden" name="act" value="do:update_my_profile" />

<h3>{$m->lang.CHANGE_PROFILE_DATA}</h3>

<table class="gwTable">


{include file="elements/input.tpl" name=email}
{include file="elements/input.tpl" name=name}
{include file="elements/input.tpl" name=surname}


<tr><td></td><td><input class="btn btn-primary" type="submit" value="{$lang.SAVE}"/></td></tr>
</table>

</form>

<h3>{$m->lang.CHANGE_PASS}</h3>

<form action="{$smarty.server.REQUEST_URI}" method="post">

<input  type="hidden" name="act" value="do:update_my_pass" />

<table class="gwTable">



{include file="elements/input.tpl" type="password" name=pass_old}
{include file="elements/input.tpl" type="password" name=pass_new}
{include file="elements/input.tpl" type="password" name=pass_new_repeat}


<tr><td></td><td><input class="btn btn-primary" type="submit" value="{$lang.SAVE}"/></td></tr>
</table>

</form>





<script>

$(document).ready(function(){
	GW_SW.initBtns();
	
});
</script>

<br />
<button id="subscribe_btn" data-enable="Enable push messages" data-disable="Disable push messages" style="display:none">Push messages ...</button>
<button id="test_subscribe_btn" style="display:none" onclick="$.ajax('{$m->buildUri(false,[act=>doTestSubscription])}')">Test push message</button>
<br /><br />


{include file="extra_info.tpl" exta_fields=['id','login_time','login_count','insert_time','update_time']}


</div>

{include file="default_close.tpl"}