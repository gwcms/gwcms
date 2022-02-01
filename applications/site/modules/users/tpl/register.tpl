{include "default_open.tpl"}



{capture assign=infoblock}{strip}{include "user_register_infoblock.tpl"}{/strip}{/capture}


{if $infoblock}
<section class="container  g-pt-20 g-pb-20">
        <div class="row">
		<div class="col-lg-7 order-lg-2">
{else}
	<div class="row  g-pt-20 g-pb-20">
		
	<div class="col-lg-6" style="float:none;margin:auto;">
{/if}
			
            <div class="text-center  mb-30">
              <p class="g-color-gray-dark-v5 mb-0">{GW::ln('/m/ALREADY_HAVE_ACCOUNT_Q')}
                <a class="g-font-weight-600" href="{$ln}/direct/users/users/login">{GW::ln('/M/USERS/VIEWS/login')}</a></p>
            </div>
		<br />	    
			
            <div class="g-brd-around g-brd-gray-light-v3 g-bg-white rounded g-px-30 g-py-50 mb-4">
		    {include "`$m->tpl_dir`/register_form.tpl"}
            </div>

            <div class="text-center">
              <p class="g-color-gray-dark-v5 mb-0">{GW::ln('/m/ALREADY_HAVE_ACCOUNT_Q')}
                <a class="g-font-weight-600" href="{$ln}/direct/users/users/login">{GW::ln('/M/USERS/VIEWS/login')}</a></p>
            </div>
	    
	    
{if $infoblock}	    
			</div>
	    
	    
	    
	    
			
	  <div class="col-lg-5 order-lg-1 g-mb-80  g-hidden-sm-down">{$infoblock}</div>			
			
          </div>

</section>
{else}
	</div>
	</div>
{/if}

{include "default_close.tpl"}