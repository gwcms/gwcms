{if $prodtypes || $classificatorGroup}   
<!-- Filters -->
    <div class="col-md-3 order-md-1 g-brd-right--lg g-brd-gray-light-v4 g-pt-40">
      <div class="g-pr-15--lg g-pt-60">
	<!-- Categories -->
	
	

				
				

		
	<div class="g-mb-30">
		
		{foreach $prodtypes as $pgroup}
			{$is_active=$smarty.get.prodgroup==$pgroup->id}
	  <h3 class="h5 mb-3">
		  <a class="d-block u-link-v5 {if !$is_active}g-color-gray-dark-v4{/if} g-color-primary--hover gw" href="{$m->buildUri(false,[prodgroup=>$pgroup->id])}" >
		  {$pgroup->title}
		  
			
				<span class="float-right g-font-size-12">{$pgroup->count}</span>
			
		  </a>
	  </h3>
		  {/foreach}

		  
		  <hr>
		  
		  {foreach $classificatorGroup as $classid => $classif}
	  <h6 class="h6 mb-3">
		
			{$classTypes[$classid]->title}:
		
	  </h6>			  
			  
			
			  
	  <ul class="list-unstyled">
		  
		 
		  {foreach $classif as $class}
			  {$is_active=$smarty.get.classid==$class->id}
	    <li class="my-3">
		    
		  
	      <a class="gwUrlMod d-block u-link-v5 {if !$is_active}g-color-gray-dark-v4{/if} g-color-primary--hover gw" href="#!" data-args='{ "page": "1", "classid": "{$class->id}", "prodgroup": null }'>
		      {$class->title}
		<span class="float-right g-font-size-12">{$class->count}</span></a>
	    </li>
		{/foreach}
		
	  </ul>
		      {/foreach}
	</div>





      </div>
    </div>
    <!-- End Filters -->
{/if}
    