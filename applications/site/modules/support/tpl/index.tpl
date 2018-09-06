
<div class="col-md-6 col-lg-4 d-flex g-theme-bg-black-v1 g-pa-40">
	<div class="align-self-center w-100" style="position:absolute">
		<div class="u-heading-v8-2 g-mb-60">
			<h2 class="h1 text-uppercase u-heading-v8__title g-font-weight-700 g-font-size-26 g-color-white g-mb-30">

				{GW::ln('/g/HAVE_ANY_QUESTIONS')}
			</h2>
		</div>

		<form role="form" id="supportform" onsubmit="return SupportSubmit()">

			<div class="form-group g-mb-10">
				<input name="item[name]" required="1" class="form-control h-100 g-font-size-15 g-font-secondary g-color-white-opacity-0_8 g-placeholder-inherit g-theme-bg-gray-dark-v1 u-shadow-v16 g-brd-none g-rounded-1 g-pa-10" type="text" placeholder="{GW::ln('/g/YOUR_NAME')}">
			</div>

			<div class="form-group g-mb-10">
				<input name="item[phone]" required="1"  class="form-control h-100 g-font-size-15 g-font-secondary g-color-white-opacity-0_8 g-placeholder-inherit g-theme-bg-gray-dark-v1 u-shadow-v16 g-brd-none g-rounded-1 g-pa-10" type="tel" placeholder="{GW::ln('/g/YOUR_PHONE')}">
			</div>

			<div class="form-group g-mb-10">
				<input  name="item[subject]" required="1"  class="form-control h-100 g-font-size-15 g-font-secondary g-color-white-opacity-0_8 g-placeholder-inherit g-theme-bg-gray-dark-v1 u-shadow-v16 g-brd-none g-rounded-1 g-pa-10" type="text" placeholder="{GW::ln('/g/QUESTION_SUBJECT')}">
			</div>

			<div class="form-group g-mb-40">
				<textarea  name="item[message]" required="1"  class="form-control g-font-size-15 g-font-secondary g-color-white-opacity-0_8 g-placeholder-inherit g-theme-bg-gray-dark-v1 u-shadow-v16 g-resize-none g-brd-none g-rounded-1 g-pa-10" rows="5" placeholder="{GW::ln('/g/MESSAGE')}"></textarea>
			</div>

			<button class="btn btn-md text-uppercase u-btn-primary g-font-weight-700 g-font-size-12 rounded-0 g-py-10 g-px-25" type="submit" role="button">{GW::ln('/g/SEND_MESSAGE')}</button>
		</form>

		<div id="supportSuccess" class="mtSuccOver" style="display:none;"><div><div>{GW::ln('/g/SUPPORT/SUCCESS_MESSAGE')} <button onclick="SupportReset()">ok</button></div> </div></div>
	</div>
</div>