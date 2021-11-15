

	{include "product_display.tpl"}
	{if $smarty.get.displ=='table'}
		{$displ=table}
		{include "`$m->tpl_dir`list_table.tpl"}
	{else}
		{$displ=icons}
		{include "`$m->tpl_dir`list_pics.tpl"}
	{/if}

	
	    
{*new arival*}
{*<div class="col-6 col-lg-4 g-mb-30">
	    <!-- Product -->
	    <figure class="g-pos-rel g-mb-20">
	      <img class="img-fluid" src="{$assets}assets/img-temp/480x700/img1.jpg" alt="Image Description">

	      <figcaption class="w-100 g-bg-primary g-bg-black--hover text-center g-pos-abs g-bottom-0 g-transition-0_2 g-py-5">
		<a class="g-color-white g-font-size-11 text-uppercase g-letter-spacing-1 g-text-underline--none--hover" href="#!">New Arrival</a>
	      </figcaption>
	    </figure>

	    <div class="media">
	      <!-- Product Info -->
	      <div class="d-flex flex-column">
		<h4 class="h6 g-color-black mb-1">
		  <a class="u-link-v5 g-color-black g-color-primary--hover" href="#!">
		    Summer shorts
		  </a>
		</h4>
		<a class="d-inline-block g-color-gray-dark-v5 g-font-size-13" href="#!">Man</a>
		<span class="d-block g-color-black g-font-size-17">$52.00</span>
	      </div>
	      <!-- End Product Info -->

	      <!-- Products Icons -->
	      <ul class="list-inline media-body text-right">
		<li class="list-inline-item align-middle mx-0">
		  <a class="u-icon-v1 u-icon-size--sm g-color-gray-dark-v5 g-color-primary--hover g-font-size-15 rounded-circle" href="#!"
		     data-toggle="tooltip"
		     data-placement="top"
		     title="Add to Cart">
		    <i class="icon-finance-100 u-line-icon-pro"></i>
		  </a>
		</li>
		<li class="list-inline-item align-middle mx-0">
		  <a class="u-icon-v1 u-icon-size--sm g-color-gray-dark-v5 g-color-primary--hover g-font-size-15 rounded-circle" href="#!"
		     data-toggle="tooltip"
		     data-placement="top"
		     title="Add to Wishlist">
		    <i class="icon-medical-022 u-line-icon-pro"></i>
		  </a>
		</li>
	      </ul>
	      <!-- End Products Icons -->
	    </div>
	    <!-- End Product -->
	  </div>*}
	  
{*discount*}
{*
	  <div class="col-6 col-lg-4 g-mb-30">
	    <!-- Product -->
	    <figure class="g-pos-rel g-mb-20">
	      <img class="img-fluid" src="{$assets}assets/img-temp/480x700/img2.jpg" alt="Image Description">

	      <span class="u-ribbon-v1 g-width-40 g-height-40 g-color-white g-bg-primary g-font-size-13 text-center text-uppercase g-rounded-50x g-top-10 g-right-minus-10 g-px-2 g-py-10">-40%</span>
	    </figure>

	    <div class="media">
	      <!-- Product Info -->
	      <div class="d-flex flex-column">
		<h4 class="h6 g-color-black mb-1">
		  <a class="u-link-v5 g-color-black g-color-primary--hover" href="#!">
		    Stylish shirt
		  </a>
		</h4>
		<a class="d-inline-block g-color-gray-dark-v5 g-font-size-13" href="#!">Woman</a>
		<span class="d-block g-color-black g-font-size-17">$99.00</span>
	      </div>
	      <!-- End Product Info -->

	      <!-- Products Icons -->
	      <ul class="list-inline media-body text-right">
		<li class="list-inline-item align-middle mx-0">
		  <a class="u-icon-v1 u-icon-size--sm g-color-gray-dark-v5 g-color-primary--hover g-font-size-15 rounded-circle" href="#!"
		     data-toggle="tooltip"
		     data-placement="top"
		     title="Add to Cart">
		    <i class="icon-finance-100 u-line-icon-pro"></i>
		  </a>
		</li>
		<li class="list-inline-item align-middle mx-0">
		  <a class="u-icon-v1 u-icon-size--sm g-color-gray-dark-v5 g-color-primary--hover g-font-size-15 rounded-circle" href="#!"
		     data-toggle="tooltip"
		     data-placement="top"
		     title="Add to Wishlist">
		    <i class="icon-medical-022 u-line-icon-pro"></i>
		  </a>
		</li>
	      </ul>
	      <!-- End Products Icons -->
	    </div>
	    <!-- End Product -->
	  </div>
*}

{*sold out*}

{*	  <div class="col-6 col-lg-4 g-mb-30">
	    <!-- Product -->
	    <figure class="g-pos-rel g-mb-20">
	      <img class="img-fluid" src="{$assets}assets/img-temp/480x700/img3.jpg" alt="Image Description">

	      <figcaption class="w-100 g-bg-lightred text-center g-pos-abs g-bottom-0 g-transition-0_2 g-py-5">
		<span class="g-color-white g-font-size-11 text-uppercase g-letter-spacing-1">Sold Out</a>
	      </figcaption>
	    </figure>

	    <div class="media">
	      <!-- Product Info -->
	      <div class="d-flex flex-column">
		<h4 class="h6 g-color-black mb-1">
		  <a class="u-link-v5 g-color-black g-color-primary--hover" href="#!">
		    Classic jacket
		  </a>
		</h4>
		<a class="d-inline-block g-color-gray-dark-v5 g-font-size-13" href="#!">Man</a>
		<span class="d-block g-color-black g-font-size-17">$49.99</span>
	      </div>
	      <!-- End Product Info -->

	      <!-- Products Icons -->
	      <ul class="list-inline media-body text-right">
		<li class="list-inline-item align-middle mx-0">
		  <a class="u-icon-v1 u-icon-size--sm g-color-gray-dark-v5 g-color-primary--hover g-font-size-15 rounded-circle" href="#!"
		     data-toggle="tooltip"
		     data-placement="top"
		     title="Add to Cart">
		    <i class="icon-finance-100 u-line-icon-pro"></i>
		  </a>
		</li>
		<li class="list-inline-item align-middle mx-0">
		  <a class="u-icon-v1 u-icon-size--sm g-color-gray-dark-v5 g-color-primary--hover g-font-size-15 rounded-circle" href="#!"
		     data-toggle="tooltip"
		     data-placement="top"
		     title="Add to Wishlist">
		    <i class="icon-medical-022 u-line-icon-pro"></i>
		  </a>
		</li>
	      </ul>
	      <!-- End Products Icons -->
	    </div>
	    <!-- End Product -->
	  </div>*}
	  
	  <p class="text-muted">{GW::ln('/m/BOTTOM_INFO_TEXT')}</p>