

<h2 class="h5 g-color-gray-light-v3 mb-4">{GW::ln('/g/SUBSCRIBE')}</h2>

<!-- Subscribe Form -->
<form class="input-group u-shadow-v19 rounded" onsubmit="return NewsLetterSubmit(this)">
  <input 
	  name="item[name]"
	  class="form-control g-brd-none g-color-gray-dark-v5 g-bg-main-light-v2 g-bg-main-light-v2--focus g-placeholder-gray-dark-v3 rounded g-px-20 g-py-8" 
	  type="email" placeholder="{GW::ln('/g/ENTER_EMAIL')}">
  <span class="input-group-addon u-shadow-v19 g-brd-none g-bg-main-light-v2 g-pa-5">
    <button class="btn u-btn-primary rounded text-uppercase g-py-8 g-px-18" type="submit"><i class="fa fa-angle-right"></i></button>
  </span>

  <div class="formSuccess" style="display:none;">Ačiū kad registruojatės!</div>
</form>


<!-- End Subscribe Form -->


