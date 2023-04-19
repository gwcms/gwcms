{include "default_open.tpl"}

<br />

     
<div id="accordion-08" class="u-accordion u-accordion-color-primary" role="tablist" aria-multiselectable="true">
  <!-- Card -->
  
  {foreach $list as $item}
  <div class="card g-rounded-30 g-overflow-hidden g-brd-none mb-2">
    <div id="accordion-08-heading-{$item->id}" class="u-accordion__header g-pa-0" role="tab">
      <h5 class="mb-0 text-uppercase g-font-size-default g-font-weight-700 g-pa-20a mb-0">
        <a class="d-flex g-color-main g-text-underline--none--hover" href="#accordion-08-body-{$item->id}" data-toggle="collapse" data-parent="#accordion-08" aria-expanded="true" aria-controls="accordion-08-body-01">
          <span class="u-accordion__control-icon g-brd-right g-brd-gray-light-v4 g-color-primary text-center g-pa-20">
            <i class="fa fa-plus"></i>
            <i class="fa fa-minus"></i>
          </span>
          <span class="g-pa-20">
            {$item->title|escape}
          </span>
        </a>
      </h5>
    </div>
    <div id="accordion-08-body-{$item->id}" class="collapse show" role="tabpanel" aria-labelledby="accordion-08-heading-{$item->id}" data-parent="#accordion-08">
      <div class="u-accordion__body g-bg-gray-light-v5 g-px-50 g-py-30">
        {$item->answer|escape|nl2br}
      </div>
    </div>
  </div>
     {/foreach}
  <!-- End Card -->

</div>
  
  <br /><br />


 {include "default_close.tpl"}