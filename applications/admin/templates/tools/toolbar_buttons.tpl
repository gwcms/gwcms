{function name=do_toolbar_buttons_addnew}
	{toolbar_button title=$lang.CREATE_NEW iconclass='gwico-Plus' href=$m->buildUri('form',[id=>0])}
	
{/function}

{function name=do_toolbar_buttons_filters}
		{*toolbar_button title=$lang.SEARCH iconclass='gwico-SearchFilled' onclick="$('#filters').toggle();" toggle=1*}
{/function}	

{function name=do_toolbar_buttons_print}
	
	{toolbar_button title=$lang.PRINT_VIEW iconclass='gwico-Print-Filled' href=$m->buildUri(false,[print_view=>1],[carry_params=>1])}
{/function}


{function name=do_toolbar_buttons_info}
	{if $page->notes}
		{toolbar_button title="`$lang.ABOUT` `$page->title`" iconclass='gwico-Info' onclick="open_notes(`$page->id`)" }
	{/if}
{/function}

{function name=do_toolbar_buttons_dialogconf}
	{toolbar_button title=$lang.LIST_DISPLAY_SETTINGS iconclass='gwico-Vertical-Settings-Mixer' onclick="lds_config(this);" }
	{capture append="footer_hidden"}
	<script type="text/javascript">
		function lds_config(obj)
		{
			gwcms.open_dialog2({ url: '{$m->buildURI('dialogconfig')}', iframe:1, title:$(obj).text() })
		}
	</script>
	{/capture}
{/function}	


{function name=do_toolbar_buttons_addinlist}
	{toolbar_button title=$lang.CREATE_NEW iconclass='gwico-Plus' onclick="gwToogleAdd($(this).hasClass('active'));" toggle=1}

	{capture append="footer_hidden"}
	<script type="text/javascript">
		function gwToogleAdd(active){

			if($('#iframebeforelist').length == 0){
				$('#additemscontainer').html('<iframe id="iframebeforelist" src="" style="width:800px;margin-top:5px;margin-bottom:20px;" frameborder="0"></iframe>');
				
				$('#iframebeforelist').contents().find('body').html('Loading...');
				
			}

			$('#additemscontainer').toggle();

			if(!active){
				gwcms.initAutoresizeIframe('#iframebeforelist', false, function(){
					$('#iframebeforelist').attr('src','{$m->buildUri('form',[id=>0,clean=>1,'RETURN_TO'=>$m->buildUri('iframeclose')])}');
				});
			}
			
			window.iframeClose = function(){
				$('#additemscontainer').hide();
				location.href = location.href;
			}
			
		}
	</script>
	{/capture}
{/function}

{function name=do_toolbar_buttons_hidden}

	
						<div class="btn-group">
						
					                            <a type="button" data-toggle="dropdown" class="gwtoolbarbtn btn btn-default btn-active-dark dropdown-toggle dropdown-toggle-icon">
					                                <i class="gwico-Menu-Filled"></i> 
					                            </a>
					                            <ul class="dropdown-menu">
			{foreach $do_toolbar_buttons_hidden as $button_func}
				{if $button_func=='divider'}
					<li class="divider"></li>
				{else}
					<li >{call name="do_toolbar_buttons_`$button_func`" indropdown=1}</li>
				{/if}
			{/foreach}
					                            </ul>
					                        </div>	
	
	
	{*
	<div class="unhideroot" style="">
		<img class="visible unhidetrigger" align="absmiddle" src="{$app->icon_root}action_down24.png">

		<div class="dropdown">
	
		</div>
	</div>

	*}
{/function}

{function name=do_toolbar_buttons_importdata} 
	{toolbar_button title=GW::l('/A/VIEWS/importdata') iconclass='gwico-Import' href=$m->buildUri(importdata)}
	
{/function}	
{function name=do_toolbar_buttons_exportdata} 
	{toolbar_button title=GW::l('/A/VIEWS/exportdata') iconclass='gwico-Export' href=$m->buildUri(exportdata)}
	
{/function}

{function name=do_toolbar_buttons_edit} 
	{toolbar_button title=GW::l('/A/VIEWS/edit') iconclass='gwico-Vertical-Settings-Mixer' href=$m->buildUri(edit)}
{/function}


{function name=do_display_toolbar_buttons}
	{if $do_toolbar_buttons}
		<div class="btn-group mar-rgt">
		{foreach $do_toolbar_buttons as $button_func}
			{call name="do_toolbar_buttons_`$button_func`"}
		{/foreach}	
		</div>
		{assign var=gw_toolbar_show value=1 scope=global}
	{/if}
{/function}
