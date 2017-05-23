{include file="head.tpl"}


<body>
	

	
<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

<body>


<div id="sb-site">
<div class="boxed">

<header id="header-full-top" class="hidden-xs header-full">
    <div class="container">
        <div class="header-full-title">
            <h1 class="animated fadeInRight"><img src="{$app_root}assets/img/colors/orange3/letters.png" alt="Artist Database"></a></h1>
            <p class="animated fadeInRight">{GW::ln('/g/SLOGAN')}</p>
        </div>
        <nav class="top-nav">
		
		
            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="{$app_root}assets/img/flags/16x16/flag_{$app->ln}" alt="{$app->ln}" /> {GW::ln("/g/ln/`$app->ln`")}

		</a>
                <div class="dropdown-menu ">
			{include "languages.tpl"}
                </div>
            </div> <!-- dropdown -->		
		
		
		
            <ul class="top-nav-social hidden-sm">

		    
   
		    {if $app->user_cfg->login_with_fb && !$app->user->fbid}
                <li><a href="{$login_with_fb_url}" class="animated fadeIn animation-delay-8 facebook"><i class="fa fa-facebook"></i></a></li>
			{/if}

            </ul>

            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> 
			{if $app->user}{GW::ln('/M/USERS/SIGNED_IN_AS')} <strong>{$app->user->title}</strong>{else}{GW::ln('/M/USERS/LOGIN')}{/if}
		</a>
		{if !$app->user}
                <div class="dropdown-menu dropdown-menu-right dropdown-login-box animated fadeIn">
                    <form role="form" action="{$app->buildUri(GW::s('SITE/PATH_LOGIN'))}" method="post">
			    <input type="hidden" name="act" value="do:login" />
                        <h4>{GW::ln('/M/USERS/LOGIN_FORM')}</h4>

                        <div class="form-group">
                            <div class="input-group login-input">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                <input name="login[0]" type="text" class="form-control" placeholder="{GW::ln('/M/USERS/USERNAME_EMAIL')}">
                            </div>
                            <br>
                            <div class="input-group login-input">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <input  name="login[1]" type="password" class="form-control" placeholder="{GW::ln('/M/USERS/PASSWORD')}">
                            </div>
                            <div class="checkbox pull-left">
                                  <input type="checkbox" id="checkbox_remember1" name="login_auto"  checked="checked" {*2016-11-16*}>
                                  <label for="checkbox_remember1">
                                     {GW::ln('/M/USERS/REMEMBER_ME')}
                                  </label>
                            </div>
                            <button type="submit" class="btn btn-ar btn-primary pull-right">{GW::ln('/M/USERS/LOGIN')}</button>
                            <div class="clearfix"></div>
                        </div>
                    </form>
                </div>
		{/if}
            </div> <!-- dropdown -->

            <div class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-search"></i></a>
                <div class="dropdown-menu dropdown-menu-right dropdown-search-box animated fadeIn">
                    <form role="form">
                        <div class="input-group">
                            <input id="searchquery" type="text" class="form-control" placeholder="{GW::ln('/g/SEARCH')}..."  onkeydown="if(event.keyCode==13)$('#searchquerybtn').click();">
                            <span class="input-group-btn">
                                <button id="searchquerybtn" class="btn btn-ar btn-primary" type="button" onclick="window.open('https://www.google.lt/search?q='+escape('site:ipmc.lt/artistdb ')+$('#searchquery').val());">Go!</button>
                            </span>
                        </div><!-- /input-group -->
                    </form>
                </div>
            </div> <!-- dropdown -->
        </nav>
    </div> <!-- container -->
</header> <!-- header-full -->
<nav class="navbar navbar-default navbar-header-full navbar-dark yamm navbar-static-top" role="navigation" id="header">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <i class="fa fa-bars"></i>
            </button>
            <a id="ar-brand" class="navbar-brand hidden-lg hidden-md hidden-sm" href="">
		    {$words=explode(' ',GW::s('SITE/TITLE'),2)}
		    {$words.0} <span>{$words.1}</span>
	    </a>
        </div> <!-- navbar-header -->

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="pull-right">
            <a href="javascript:void(0);" class="sb-icon-navbar sb-toggle-right"><i class="fa fa-bars"></i></a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
		    
		{include "menu.tpl" menu_path='a'}


		
		    


             </ul>
		
            <ul class="nav navbar-nav" style='float:right'>
		    

		{if $app->user}
			{include "menu.tpl" menu_path='usr'}
		{else}
			{include "menu.tpl" menu_path='nousr'}
		{/if}

		
		    


             </ul>		
        </div><!-- navbar-collapse -->
    </div><!-- container -->
</nav>	
	
	
{if !$hide_title}	
<header class="main-header">
    <div class="container">
        <h1 class="page-title">{$page->title}</h1>

        {include "breadcrumbs.tpl"}
    </div>
</header>	
{/if}
	
	
	
	
	
{if !$nocontainer}
	<div class="container">
{/if}

{include "messages.tpl"}
