{if $app->user}
<!--User dropdown-->
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<li id="dropdown-user" class="dropdown">
	<a href="#" data-toggle="dropdown" class="dropdown-toggle text-right">
		<span class="pull-right">
			<!-- You may use image instead of an icon.
			<!--<img class="img-circle img-user media-object" src="static/img/av1.png" alt="Profile Picture">-->
			{if $app->user->image}
				<img 
					src="{$app->sys_base}tools/imga/{$app->user->image->id}?size=28x28&method=crop" 
					 class="img-circle blured gw-do-user-img"
					 
					 >
			{else}
				<i class="fa fa-user-circle-o ic-user"></i>
			{/if}
		</span>
		<div class="username hidden-xs">{$app->user->title|default:$app->user->username} </div>
	</a>


	<div class="dropdown-menu dropdown-menu-md dropdown-menu-right panel-default">

		{*
		<!-- Dropdown heading  -->
		<div class="pad-all bord-btm">
		<p class="text-main mar-btm"><span class="text-bold">750GB</span> of 1,000GB Used</p>
		<div class="progress progress-sm">
		<div class="progress-bar" style="width: 70%;">
		<span class="sr-only">70%</span>
		</div>
		</div>
		</div>
		*}

		
		<!-- User dropdown menu -->
		<ul class="head-list">
			<li>
				<a href="{$app->buildUri('users/profile')}">
					<i class="ti-user icon-fw icon-lg"></i> {GW::l('/M/USERS/PROFILE')}
				</a>
			</li>
			<li>
				{$new_messages=$app->user->countNewMessages()}
				<a href="{$app->buildUri('users/messages')}">
					{if $new_messages}<span class="badge badge-danger pull-right">{$new_messages}</span>{/if}
					<i class="ti-email icon-fw icon-lg"></i> {GW::l('/M/USERS/MESSAGES')}
				</a>
			</li>
			
			
			{if $app->auth->isUserSwitched()}
			<li>
				<a href="{$app->buildUri('users/profile',[act=>doSwitchUserReturn])}">
					<i class="ti-angle-double-left icon-fw icon-lg" style='font-size:20px'></i> {$usrret=$app->user->find(['id=?',$app->auth->getOrigUser()])} {sprintf(GW::l('/g/SWITCH_USER_RETURN'),"<b>`$usrret->title`</b>")}
				</a>
			</li>
			{/if}
			
			{*
			<li>
				<a href="#">
					<span class="label label-success pull-right">New</span>
					<i class="ti-settings icon-fw icon-lg"></i> Settings
				</a>
			</li>
			*}
		</ul>

		<!-- Dropdown footer -->
		<div class="pad-all text-right">
			<a href="{$app->buildUri('users/login/logout')}" class="btn btn-primary">
				<i class="ti-unlock icon-fw"></i> {GW::l('/g/LOGOUT')}
			</a>
		</div>
	</div>
</li>
{/if}
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<!--End user dropdown-->