{if $smarty.session.messages}

{$classes=[0=>'sbrsucc', 1=>'sbrwarn', 2=>'sbrerror', 3=>'sbrinfo']}


{foreach $smarty.session.messages as $msg}
	{$msg_type_id=$msg.0}
	<div class="status_bx1 {$classes.$msg_type_id}" style="display:none">
		{GW_Error_Message::read($msg.1)}
	</div>
{/foreach}


<script>
{literal}
$(document).ready(function() {
	$('.status_bx1').fadeIn("slow");
});
{/literal}

</script>

{GW::$request->removeMessages()}

<br />

{/if}