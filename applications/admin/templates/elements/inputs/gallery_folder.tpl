
{$item0=GW::getInstance(GW_Gallery_Item,'modules/gallery/gw_gallery_item.class.php')}
{$folders=$item0->getFoldersTree([active=>1])}

{include file="elements/inputs/select.tpl" options=$folders}	

