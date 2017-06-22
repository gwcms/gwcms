{include file="head.tpl"}



{if !isset($smarty.get.clean) && !$no_standart_cms_frame && !$smarty.get.print_view}
	<body class="">		
		
    <div id="container" class="effect mainnav-lg">
		
		
        <!--NAVBAR-->
        <!--===================================================-->
        <header id="navbar">
            <div id="navbar-container" class="boxed">

                <!--Brand logo & name-->
                <!--================================-->
                <div class="navbar-header">
                    {include "logo.tpl"}
                </div>
                <!--================================-->
                <!--End brand logo & name-->


                <!--Navbar Dropdown-->
                <!--================================-->
                <div class="navbar-content clearfix">
                    <ul class="nav navbar-top-links pull-left">

                        <!--Navigation toogle button-->
                        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                        <li class="tgl-menu-btn">
                            <a class="mainnav-toggle" href="#">
                                <i class="ti-view-list icon-lg"></i>
                            </a>
                        </li>
                        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                        <!--End Navigation toogle button-->

						{*include "do_notifications.tpl"*}
                        {*include "do_mega_dropdown.tpl"*}

                    </ul>
                    <ul class="nav navbar-top-links pull-right">
						
						{$app->processHook("NAVBAR_LEFT_1")}

                        {include "langselect.tpl"}
                        {include "do_userdropdown.tpl"}

						{*
                        <li>
                            <a href="#" class="aside-toggle navbar-aside-icon">
                                <i class="pci-ver-dots"></i>
                            </a>
                        </li>
						*}
                    </ul>
                </div>
                <!--================================-->
                <!--End Navbar Dropdown-->

            </div>
        </header>
        <!--===================================================-->
        <!--END NAVBAR-->		
		

        <div class="boxed">

            <!--CONTENT CONTAINER-->
            <!--===================================================-->
            <div id="content-container">	
				
				{*
                <div id="page-title">
                    <h1 class="page-header text-overflow">{$app->page->title}</h1>

					
                    <!--Searchbox-->
                    <div class="searchbox">
                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" placeholder="Search..">
							
							
                            <span class="input-group-btn">
                                <button class="text-muted" type="button"><i class="ti-search"></i></button>
                            </span>
                        </div>
                    </div>
					
                </div>	
				*}
				
				
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <!--End page title-->

				
				{if $app->user}
					{include file="breadcrumbs.tpl"}
				{/if}
		
			

                <!--Page content-->
                <!--===================================================-->
                <div id="page-content">	
					
				{else}
					<body class="gwBodyClean {if $smarty.get.clean==2}gwBodyClean2{/if}"   data-clean="1">
				{/if}	
				{include "messages.tpl"}
				


{include "do_toolbar.tpl"}

<div id='gwcms-dynamic-alerts-container'></div>

{foreach $log as $item}
	{if $item}
		{d::ldump($item)}
	{/if}
{/foreach}

