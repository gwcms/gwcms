{include "default_open.tpl"}

{function display_cust_ico}
		{if $item->printprofilefoto}
			{$image=$item->printprofilefoto}			
			{$profile_image_url="{$app->sys_base}tools/img/{$image->key}?size=28x28&{$item->getZoomOffsetArg()}&method=crop"}				
			
			<img src="{$profile_image_url}" class="img-circle blured gw-do-user-img profileico" >
		{else}
			<i class="fa fa-user-circle-o profileico" aria-hidden="true" style="font-size: 28px;color:silver"></i>
		{/if}
{/function}


{if $original_user}
	<a title="" href="{$app->buildUri(false, [act=>doReturnToOriginal])}" class="btn btn-sm btn-default">
		<i class="fa fa-undo" aria-hidden="true"></i>
		{GW::ln('/m/RETURN_TO_ORIGINAL_USER')}
		&nbsp;&nbsp; {call "display_cust_ico" item=$original_user}
		{$original_user->title}
	</a>	
{/if}



<table class="accountmenu">
{foreach $list as $item}
	<tr>
		<td>{call "display_cust_ico"} {$item->title}</td>
		<td>
			<a title="{GW::ln('/m/LOGIN')}" href="{$app->buildUri(false, [act=>doLoginAs,id=>$item->id])}" class="btn btn-sm btn-default"><i class='fa fa-sign-in'></i></a>
		</td>
	</tr>
{/foreach}
</table>

<br><br>

<style>
	.accountmenu li { margin-top:4px; }
	.profileico{ margin-right:5px; }
	.accountmenu td { padding: 2px 10px 2px 10px }
</style>

{include "default_close.tpl"}