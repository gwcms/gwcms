{if $smarty.session.messages}

{$classes=[0=>'sbrsucc', 1=>'sbrwarn', 2=>'sbrerror', 3=>'sbrinfo']}


{foreach $app->acceptMessages() as $field => $msg}
	{$msg_type_id=$msg.0}
	<div class="status_bx1 {$classes.$msg_type_id}" style="display:none" title="{$field}">
		{if !is_numeric($field)}<small>"<b>{$app->fh()->fieldTitle($field)}</b>" {$lang.ERROR}: </small> {/if}{GW::l($msg.1)}
	</div>
{/foreach}


<script type="text/javascript">
{literal}
$(document).ready(function() {
	$('.status_bx1').fadeIn("slow");
});
{/literal}

</script>


<br />

{/if}
