{include "default_open.tpl"}

<section class="container  g-pt-20 g-pb-80">
{$username=$remoteuser->name}
{if $ln=='lt'}
	{$username=GW_Linksniai_Helper::getLinksnis($username)}
{/if}


{GW::ln('/m/HELLO')} <b>{$username}</b>! &nbsp; 

{if $remoteuser->picture}
	<img src="{$remoteuser->picture}" style="border-radius: 30px;">
{else}
	<img src="https://graph.facebook.com/{$remoteuser->id}/picture?type=small" style="border-radius: 30px;">
{/if}

<br /><br />

<form action='{$smarty.server.REQUEST_URI}' method='post' id="takeaction">
	<input type='hidden' name='act' value='doSignupOrLink' />
	<input type='hidden' type='{$remoteuser->type}'>
	<input type='radio' name='action' value='link' id="r1"> <label for="r1">{GW::ln('/m/LINK_WITH_EXISTING')}</label> </br>
	<input type='radio' name='action' value='register' id="r2"> <label for="r2">{GW::ln('/m/REGISTER_FROM_PROVIDED_DATA',[v=>[type=>strtoupper($remoteuser->type)]])} ({GW::ln('/m/FIELDS/name')}: <b>{$remoteuser->title}</b>, {GW::ln('/m/FIELDS/email')}: <b>{$remoteuser->email}</b>)</label></br>
	<input type='radio' name='action' value='register_custom' id="r3"> <label for="r3">{GW::ln('/m/REGISTER_WITH_CUSTOM_DATA')}  </label> </br>

<br />

	<input type='submit' class="btn btn-primary" value="{GW::ln('/g/SUBMIT')}">
</form>
	
	
<style>
	#takeaction label{ font-weight: normal !important; }
</style>

</section>

{include "default_close.tpl"}