
<section id="services" class="row no-gutters">
	<div class="col-lg-3 text-center g-bg-primary g-py-30 g-px-10">
		<div class="u-heading-v8-2 g-mb-30">
            <h2 class="text-uppercase u-heading-v8__title g-font-weight-700 g-font-size-40 mb-0">
				<strong class="h6 d-inline-block g-theme-bg-black-v1 g-color-white g-mb-20">{GW::ln('/g/ARTICLES/INDEX_CAPTION')}</strong>
				<br>{GW::ln('/g/ARTICLES/INDEX_CAPTION_LARGE')}
            </h2>
		</div>

		<p class="g-color-white-opacity-0_8 g-px-30 mb-0">{GW::ln('/g/ARTICLES/INDEX_BIG_TEXT')}</p>
	</div>

	<div class="col-lg-9">
		<div class="js-carousel u-carousel-v5"
			 data-infinite="true"
			 data-slides-scroll="true"
			 data-slides-show="3"
			 data-arrows-classes="u-arrow-v1 g-pos-abs g-top-100 g-width-45 g-height-45 g-font-size-default g-color-white g-bg-primary g-theme-bg-black-v1--hover"
			 data-arrow-left-classes="fa fa-chevron-left g-left-0"
			 data-arrow-right-classes="fa fa-chevron-right g-right-0"
			 data-responsive='[{
			 "breakpoint": 1200,
			 "settings": {
			 "slidesToShow": 2
			 }
			 }, {
			 "breakpoint": 768,
			 "settings": {
			 "slidesToShow": 1
			 }
			 }]'>


			{foreach $list as $article}  
				
				{$img = $article->image}
        
    			
	
				
				<div class="js-slide">
					<article class="u-shadow-v26 g-parent g-theme-bg-black-v1 g-bg-primary--hover g-transition-0_2 g-transition--ease-in">
						<div class="u-bg-overlay g-bg-black-opacity-0_3--after">
							<img class="img-fluid w-100" src="/tools/img/{$img->key}&v={$img->v}&size=570x436&method=crop" alt="Image description">
						</div>

						<div class="text-center g-pa-45">
							<h3 class="text-uppercase g-font-weight-700 g-font-size-default g-font-secondary g-color-white g-mb-15">{$article->title}</h3>
							<p class="g-color-white-opacity-0_8 g-mb-35">{$article->short|truncate:120}</p>
							<a class="btn btn-md text-uppercase u-btn-primary g-font-weight-700 g-font-size-12 g-theme-bg-black-v1--parent-hover rounded-0 g-py-10 g-px-25" href="#!">{GW::ln('/g/ARTICLES/LEARN_MORE')}</a>
						</div>
					</article>
				</div>
			{/foreach}

		</div>
	</div>
</section>

<br />