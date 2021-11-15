{if $paging}
{function "page_func"}
	    <li class="list-inline-item hidden-down">
			<a class="gwUrlMod {if $currentpage==$page}active u-pagination-v1__item g-width-30 g-height-30 g-brd-gray-light-v3 g-brd-primary--active g-color-white g-bg-primary--active g-font-size-12 rounded-circle g-pa-5
			   {else}
			   u-pagination-v1__item g-width-30 g-height-30 g-color-gray-dark-v5 g-color-primary--hover g-font-size-12 rounded-circle g-pa-5
			   {/if}  " 
			   href="#{$page}" data-args='{json_encode([page=>$page])}'>{$page}</a>
	    </li>	
{/function}



		
	
	<!-- Pagination -->
	<nav aria-label="Page Navigation">
	  <ul class="list-inline mb-0">
		  {$currentpage=$smarty.get.page|default:1}
		  
		  
		  {if $paging.prev}
	<li class="list-inline-item">
	      <a class="gwUrlMod u-pagination-v1__item g-width-30 g-height-30 g-brd-gray-light-v3 g-brd-primary--hover g-color-gray-dark-v5 g-color-primary--hover g-font-size-12 rounded-circle g-pa-5 g-ml-15" 
		 aria-label="{GW::ln('/m/PREV')}"  href="#{$paging.prev}" data-args='{json_encode([page=>$paging.prev])}'>
		<span aria-hidden="true">
		  <i class="fa fa-angle-left"></i>
		</span>
		<span class="sr-only">{GW::ln('/m/PREV')}</span>
	      </a>
	    </li>
		{/if}	
		
		{if $paging.first}{call "page_func" page=$paging.first}{/if}
		
		
		
		{if !$short}
			{if $paging.current-$paging.first > 3}
				<li class="list-inline-item hidden-down">
				  <span class="g-width-30 g-height-30 g-color-gray-dark-v5 g-font-size-12 rounded-circle g-pa-5">...</span>
				</li>			
			{/if}



			{if $paging.first+2 < $paging.current}
				{call "page_func" page=$paging.current-2}
			{/if}	


			{if $paging.first+1 < $paging.current}
				{call "page_func" page=$paging.current-1}
			{/if}	
		{/if}
		
		{if $paging.current != $paging.last && $paging.first!=$paging.current}
			{call "page_func" page=$paging.current}
		{/if}
		
		
		{if !$short}
			{if $paging.last-1 > $paging.current}
				{call "page_func" page=$paging.current+1}
			{/if}	


			{if $paging.last-2 > $paging.current}
				{call "page_func" page=$paging.current+2}
			{/if}	



			{if $paging.last-$paging.current > 3}
				<li class="list-inline-item hidden-down">
				  <span class="g-width-30 g-height-30 g-color-gray-dark-v5 g-font-size-12 rounded-circle g-pa-5">...</span>
				</li>			
			{/if}
		{/if}
		
		{call "page_func" page=$paging.last}
	    
	    

	    
		  {if $paging.next}
	<li class="list-inline-item">
	      <a class="gwUrlMod u-pagination-v1__item g-width-30 g-height-30 g-brd-gray-light-v3 g-brd-primary--hover g-color-gray-dark-v5 g-color-primary--hover g-font-size-12 rounded-circle g-pa-5 g-ml-15" 
		 aria-label="{GW::ln('/m/NEXT')}"  href="#{$paging.next}" data-args='{json_encode([page=>$paging.next])}'>
		<span aria-hidden="true">
		  <i class="fa fa-angle-right"></i>
		</span>
		<span class="sr-only">{GW::ln('/m/NEXT')}</span>
	      </a>
	    </li>
		{/if}		    
	    
	    
		{if !$short}
	    <li class="list-inline-item float-right">
	      <span class="u-pagination-v1__item-info g-color-gray-dark-v4 g-font-size-12 g-pa-5">{GW::ln('/m/PAGE')} {$paging.current} {GW::ln('/m/PAGE_OF')} {$paging.length}</span>
	    </li>
		{/if}
	  </ul>
	</nav>
	<!-- End Pagination -->
{/if}