<div class="smallnum">
<input 
	name="{$input_name}" 
	type="number" 
	class="{$addclass}"
	id="{$id}" 
	value="{intval($value)}" 
	{if isset($min)}min="{$min}"{/if}
	{if isset($max)}max="{$max}"{/if}
	style="display:none"
>

<span class="smallnumShow"></span>
<i class='fa fa-plus-circle smallnumbtn smallnuminc'></i>
<i class='fa fa-minus-circle smallnumbtn smallnumdec'></i>

{if $max && $showmax}<small class="text-muted">(max {$max})</small>{/if}

</div>
	
	
	
	
	
	
{if !isset(GW::$globals.smallnuminitdone)}
	{$GLOBALS.smallnuminitdone=1}
	<script>
		$(function(){
			$('.smallnuminc').click(function(){
				if($(this).hasClass('disabled') || $(this).hasClass('disabled2'))
					return false;

				var inp=$(this).parents('.smallnum:first').find('input')

				if(inp.val()-0 < inp.attr('max')-0){
					inp.val(inp.val()-0+1);
					inp.change();
				}
			});
			$('.smallnumdec').click(function(){
				if($(this).hasClass('disabled') || $(this).hasClass('disabled2'))
					return false;

				var inp=$(this).parents('.smallnum:first').find('input')

				if(inp.val()-0 > inp.attr('min')-0){
					inp.val(inp.val()-0-1);
					inp.change();
				}
			});				

			$('.smallnum input').change(function(){
				var parent=$(this).parents('.smallnum:first')
				var out=parent.find('.smallnumShow').text(this.value)

				var inp = $(this)
				var val = inp.val()-0;
				var max = inp.attr('max')-0;
				var min = inp.attr('min')-0;

				var isincenabled = val < max;
				var isdecenabled = val > min;

				//console.log([min, val, max, isincenabled, isdecenabled])

				parent.find('.smallnuminc').toggleClass('disabled', !isincenabled)
				parent.find('.smallnumdec').toggleClass('disabled', !isdecenabled)



			}).change();
		});


	</script>
	<style>
		.smallnumbtn{ 
			cursor: pointer; opacity: 0.6;font-size:25px;
			-moz-user-select: none;
			 -khtml-user-select: none;
			 -webkit-user-select: none;
			 user-select: none;				
		}
		.smallnum .disabled, .smallnum .disabled2{ color:silver; opacity:1 }
		.smallnumbtn:hover{ opacity: 1; }
		.smallnuminc{ color: green;  }
		.smallnumdec{ color: #bb0000; }
		.smallnumShow{ font-size: 23px; margin-right: 5px; }

	</style>		
{/if}	
