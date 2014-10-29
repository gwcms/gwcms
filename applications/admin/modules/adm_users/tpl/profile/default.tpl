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


<tr><td></td><td><input type="submit" value="{$lang.SAVE}"/></td></tr>
</table>

</form>

<h3>{$m->lang.CHANGE_PASS}</h3>

<form action="{$smarty.server.REQUEST_URI}" method="post">

<input type="hidden" name="act" value="do:update_my_pass" />

<table class="gwTable">



{include file="elements/input.tpl" type="password" name=pass_old}
{include file="elements/input.tpl" type="password" name=pass_new}
{include file="elements/input.tpl" type="password" name=pass_new_repeat}


<tr><td></td><td><input type="submit" value="{$lang.SAVE}"/></td></tr>
</table>

</form>


{include file="extra_info.tpl" exta_fields=['id','login_time','login_count','insert_time','update_time']}


</div>

{include file="default_close.tpl"}