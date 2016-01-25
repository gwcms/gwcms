{if !$title}
	{$title=$app->fh()->fieldTitle($name)}
{/if}


{php}
	$vars = FH::getTplVars($template,Array('m','name'));
	$order = $vars['m']->list_params['order'];
	$orders = explode(', ',$vars['m']->list_params['order']);
	$multiorder_index = 0;
	
	
	$variants1=Array('desc','asc');
	
	foreach(explode(',', $vars['name']) as $name)
	{
		$variants[0].=($variants[0]?',':'')."$name ASC";
		$variants[1].=($variants[1]?',':'')."$name DESC";
	}

	if($tmp=array_intersect($orders, $variants)) {
		foreach($tmp as $index => $ordercopy) {
			$multiorder_index = $index+1;
		}
	
		$order = $ordercopy;
	}

	$param = $variants[$tmp = intval(strpos($order, 'DESC')===false)];
	$curr_dir = $variants1[$tmp];
	

	$template->assign('order', 
	[
		'uri'=> Navigator::buildURI(false, ['act'=>'do:setOrder','order'=>$param] ),
		'current'=>in_array($order, $variants) ? $curr_dir : false,
		'multiorder'=>count($orders) > 1 ? $multiorder_index : false
	]);
{/php}

{if $smarty.get.print_view}
	{$title}
{else}
	{if $order.current}<img style="padding: 2px" src="{$app_root}img/icons/order_{$order.current}.png" align="absmiddle" onclick="$(this).next().click()" />{/if}
	<a href="{$order.uri}" {if $order.current}style="font-weight:bold"{/if}>{$title}{if $order.multiorder} ({$order.multiorder}){/if}</a>
{/if}