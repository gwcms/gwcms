{include_php file="modules/category.php"}

<div>
	<div id="categorimenu">
		<ul>
		{foreach $list as $item}
			{$name=$item->title}
			{$catID=$item->id}
			{$desc=$item->desc}
			<li><a href="{$request->ln}/{$name|lower}" title="{$desc}" alt="{$desc}"
			{if $request->path_arr[0]['name']|lower==$name|lower}
					class="active"
					{$types=$item->getTypes($catID)}
					{$activeCat=$item}
			{/if}
			{if $request->path_arr[0]['name']|lower=='start' && $name|lower=='hovedside'}
				class="active"
				{$types=$item->getTypes($catID)}
			{/if}
			>{$name}</a></li>
		{/foreach}
		</ul>
	</div>
	<div id="typesmenu">
		<div id="typeslinker">
			{if $request->path_arr[0]['name'] == 'start'}
    			{if !GW::$user}<a href="{$request->ln}/registrer">Registrer deg!</a>{/if}
    		{elseif $request->path_arr[0]['name'] == 'handlekurv'}
    			{$handlekurvenActive = true}
    		{elseif $request->path_arr[0]['name'] == 'bruker'}
    			<a href="{$request->ln}/bruker" {if !isset($request->path_arr[1])} class="active"{/if}>Bruker</a>
    			<a href="{$request->ln}/bruker/orders" {if $request->path_arr[1]['name']|lower == 'orders'} class="active"{/if}>Ordrers</a>
    			<a href="{$request->ln}/bruker/innstillinger" {if $request->path_arr[1]['name']|lower == 'innstillinger'} class="active"{/if}>Innstillinger</a>
    			<a href="{$request->ln}/bruker/passord" {if $request->path_arr[1]['name']|lower == 'passord'} class="active"{/if}>Passord</a>
    		{else}
    			<a href="{$request->ln}/{$request->path_arr[0]['name']}" title="{$activeCat->desc}" alt="{$activeCat->desc}"
    			{if $request->path_arr[1]['name']|lower == 'forside' || !isset($request->path_arr[1])}
    				class="active"
    			{/if}
    			>{$request->path_arr[0]['name']|capitalize}</a>
    			{foreach $types as $type}
    				{$name=$type['title']}
    				{$desc=$type['desc']}
  					<a href="{$request->ln}/{$activeCat->title|lower}/{$name|lower}" title="{$desc}" alt="{$desc}"
    				{if $request->path_arr[1]['name']|lower==$name|lower}
    					class="active"
    					{$activeType=$type}
    				{/if}
    				>{$name}</a>
				{/foreach}
    		{/if}
    	</div>
    	<div class="handlekurv"><a  href="{$request->ln}/handlekurv" {if isset($handlekurvenActive)} class=active{/if} title="Handlekurv">
    		{if GW::$user}{if $miniCartInfo['nr_products'] == '0'}Handlevognen er tom.{else}{$miniCartInfo['nr_items']} varer fra {$miniCartInfo['nr_products']} design, totalt {$miniCartInfo['sum']},-{/if}{else}Handlekurven er tom. Logg inn for å bruke den.{/if}</a></div>
	</div>
</div>