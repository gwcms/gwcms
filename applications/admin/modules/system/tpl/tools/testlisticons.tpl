{$do_toolbar_buttons=[listicons]}


{include "default_open.tpl"}

{function do_toolbar_buttons_listicons}
	{toolbar_button toggle=1 onclick="$('#parsepanel').toggle()" title="parse html" iconclass="gwico-Process-Filled" size=2}

	<button class="gwtoolbarbtn btn btn-default btn-active-success add-popover" 
			data-toggle="popover" data-container="body" data-placement="left" 
			data-original-title="Separate svg images to single font file" 
			data-content="This tool exports parsed svg content to separate files; stores them in /tmp/icons directory, upload files to https://icomoon.io/app/#/select export as name gwcms / class prefix gwico-, extract files to applications/admin/static/fonts/gwcms">
		<i class='fa fa-info-circle'></i>
	</button>
{/function}





<div id="parsepanel" class="panel" style="display: none">

	<!--No Label Form-->
	<!--===================================================-->
	<form class="form-horizontal" action="{$smarty.server.REQUEST_URI}" method="post">
		<input type="hidden" name="act" value="do:parseIcons">
		<div class="panel-body">
			<textarea class="form-control" rows="13" placeholder="html" name="data"></textarea>
		</div>
		<div class="panel-footer">
			<button class="btn btn-default">Submit</button>
		</div>
	</form>
	<!--===================================================-->
	<!--End No Label Form-->
</div>





<div class="panel">
	<div  style="padding:5px">
		{foreach [100,150,200,300,400] as $perc}
			<a class="btn {if $smarty.get.fontsize==$perc}btn-default{else}btn-primary{/if}" href="{$m->buildUri(TestListIcons, [fontsize=>$perc])}">{$perc}%</a>
		{/foreach}
	</div>
	<div class="panel-body">
	
		{capture append=footer_hidden}
		<style>
			{if $smarty.get.fontsize}.testicoc .gwtoolbarbtn i{ font-size: {$smarty.get.fontsize}% } {/if}
			.testicoc .gwtoolbarbtn{ margin-bottom:2px; color: #000; }
		</style>
		<script>



			$(function () {
				$('.testicoc a').each(function () {
					$(this).click(function () {
						GW.copy_to_clipboard($(this).find('span').text());
						//gw_adm_sys.notify('success', $(this).find('span').text() + ' copied to clipboard');
						
						$.niftyNoty({
							type: 'dark',
							title: 'Copied to clipboard',
							message: $(this).find('span').text() + ' copied to clipboard',
							container: 'floating',
							timer: 5000
						});
						
					})
				})
			}
			);
		</script>
		{/capture}


		<div class="testicoc">
		{foreach $list as $itm}
				{toolbar_button toggle=1 title=$itm iconclass=$itm size=2}
		{/foreach}
		</div>



	</div>
		
		<div class="panel-footer">

			Total: {count($list)}


		</div>		
</div>

{include "default_close.tpl"}