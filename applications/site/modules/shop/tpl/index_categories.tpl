<div class="g-pl-15--lg">
	
	{if $smarty.get.test}
		{$cond=''}
	{else}
		{$cond='active=1'}
	{/if}
	


			
		<div class="">
			

			
			
			
				<figure class="g-pos-rel">


		
			
		
			<div  class="sliding-background" style='padding:30px 50px 30px 50px; background-size: cover;
			     max-height:400px;background-image:url("{$app_base}tools/img/{$img->key}&v={$img->v}&size=800x500&method=crop")'>
			 <span class="catidxtitle">test</span>
			<br>
			<span class="catidxdesc">abc</span>
			
			
			</div>		
		
				</figure>
			


			
			{$prods =  $cat->products}
			{$list = $cat->getProducts()}
			
		</div>
		<div class="d-flex justify-content-end align-items-center g-brd-bottom g-brd-gray-light-v4 g-pt-10 g-pb-20">	
			{include "`$smarty.current_dir`/list.tpl"}
		</div>

	
	
	
	
	
</div>

<style>
	.catidxtitle, .catidxdesc{
		background-color: rgba(255,255,255,0.7);
		padding: 2px 10px 2px 10px;
	}
	.catidxtitle{ font-size: 30px; }
	
	
	
	
	
	
.sliding-background {
  animation: slide 30s linear infinite;
}

@keyframes slide{
  0%{
    background-position: 0% 0%;
  }
  100%{
    background-position: 0% 100%;
  }
}	
	
</style>

