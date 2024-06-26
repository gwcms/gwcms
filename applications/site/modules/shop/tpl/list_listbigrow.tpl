{$cols=$cols|default:12}
{$celsz=12/$cols}
{*
{$imsize=[4=>"480x700",6=>"200x200"]}
{$imsize=$imsize[$cols]}
*}
{$imsize="200x200"}


	{foreach $list as $item}
		{$link=$app->buildUri("direct/shop/shop/p/{FH::urlStr($item->title)}",[id=>$item->id])}
		
		<a href="{$link}">
		<div class="wrapper" style="background-color:#f2f2f2">
			
			<div class="cellim">
				{call name="product_image" product=$item size=$imsize crop=1}
			</div>
			
			{*background-image:url('applications/site/assets/img/{if $item->gender_limit=='f'}woman{else}man{/if}.svg')"*}
			
			<div class="cellcont" >
				<a href="{$link}" style="color:inherit;text-decoration:none">
				<h3>{$item->title}</h3>
				{$field=$m->getFirstClass($item)}
				{$m->getClassifVal($item->get($field))}


			
				<div class="event_description">
					{$item->keyval->description|nl2br}
				</div>
				

				<a href="{$link}" class="btn btn-primary mar-top">{GW::ln('/m/MORE')}</a>

		


			</div>
		</div>
			</a>
					
		{if !$item@last}
			<hr class="mt-1 mb-1">
		{/if}
	{/foreach}
		


			
		
		
<style>
{*
.wrapper {
  display: flex;
}
.cellim {
  flex: 0 0 200px;
}
.cellcont {
  flex: 1;
}	
*}
	

	
.wrapper {
  width: 100%;
  display: flex;
  padding: 10px;
  padding-top:15px;
  padding-bottom:15px;
  
  border-radius: 10px;
}

.eventlist .col-md-6{ padding-left: 5px; padding-right: 5px; }

.cellim {  flex: 0 0 200px;;margin-right:15px } 
.cellcont { flex: 1; } 

.:before {
  content:'';
  width:100%;
  height:100%;    
  position:absolute;
  left:0;
  top:0;
  background:linear-gradient(transparent 150px, white);
}
.event_description{
	max-height:200px;
    -webkit-mask-image: linear-gradient(180deg, #000 60%, transparent);
}
	
</style>