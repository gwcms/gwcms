{include "head.tpl"}
{include "header.tpl"}


    <!-- Header -->
    
    <!-- End Header -->

    <main class="container" role="main">
      <div class="row flex-xl-nowrap">
        <!-- Sidebar -->
        <div class="col-12 col-md-3 border-right">
          <div class="mtarticlemenu sticky-top docs-sidebar u-font-size-90 py-4">
            <!-- Introduction -->
			
			<h2 class="h6 font-weight-bold mb-2"><a href="{$list_url}">Visos kategorijos</a></h2>
				<ul class="list-unstyled">
			{foreach $groups as $id => $title}
				
				  <li class="mb-1"><a {if $smarty.get.group==$id}class="active"{/if} href="{$list_url}?group={$id}">{$title}</a></li>								
			{/foreach}
			</ul>


            <!-- End Others -->
          </div>
        </div>
        <!-- End Sidebar -->

        <!-- Content -->
        <div class="col-12 col-md-9 px-md-7 py-6">
			
			
		{if $item}
            <header>
              <h2 class="h1 font-weight-light">{$item->title}</h2>
            </header>
			
			{$img = $item->image}
			<img class="img-fluid mar-btm" src="{$app_base}tools/img/{$img->key}&v={$img->v}&size=759x500&method=crop" alt="Image description">
			
			<p>{$item->short}</p>

			{$item->text}
            <!-- End About -->

            <hr class="mt-7 mb-4">			
			
		{/if}			
			
		{if $item}
			{$size="250x150"}
		{else}
			{$size="300x250"}
		{/if}
		
		{if $list}
			{foreach $list as $itm}{if $itm->id!=$item->id}
				
            <header onclick="location.href = $('.mtlink{$itm->id}').attr('href')" style="cursor:pointer">
              <h2 class="h1 font-weight-light">{$itm->title}</h2>
            </header>
			
			{$img = $itm->image}
			<div class="row" onclick="location.href = $(this).find('.mtlink{$itm->id}').attr('href')" style="cursor:pointer">
				<div class="col-lg-5 col-md-9">
			<img class="" src="{$app_base}tools/img/{$img->key}&v={$img->v}&size={$size}&method=crop" alt="Image description">
				</div>
				<div class="col-lg-7 col-md-9">
						{$itm->short}
						<br/><br/>
						<a href="{$item_url}?id={$itm->id}" class="mtlink{$itm->id} btn btn-primary mar-top">Skaityti daugiau</a>
				</div>
			</div>
            <!-- End About -->
			

            <hr class="mt-7 mb-4">	
			{/if}{/foreach}
			
		{/if}

        </div>
        <!-- End Content -->
      </div>
    </main>

 {include "footer.tpl"}