
     <div class="text-center">
              <p class="g-color-gray-dark-v5 mb-10">
                <a class="g-font-weight-600" href=""></a></p>
            </div>	


<div class="pickloginorregister g-bg-gray-dark-v2 g-color-white g-px-15 g-py-10">
                    <!-- Nav tabs -->
                    <ul class="nav nav-fill u-nav-v1-1 u-nav-light" 
			 data-target="nav-1-1-primary-hor-fill" 
			data-btn-classes="btn btn-md btn-block rounded-0 u-btn-outline-white g-mb-20">
                      <li class="nav-item">
                        <a class="nav-link {if $login==0}active{/if}"  href="{$ln}/direct/users/users/register" >
				<b>{GW::ln('/m/VIEWS/register')}</b>
				<br><small>{GW::ln('/m/DONT_HAVE_ACCOUNT')}</small></a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link {if $login==1}active{/if}"  href="{$ln}/direct/users/users/login" >
				
				<b>{GW::ln('/M/USERS/VIEWS/login')}</b>
				<br>
				<small>{GW::ln('/m/ALREADY_HAVE_ACCOUNT_Q')}</small>
			</a>
                      </li>
                      
                    </ul>
                    <!-- End Nav tabs -->

                    <!-- End Tab panes -->
                  </div>
		<br>	
<style>
	.pickloginorregister .nav-link{ color: white; font-size: 18px }
	.pickloginorregister .active{ background-color:white;color:black }
	.pickloginorregister small{ color: silver }
</style>