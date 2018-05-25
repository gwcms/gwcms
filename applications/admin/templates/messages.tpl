{$tmp=$app->acceptMessages(1)}
{if $tmp}

	{$classes=[0=>'alert-success', 1=>'alert-warning', 2=>'alert-danger', 3=>'alert-info']}


	{foreach $tmp as $field => $msg}
		{if $msg.float}
			<script>require(['gwcms'],function(){ gw_adm_sys.notification({$msg|json_encode}) })</script>
		{else}
		<div class="alert {$classes[$msg.type]}" data-objid="{$msg.obj_id}" {if $msg.title}title="{$msg.title|escape}"{/if}>
			<button class="close" data-dismiss="alert"><i class="pci-cross pci-circle"></i></button>
			
			{if isset($msg.field)}<small>"<b>{$app->fh()->fieldTitle($msg.field)}</b>" {$lang.ERROR}: </small> {/if} {if is_array($msg.text)}<pre>{print_r($msg, true)}</pre>{else}{$msg.text}{/if}
		</div>
		{/if}
	{/foreach}


{/if}