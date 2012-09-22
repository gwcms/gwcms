{include file="default_open.tpl"}

{php}
	include GW::$dir['MODULES'].'/gallery/gw_gallery_item.class.php';
	
	$gallery_folder_id=GW::$request->page->getContent('gallery_folder_id');

	$item0 = new GW_Gallery_Item($gallery_folder_id);
	$list = $item0->getChilds(Array('type'=>GW_GALLERY_ITEM_IMAGE, 'active'=>1));
	$smarty->assign('list', $list);
{/php}

<div id="scroller_container">
	
	<img class="hide" id="left_arrow" src="images/left.png" alt="Scroll left" title="Scroll left" />					
	<div id="image_scroller">
		<ul id="thumbnails_container" class="thumbnails_unstyled">
							
							
{foreach $list as $item}
	{$img=$item->image}
	<li class="{if $item@first}active{/if}">
	<a href="tools/img.php?id={$img->id}&size=1000x500" title="">
		<img src="tools/img.php?id={$img->id}&size=90x90&method=crop" alt="" title="" style="" rel="" />
	</a>
	</li>
{/foreach}								
					
							
		</ul>
		<div class="clear"></div>
	</div>					
	<img class="hide" id="right_arrow" src="images/right.png" alt="Scroll right" title="Scroll right" />
	
	<div class="clear"></div>
</div>
<div class="clear"></div>
<div id="middle_container">
	<div id="main_image"></div>
</div>



<div id="jalbumwidgetcontainer"></div>
<script type="text/javascript" charset="utf-8"><!--//--><![CDATA[//><!--
{literal}
_jaSkin = "Galleria";
_jaStyle = "Dark.css";
_jaVersion = "8.5.3";
_jaLanguage = "en";
_jaPageType = "index";
_jaRootPath = ".";
_jaUserName = "vrgrtssd";
var script = document.createElement("script");
script.type = "text/javascript";
script.src = "http://jalbum.net/widgetapi/load.js";
document.getElementById("jalbumwidgetcontainer").appendChild(script);
//--><!]]>
{/literal}
</script>
			
{include file="default_close.tpl"}