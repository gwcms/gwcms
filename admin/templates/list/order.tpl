{if !$title}
	{$title=FH::fieldTitle($name)}
{/if}


{php}
	$vars = FH::getTplVars($template,Array('m','name'));
	$order = $vars['m']->list_params['order'];
	
	$variants1=Array('desc','asc');
	
	foreach(explode(',', $vars['name']) as $name)
	{
		$variants[0].=($variants[0]?',':'')."$name ASC";
		$variants[1].=($variants[1]?',':'')."$name DESC";
	}

	$param = $variants[$tmp = intval(strpos($order, 'DESC')===false)];
	$curr_dir = $variants1[$tmp];
	
	$template->assign('order', Array
	(
		'uri'=> Navigator::buildURI(false, Array('list_params' => Array('order'=>$param) ) ),
		'current'=>in_array($order, $variants) ? $curr_dir : false
	));
{/php}

{if $order.current}<img style="padding: 2px" src="img/icons/order_{$order.current}.png" align="absmiddle" onclick="$(this).next().click()" />{/if}
<a href="{$order.uri}" {if $order.current}style="font-weight:bold"{/if}>{$title}</a>