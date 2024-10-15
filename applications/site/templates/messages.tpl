{*ant kaikuriu klientu vientisas dizainas, jei reikia pagal projekta det i css #msgDrop{ padding-top:5px }"*}
<div id="msgDrop" style=""></div>
{$tmp=$app->acceptMessages()}


{if $tmp}

{$classes=[0=>'alert-success', 1=>'alert-warning', 2=>'alert-danger', 3=>'alert-info']}


{foreach $tmp as $field => $msg}
	{$msg_type_id=$msg.type}
	
<div class="alert {$classes[$msg_type_id]} alert-dismissible fade show" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">×</span>
                        </button>
                        <p style="margin-bottom:0"> 
				{if $msg_type_id==0}<i class="fa fa-check-circle-o"></i> 
				{elseif $msg_type_id==2}
					<i class="fa fa-exclamation-circle"></i> 
				{/if}
				{if $msg.field}
					<span style="border:1px solid silver;padding:2px; border-radius:3px;background-color:#fff;margin-right:5px;">
						{$msg.field_title}
					</span>
				{/if}
				{GW::ln($msg.text)}
				{if $msg.html}{$msg.html}{/if}
			</p>
			{if $msg.buttons}
				<div class="g-pt-5">
				{foreach $msg.buttons as $btn}
					
					<a href="{$btn.url}" class="btn u-btn-darkgray btn-xs rounded-0">
						{if $btn.title}
							{$btn.title}
						{else}
							<i class="fa fa-cog g-mr-2"></i>
						{/if}</a>
				{/foreach}
				</div>
			{/if}
                        
                        
                      </div>	
	

{/foreach}


<script type="text/javascript">
{literal}
$(document).ready(function() {
	//$('.alert').fadeIn("slow");
});
{/literal}

</script>


<br />

{/if}
