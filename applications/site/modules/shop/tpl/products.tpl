<!-- Products -->

  <div class="row">
    <!-- Content -->
    <div class="col-md-9 order-md-2">
      <div class="g-pl-15--lg">
	<!-- Filters -->
	<div class="d-flex justify-content-end align-items-center g-brd-bottom g-brd-gray-light-v4 g-pt-40 g-pb-20">
	  <!-- Show -->
	  
	  
	  {if count($list) >=12 }
	  <div class="gw-g-mr-60 d-none d-lg-block">
		{include "`$m->tpl_dir`/paging.tpl" short=1}		  
		 </div>
	  
	  <div class="gw-g-mr-60">
	    <h2 class="h6 align-middle d-inline-block g-font-weight-400 text-uppercase g-pos-rel g-top-1 mb-0 customiselist">{GW::ln('/g/SHOW')}:</h2>

	    <!-- Secondary Button -->
	    <div class="d-inline-block btn-group g-line-height-1_2">
	      <button type="button" class="btn btn-secondary dropdown-toggle h6 align-middle g-brd-none g-color-gray-dark-v5 g-color-black--hover g-bg-transparent text-uppercase g-font-weight-300 customiselist g-pa-0 g-pl-10 g-ma-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		{$current_page_by}
	      </button>
	      <div class="dropdown-menu rounded-0">
		      {if $smarty.get.displ==table}
		<a class="dropdown-item g-color-gray-dark-v4 g-font-weight-300 gwUrlMod" href="#!" data-args='{ "pageby":64 ,"page":1}'>64</a>
		<a class="dropdown-item g-color-gray-dark-v4 g-font-weight-300 gwUrlMod" href="#!" data-args='{ "pageby":128,"page":1}'>128</a>
		<a class="dropdown-item g-color-gray-dark-v4 g-font-weight-300 gwUrlMod" href="#!" data-args='{ "pageby":256,"page":1}'>256</a>
		<a class="dropdown-item g-color-gray-dark-v4 g-font-weight-300 gwUrlMod" href="#!" data-args='{ "pageby":512,"page":1}'>512</a>			      
		{else}
		<a class="dropdown-item g-color-gray-dark-v4 g-font-weight-300 gwUrlMod" href="#!" data-args='{ "pageby":12,"page":1 }'>12</a>
		<a class="dropdown-item g-color-gray-dark-v4 g-font-weight-300 gwUrlMod" href="#!" data-args='{ "pageby":24,"page":1 }'>24</a>
		<a class="dropdown-item g-color-gray-dark-v4 g-font-weight-300 gwUrlMod" href="#!" data-args='{ "pageby":48,"page":1 }'>48</a>
		<a class="dropdown-item g-color-gray-dark-v4 g-font-weight-300 gwUrlMod" href="#!" data-args='{ "pageby":92,"page":1 }'>92</a>
		{/if}
	      </div>
	    </div>
	    <!-- End Secondary Button -->
	  </div>
	  <!-- End Show -->
	  {/if}
	{if count($list) >=12 }
	  <!-- Sort By -->
	  <div class="gw-g-mr-60">
	    <h2 class="h6 align-middle d-inline-block g-font-weight-400 text-uppercase g-pos-rel g-top-1 mb-0 customiselist">{GW::ln('/g/SORT')}:</h2>

	    <!-- Secondary Button -->
	    <div class="d-inline-block btn-group g-line-height-1_2">
	      <button type="button" class="btn btn-secondary dropdown-toggle h6 align-middle g-brd-none g-color-gray-dark-v5 g-color-black--hover g-bg-transparent text-uppercase g-font-weight-300 customiselist g-pa-0 g-pl-10 g-ma-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		{$ord=$smarty.get.ord|default}
		{if in_array($ord,$validord)}
			{GW::ln("/m/FIELDS/`$ord`")}
		{else}
			{GW::ln("/m/FIELDS/`$validord[0]`")}
		{/if}
	      </button>
	      <div class="dropdown-menu rounded-0">
		     <a class="dropdown-item g-color-gray-dark-v4 g-font-weight-300 gwUrlMod" href="#!" data-args='{ "ord":"priority" }'>{GW::ln('/m/FIELDS/priority')}</a>
		      <a class="dropdown-item g-color-gray-dark-v4 g-font-weight-300 gwUrlMod" href="#!" data-args='{ "ord":"title" }'>{GW::ln('/m/FIELDS/title')}</a>
		<a class="dropdown-item g-color-gray-dark-v4 g-font-weight-300 gwUrlMod" href="#!" data-args='{ "ord":"price" }'>{GW::ln('/m/FIELDS/price')}</a>
		
	      </div>
	    </div>
	    <!-- End Secondary Button -->
	  </div>
	  <!-- End Sort By -->
	  {/if}

	  {$passive_c="u-icon-v2 u-icon-size--xs g-brd-gray-light-v3 g-brd-black--hover g-color-gray-dark-v5 g-color-black--hover gwUrlMod"}
	  {$active_c="u-icon-v2 u-icon-size--xs g-brd-primary g-color-primary gwUrlMod"}
	  
	  
	  
	  {$list_types = json_decode($m->config->site_list_types, true)}
	  
	  
	  
	  {if count($list_types) > 1}	 
		  {$icon_by_type=['table'=>'icon-list','grid'=>'icon-grid', 'unprepared'=>'icon-music-020 u-line-icon-pro']}
		  {$default_li_type=$list_types.0}
	  <!-- Sort By -->
	  <ul class="list-inline mb-0">
		  
		{foreach $list_types as $listtype}
			
				{*sarasas tik pavadinimas ir produkto ikona*}
				{*http://1.voro.lt:2080/html/unify/2.6.3/html/unify-main/shortcodes/icons/shortcode-base-icon-line-icons-pro-1.html*}
				{*http://1.voro.lt:2080/html/unify/2.6.3/html/unify-main/shortcodes/icons/shortcode-base-icon-line-icons-pro-2.html*}
			<li class="list-inline-item">
			  <a class="{if $smarty.get.displ==$listtype || (!$smarty.get.displ && $listtype == $default_li_type)}{$active_c}{else}{$passive_c}{/if}" href="#" data-args='{ "displ":"{$listtype}" }' >
			    <i class="{if $icon_by_type[$listtype]}{$icon_by_type[$listtype]}{else}{$icon_by_type.unprepared}{/if}"></i>
			  </a>
			</li>	
			
			
		{/foreach}
		  

	  </ul>
	
	  <!-- End Sort By -->
	  {/if}
	</div>
	<!-- End Filters -->

	<!-- Products -->
	{include "`$m->tpl_dir`list.tpl"}
	<!-- End Products -->

	<hr class="g-mb-60">

	
	<div class="g-mb-100">
	{include "`$m->tpl_dir`/paging.tpl"}
	</div>
	

      </div>
    </div>
    <!-- End Content -->

    {if !$nofilters}
	{include "`$m->tpl_dir`filters.tpl"}
    {/if}
  </div>

<!-- End Products -->

<script src="{$app_root}assets/js/gw_list.js"></script>
<style>
	.gw-g-mr-60{ margin-right:4vw; }
	.customiselist{
		font-size: 1rem;
	 }
	/* responsive, form small screens, use 13px font size */
	@media (max-width: 600px) {
	    .customiselist {
		font-size: 0.71429rem !important;
	    }
	    
	}	
</style>