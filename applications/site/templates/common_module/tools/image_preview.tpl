{capture name=info}{GW::ln('/g/DIMENSIONS')}: {$image->width}x{$image->height}, {GW::ln('/g/FILE_SIZE')}: {GW_Math_Helper::cfilesize($image->size)} {if $show_filename}{$image->original_filename}{/if}{/capture}

<table>
	<tr>
	<td>
<a href="{$app->sys_base}tools/img/{$image->key}?{$item->v}&x=file.jpg" {if $fancybox}class="fancybox-thumbs" data-fancybox-group="{$fancybox_group}"{/if} target="_blank">
	<img title="{$smarty.capture.info|escape}" src="{$app->sys_base}tools/img/{$image->key}?size={$width}x{$height}&v={$image->v}&x=file.jpg" border="{$border|default:0}" />
</a>	</td>
{if $in_form}
<td valign="top">{include "elements/zz_remove_composite.tpl"}</td>
<td valign="top" style="padding-left:5px">
	{include "list/actions.tpl"}
	{call dl_actions_ext_actions item=$image modpath="datasources/images" argadd=[frompreview=>1]}
	
	
	
	
	</td>	
{/if}	
	
	</tr>
</table>



{if $fancybox}
	{if !$gwcms_fancybox_initdone}
		
	
	{*<script type="text/javascript" src="{$app->sys_base}vendor/fancybox/lib/jquery-1.10.1.min.js"></script>*}

	<!-- Add fancyBox main JS and CSS files -->

	<link rel="stylesheet" type="text/css" href="{$app_root}static/vendor/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
	<!-- Add Thumbnail helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="{$app_root}static/vendor/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />

		
		
		<script type="text/javascript">
			
			function initFancy()
			{
					$('.fancybox-thumbs').fancybox({
							prevEffect: 'fade',
							nextEffect: 'fade',
							closeBtn: false,
							arrows: true,
							nextClick: true,
							helpers: {
									thumbs: {
											width: 50,
											height: 50
									}
							}
					});
					//$('.fancybox').fancybox();	
			}

			//this will allow open dialog in root window, if this window is iframed
			require(['gwcms'], function(){   
				require(['vendor/fancybox/lib/jquery.mousewheel-3.0.6.pack', 'vendor/fancybox/source/jquery.fancybox'], function(){ 
					require(['vendor/fancybox/source/helpers/jquery.fancybox-thumbs'], function(){ initFancy() })
				})	
			});
			
		</script>
		{assign var=gwcms_fancybox_initdone value=1 scope=global}
	{/if}	
{/if}