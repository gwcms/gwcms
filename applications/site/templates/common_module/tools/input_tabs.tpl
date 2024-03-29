{*
default form plugin
input tabs

configure tabs by placing config array in form.tpl:

	{$input_tabs=[
		[base,blue,1], // id, color, display by default
		[contact,yellow,0]
	]}

for each input specity associations with tabs:

	{call e field=username tabs=[base]}
	{call e field=phone tabs=[contacts]}

add tab titles to lang.xml:
	<i id="INPUT_TABS">
		<i id="base">General info</i>
		<i id="contact">Contact info</i>
	</i>

*}


<div class="row panel gwlistpanel inputtabspan">
	<div class="panel-body">
		<ul class="inptabs_sel">
			{$showtabs=array_flip(explode(',',$smarty.get.activetabs))}
		{foreach $input_tabs as $tabitem}
			{$tabid=$tabitem.0}
			{$tabcolor=$tabitem.1}
			{if $smarty.get.activetabs}
				{$tabdefaultshow=isset($showtabs[$tabid])}
			{else}
				{$tabdefaultshow=$tabitem.2}
			{/if}
			

				<table class="inptabs_sel" style="margin-right:10px;">
					<tr>
						<td>
							<input id="tab-{$tabid}" class="tab-switch" data-color="{$tabcolor}" type="checkbox" value="{$tabid}" {if $tabdefaultshow}checked="checked"{/if} style="width:auto;height:auto">
						</td><td style="padding-left:5px;">
							<a href="#" data-id="tab-{$tabid}" class="tab-handler">{GW::ln("/m/INPUT_TABS/`$tabid`")}</a>
						</td>
					</tr>
				</table>



				<style>
					.tabitm_{$tabid}{ border-left:5px solid {$tabcolor}; {if !$tabdefaultshow}display:none{/if} }
					.inptabs_sel a:hover{ color:black }
				</style>
		{/foreach}
		</ul>
		<style>
			.inptabs_sel{  display: inline; }
		</style>
			<link rel="stylesheet" href="{$app_root}static/vendor/switchery/switchery.css" />
			<script>
				function updateTabs()
				{
					var activetabs = [];
					$('.tab-switch:not(:checked)').each(function(){
						$('.tabitm_'+this.value).hide();
						$(this).next('.switchery').find('small').css('border-color', $(this).data('color'))
					})
					
					$('.tab-switch:checked').each(function(){
						activetabs.push(this.value);
						$('.tabitm_'+this.value).fadeIn()
						$(this).next('.switchery').find('small').css('border-color', '#fff')
					})
					$('#activetabs').val(activetabs.join(','));
				}
				
				function setSwitchery(switchElement, checkedBool) {
					if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
						switchElement.setPosition(true);
						switchElement.handleOnchange(true);
					}
				}	
				
				function tabClick(currentid, leaveopen)
				{
					
					if(!leaveopen){
						$('.tab-switch').each(function(){
							setSwitchery($(this).data('switchery'), false)
						}) 
					}
					
					//$('.tab-switch').prop('checked',false);
					
					//$('.tab-switch').prop('checked',false);
					$('#'+currentid).click();
					
					
					
				}
				
				require(['gwcms'], function(){

					require(['vendor/switchery/switchery'], function(Switchery) {
						$( ".tab-switch" ).each(function() {
							 var testSwitchery = new Switchery(this, { color: $(this).data('color'), jackSecondaryColor:$(this).data('color')   });
							 $(this).data('switchery', testSwitchery);
						}).change(function(){
							updateTabs()
						});
						
						$('.tab-handler').click(function(e){
							tabClick($(this).data('id'), e.shiftKey);
							
							e.preventDefault();
						})
							
						updateTabs();
					});						

				})


			</script>			
			<input id="activetabs" name="activetabs" type="hidden" value="" data-ignorechanges="1">
	</div>
</div>