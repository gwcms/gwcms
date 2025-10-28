{include "default_open.tpl"}
{include "`$smarty.current_dir`/order_display.tpl"}


<div class="container g-pt-70 g-pb-30">
        <div class="row">
		<!-- Profile Settings -->
		<div class="col-lg-3 g-mb-50">

			<aside class="g-brd-around g-brd-gray-light-v4 rounded g-px-20 g-py-30">
				<!-- Profile Picture --
				{*
				<div class="text-center g-pos-rel g-mb-30">
				<div class="g-width-100 g-height-100 mx-auto mb-3">
				{*<img class="img-fluid rounded-circle" src="assets/img-temp/100x100/img1.jpg" alt="Image Decor">*}
				<span class="u-icon-v1 g-mb-10">
				      <i class="icon-user"></i>
				    </span>
			      </div>
			       
	      
			      <span class="d-block g-font-weight-500">James Collins</span>
	      
			      <span class="u-icon-v3 u-icon-size--xs g-color-white--hover g-bg-primary--hover rounded-circle g-pos-abs g-top-0 g-right-15 g-cursor-pointer" title="" data-toggle="tooltip" data-placement="top" data-original-title="Change Profile Picture">
				<i class="icon-finance-067 u-line-icon-pro"></i>
			      </span>
			    </div>
			    <hr class="g-brd-gray-light-v4 g-my-30">
			     *}
				<!-- End Profile Picture -->



				<!-- Profile Settings List -->
				<ul class="list-unstyled mb-0">
					{*
					<li class="g-pb-3">
					<a class="d-block align-middle u-link-v5 g-color-text g-color-primary--hover g-bg-gray-light-v5--hover rounded g-pa-3" href="page-wallet-1.html">
					<span class="u-icon-v1 g-color-gray-dark-v5 mr-2"><i class="icon-finance-059 u-line-icon-pro"></i></span>
					Your Wallet
					</a>
					</li>
					*}
					{$aclass="d-block align-middle active u-link-v5 g-color-text g-color-primary--hover g-bg-gray-light-v5--hover g-color-primary--parent-active g-bg-gray-light-v5--active rounded g-pa-3"}
					{if !$app->user}
						<li class="g-py-3">
						  <a class="{$aclass}" rel="nofollow"  href="{$ln}/direct/users/users/login">
							  <span class="u-icon-v1 g-color-gray-dark-v5 mr-2"><i class="fa fa-sign-in" style="display:inline-block;line-height: 30px;"></i></span>
							  {GW::ln('/M/USERS/VIEWS/login')}
						  </a>
						</li>
						<li class="g-py-3">
						  <a class="{$aclass}" rel="nofollow" href="{$ln}/direct/users/users/register">
						    <span class="u-icon-v1 g-color-gray-dark-v5 mr-2"><i class="icon-user-follow" style="display:inline-block;line-height: 30px;"></i></span> 
							{GW::ln('/M/USERS/VIEWS/register')}
						  </a>
						</li>
				       {/if}					
					
					<li class="g-py-3">
						<a class="{$aclass}" href="{$app->buildUri('direct/shop/orders/list')}">
							<span class="u-icon-v1 g-color-gray-dark-v5 mr-2"><i class="icon-finance-114 u-line-icon-pro"></i></span>
								{GW::ln('/m/YOUR_ORDERS')}
						</a>
					</li>

					<li class="g-py-3">
						<a class="{$aclass}" href="{$app->buildUri('direct/shop/shop/wishlist')}">
							<span class="u-icon-v1 g-color-gray-dark-v5 mr-2"><i class="icon-medical-022 u-line-icon-pro"></i></span>
								{GW::ln('/m/WISHLIST')}
						</a>
					</li>
					{*
					<li class="g-py-3">
					<a class="d-block align-middle u-link-v5 g-color-text g-color-primary--hover g-bg-gray-light-v5--hover rounded g-pa-3" href="page-addresses-1.html">
					<span class="u-icon-v1 g-color-gray-dark-v5 mr-2"><i class="icon-real-estate-027 u-line-icon-pro"></i></span>
					Addresses
					</a>
					</li>
					<li class="g-py-3">
					<a class="d-block align-middle u-link-v5 g-color-text g-color-primary--hover g-bg-gray-light-v5--hover rounded g-pa-3" href="page-payment-options-1.html">
					<span class="u-icon-v1 g-color-gray-dark-v5 mr-2"><i class="icon-finance-110 u-line-icon-pro"></i></span>
					Payment Options
					</a>
					</li>
					<li class="g-py-3">
					<a class="d-block align-middle u-link-v5 g-color-text g-color-primary--hover g-bg-gray-light-v5--hover rounded g-pa-3" href="page-login-security-1.html">
					<span class="u-icon-v1 g-color-gray-dark-v5 mr-2"><i class="icon-finance-135 u-line-icon-pro"></i></span>
					Login &amp; Security
					</a>
					</li>
					<li class="g-pt-3">
					<a class="d-block align-middle u-link-v5 g-color-text g-color-primary--hover g-bg-gray-light-v5--hover rounded g-pa-3" href="page-notifications-1.html">
					<span class="u-icon-v1 g-color-gray-dark-v5 mr-2"><i class="icon-education-033 u-line-icon-pro"></i></span>
					Notifications
					</a>
					</li>
					*}
				</ul>
				<!-- End Profile Settings List -->
			</aside>
		</div>
		<!-- End Profile Settings -->

		<!-- Orders -->
		<div class="col-lg-9 g-mb-50">
			{*
			<div class="row justify-content-end g-mb-20 g-mb-0--md">
			<div class="col-md-7 g-mb-30">
			<!-- Search Form -->
			<form class="input-group g-pos-rel">
			<span class="g-pos-abs g-top-0 g-left-0 g-z-index-3 g-px-13 g-py-10">
			<i class="g-color-gray-dark-v4 g-font-size-12 icon-education-045 u-line-icon-pro"></i>
			</span>
			<input class="form-control u-form-control g-brd-around g-brd-gray-light-v3 g-brd-primary--focus g-font-size-13 g-rounded-left-5 g-pl-35 g-pa-0" type="search" placeholder="Search all orders">
			<div class="input-group-append g-brd-none g-py-0">
			<button class="btn u-btn-black g-font-size-12 text-uppercase g-py-12 g-px-25" type="submit">Search Orders</button>
			</div>
			</form>
			<!-- End Search Form -->
			</div>
			</div>
			*}

			<!-- Links -->
			<ul class="list-inline g-brd-bottom--sm g-brd-gray-light-v3 mb-5">
				{$active="g-brd-bottom g-brd-2 g-brd-primary g-color-main g-color-black g-font-weight-600 g-text-underline--none--hover g-px-10 g-pb-13"}
				{$passive="g-brd-bottom g-brd-2 g-brd-transparent g-color-main g-color-gray-dark-v4 g-color-primary--hover g-text-underline--none--hover g-px-10 g-pb-13"}

				<li class="list-inline-item g-pb-10 g-pr-10 g-mb-20 g-mb-0--sm">
					<a class="gwUrlMod {if !$smarty.get.status}{$active}{else}{$passive}{/if}"  href="#calnceledOrders" data-args='{json_encode([status=>null])}'>{GW::ln('/m/VIEWS/orders')}</a>
				</li>
				{*
				<li class="list-inline-item g-pb-10 g-px-10 g-mb-20 g-mb-0--sm">
				<a class="g-brd-bottom g-brd-2 g-brd-transparent g-color-main g-color-gray-dark-v4 g-color-primary--hover g-text-underline--none--hover g-px-10 g-pb-13" href="page-open-orders-1.html">{GW::ln('/m/A')}</a>
				</li>
				*}
				<li class="list-inline-item g-pb-10 g-pl-10 g-mb-20 g-mb-0--sm">
					<a class="gwUrlMod {if $smarty.get.status==6}{$active}{else}{$passive}{/if}" href="#calnceledOrders" data-args='{json_encode([status=>6])}'>{GW::ln('/m/CANCELED_ORDERS')}</a>
				</li>
			</ul>
			<!-- End Links -->

			{*
			<div class="mb-5">
			<h3 class="h6 d-inline-block">2 orders <span class="g-color-gray-dark-v4 g-font-weight-400">placed in</span></h3>
	  
			<!-- Secondary Button -->
			<div class="d-inline-block btn-group u-shadow-v19 ml-2">
			<button type="button" class="btn u-btn-black dropdown-toggle h6 align-middle g-brd-none g-color-black g-bg-gray-light-v5 g-bg-gray-light-v4--hover g-font-weight-300 g-font-size-12 g-py-10 g-ma-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			past 6 months
			</button>
			<div class="dropdown-menu rounded-0 g-font-size-12">
			<a class="dropdown-item g-color-black g-font-weight-300" href="#!">last 30 days</a>
			<a class="dropdown-item g-color-black g-bg-gray-light-v5 g-font-weight-300" href="#!">past 6 months</a>
			<a class="dropdown-item g-color-black g-font-weight-300" href="#!">2017</a>
			<a class="dropdown-item g-color-black g-font-weight-300" href="#!">2016</a>
			</div>
			</div>
			<!-- End Secondary Button -->
			</div>
			*}

			<!-- Order Block -->
			{include "product_display.tpl"}

		{if !$list}
			<p class="g-color-gray-dark-v4 g-font-weight-400 g-font-size-12 text-uppercase g-mb-2">{GW::ln('/m/EMPTY')}</p>
		{else}
			{foreach $list as $item}
				{call "display_order"}
			{/foreach}
		{/if}


				{*
				<!-- Pagination -->
				<nav class="g-mt-100" aria-label="Page Navigation">
				<ul class="list-inline mb-0">
				<li class="list-inline-item hidden-down">
				<a class="active u-pagination-v1__item g-width-30 g-height-30 g-brd-gray-light-v3 g-brd-primary--active g-color-white g-bg-primary--active g-font-size-12 rounded-circle g-pa-5" href="#!">1</a>
				</li>
				<li class="list-inline-item hidden-down">
				<a class="u-pagination-v1__item g-width-30 g-height-30 g-color-gray-dark-v5 g-color-primary--hover g-font-size-12 rounded-circle g-pa-5" href="#!">2</a>
				</li>
				<li class="list-inline-item">
				<a class="u-pagination-v1__item g-width-30 g-height-30 g-brd-gray-light-v3 g-brd-primary--hover g-color-gray-dark-v5 g-color-primary--hover g-font-size-12 rounded-circle g-pa-5 g-ml-15" href="#!" aria-label="Next">
				<span aria-hidden="true">
				<i class="fa fa-angle-right"></i>
				</span>
				<span class="sr-only">Next</span>
				</a>
				</li>
				<li class="list-inline-item float-right">
				<span class="u-pagination-v1__item-info g-color-gray-dark-v4 g-font-size-12 g-pa-5">Page 1 of 2</span>
				</li>
				</ul>
				</nav>
				<!-- End Pagination -->
				*}
			</div>
			<!-- Orders -->
		</div>
	</div>



{include "default_close.tpl"}
