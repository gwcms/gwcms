
<div>

	<div class="row">
		<div class="col-md-6">
    <a class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25 g-font-weight-600"  rel="nofollow" href="{$app->buildURI('direct/users/users/register',[after_auth_nav=>$smarty.get.after_auth_nav])}">{GW::ln('/m/VIEWS/register')}</a>
		</div>
		<div class="col-md-6">


    <a class="btn btn-block u-btn-purple g-font-size-12 text-uppercase g-py-12 g-px-25 g-font-weight-600"  rel="nofollow" href="{$app->buildURI('direct/users/users/login',[after_auth_nav=>$smarty.get.after_auth_nav])}">{GW::ln('/m/VIEWS/login')}</a>
		</div>  
		</div>

	<div class="row mt-4">
		<div class="col-md-12">

<a class="btn btn-block u-btn-facebook g-font-size-12 text-uppercase g-py-12 g-px-25 mr-2" type="button"  rel="nofollow" href="{$app->buildURI('direct/users/fblogin/redirect',[after_auth_nav=>$smarty.get.after_auth_nav])}">
  <i class="mr-1 fa fa-facebook"></i>
  {GW::ln('/m/SIGN_UP_OR_REGISTER_WITH_FB')}
</a>
  </div>
		</div>
  
</div>