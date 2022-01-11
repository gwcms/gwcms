{include "default_open.tpl"}

{include "common_module/elements/input_func.tpl"}

{capture append=footer_hidden}
<!-- JS Implementing Plugins -->
<script  src="{$assets}../assets/vendor/appear.js"></script>

<!-- JS Unify -->
<script  src="{$assets}../assets/js/components/hs.counter.js"></script>

<!-- JS Plugins Init. -->
<script >
  $(document).on('ready', function () {
    // initialization of counters
    var counters = $.HSCore.components.HSCounter.init('[class*="js-counter"]');
  });
</script>
{/capture}


{if !$smarty.get.clean}
<section class="container  g-pt-20 g-pb-20">
        <div class="row">
		
		
		<div class="col-lg-7 order-lg-2">
			

		<br />	    
			
		
		
            <div class="g-brd-around g-brd-gray-light-v3 g-bg-white rounded g-px-30 g-py-50 mb-4">
{/if}		   
		
				    
		    
              <header class="text-center mb-4">
                <h1 class="h4 g-color-black g-font-weight-400">
			{if $item->id}{GW::ln('/g/EDIT')}{else}{GW::ln('/g/CREATE')}{/if} 
			{GW::ln("/m/ITEM_TYPE_{$item->classname}_TITLE",[l=>gal,c=>1])} <span class="g-color-primary">{$item->title}
				
			{if $smarty.get.dialog && $app->user && $app->user->isRoot()}
				<a href="{Navigator::buildURI(false,[clean=>null,dialog=>null])}" target="_blank" style="color:orange"><i class="fa fa-link"></i></a>
			{/if}
			</span>
		</h1>
		{if !$item->id}<p>{GW::ln('/m/CREATE_NOTES')}</p>{/if}
              </header>

              <!-- Form -->
              <form id="itemForm" class="g-py-15" action="{$smarty.server.REQUEST_URI}" method="post">
		<input type="hidden" name="act" value="doSave" />
		<input type="hidden" name="item[id]" value="{$item->id}" />