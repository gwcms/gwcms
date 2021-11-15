{if $list}
	{include "product_display.tpl"}
<div class="container g-pt-100 g-pb-70">
        <div class="text-center mx-auto g-max-width-600 g-mb-50">
          <h2 class="g-color-black mb-4">{GW::ln('/m/PRODUCT_HISTORY')}</h2>
          
        </div>

        <!-- Products -->
        <div class="row">
		{include "`$m->tpl_dir`list_pics.tpl" cols=6}
        </div>
        <!-- End Products -->
	
	{if !$app->user}
		 <span class="g-color-gray-dark-v5">{GW::ln('/m/AUTH_IF_YOU_WANT_TO_KEEP_HISTORY')}</span> <a href="{$_SERVER['REQUEST_URI']}" class="gwUrlMod" data-auth="1">{GW::ln('/M/users/VIEWS/login')}</a><br/>
	{/if}
		<a class="gwUrlMod" data-path="direct/shop/shop/history" data-auth="1" data-args='{ "id":null }'>{GW::ln('/m/VIEW_ALL_HISTORY')}</a>
	
</div>
{/if}
