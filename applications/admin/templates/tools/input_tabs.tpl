{*
default form plugin
input tabs

configure tabs by placing config array in form.tpl:

	{$input_tabs=[
		[base,blue,1], // id, color, display by default
		[contact,yellow,0]
	]}

for each input specity associations with tabs:

	{include file="elements/input.tpl" name=username tabs=[base]}
	{include file="elements/input.tpl" name=phone tabs=[contacts]}

add tab titles to lang.xml:
	<i id="INPUT_TABS">
		<i id="base">General info</i>
		<i id="contact">Contact info</i>
	</i>

*}


<div class="row panel gwlistpanel">
	<div class="panel-body">
		<ul class="inptabs_sel">
		{foreach $input_tabs as $tabitem}
			{$tabid=$tabitem.0}
			{$tabcolor=$tabitem.1}
			{$tabdefaultshow=$tabitem.2}

				<table class="inptabs_sel" style="margin-right:10px;">
					<tr>
						<td>
							<input class="tab-switch" data-color="{$tabcolor}" type="checkbox" value="{$tabid}" {if $tabdefaultshow}checked="checked"{/if} style="width:auto;height:auto">
						</td><td style="padding-left:5px;">
							{GW::l("/m/INPUT_TABS/`$tabid`")}
						</td>
					</tr>
				</table>



				<style>.tabitm_{$tabid}{ border-left:5px solid {$tabcolor}; {if !$tabdefaultshow}display:none{/if} }</style>
		{/foreach}
		</ul>
		<style>
			.inptabs_sel{  display: inline; }
		</style>
			<link rel="stylesheet" href="{$app_root}static/vendor/switchery/switchery.css" />
			<script>
				function updateTabs()
				{
					$('.tab-switch:not(:checked)').each(function(){
						$('.tabitm_'+this.value).hide();
						$(this).next('.switchery').find('small').css('border-color', $(this).data('color'))
						
					})
					$('.tab-switch:checked').each(function(){
						$('.tabitm_'+this.value).fadeIn()
						$(this).next('.switchery').find('small').css('border-color', '#fff')
					})
				}
				
				require(['gwcms'], function(){

					require(['vendor/switchery/switchery'], function(Switchery) {




						$( ".tab-switch" ).each(function() {
							 var testSwitchery = new Switchery(this, { color: $(this).data('color'), jackSecondaryColor:$(this).data('color')   });
						}).change(function(){
							updateTabs()
						});
							
						updateTabs();
					});						

				})


			</script>			

	</div>
</div>