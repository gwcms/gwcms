{$footerblocks=GW_Site_Block::singleton()->findAll('name="footerblocks" AND active=1')}
{foreach $footerblocks as $block}
	<!-- {$block->admnote} -->
	{$block->contents}
	<!-- End of {$block->admnote} -->	
{/foreach}
  

    <script src="{$app_root}assets/js/gw.js?v={$GLOBALS.version_short}"></script>
 	<script type="text/javascript">
	       $.extend(GW, { 
		       app_root:"{$app_root}", 
		       app_base:'{$app_base}', 
		       ln:'{$app->ln}', 
		       path:'{$app->path}', 
		       session_exp:"{$session_exp}", 
		       server_time:'{"F d, Y H:i:s"|date}', 
		       user_id:"{$app->user->id}",
		       pageid: "{$app->page->id}",
		       pagepid: "{$app->page->parent_id}",
		       assets_root: "{$assets}"
	       {foreach $js_vars as $key => $val}
		       , {$key}: "{addslashes($val)}"
	       {/foreach}		       
	       {if $app->isDebugMode()}, debugmode:1{/if}
       });
	</script>
        
    <script src="{$app_root}assets/js/voro.js?v={$GLOBALS.version_short}"></script>
    {include "gw/admin_func.tpl"}
    
	
	<!-- FOOTER BLOCKS -->
	{foreach $footer_hidden as $block}
		{$block}
	{/foreach}
	<!-- FOOTER BLOCKS -->


{if $m->includes}
	<!-- SYS INCLUDES -->
	{foreach $m->includes as $include}
		{if $include.0=='js'}
			<script type="text/javascript" src="{$include.1}"></script>
		{elseif $include.0=='css'}
			<link rel="stylesheet" type="text/css" href="{$include.1}" />
		{elseif $include.0=='jsstring'}			
			<script type="text/javascript">{$include.1}</script>
		{/if}
	{/foreach}
	<!-- /SYS INCLUDES -->
{/if}	

{if $doc_ready_js}
    <script>
      $(document).on('ready', function () {

	{foreach $doc_ready_js as $block}
		{$block};
	{/foreach}
	
      });
 	
 
    </script>
{/if}