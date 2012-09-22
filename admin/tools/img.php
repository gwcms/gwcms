<?

if(class_exists("GW") && GW::$public){
	//
}else{
	include dirname(__DIR__).'/init.php';
	include GW::$dir['ADMIN'].'init_auth.php';
	
	if(!GW::$user)
		die('Please login to view this content');

}

$params=$_GET;
$item0 = new GW_Image();

$condition=GW::$public?'`key`=?':'id=?';

if(isset($_REQUEST['f']) && $_REQUEST['f'] == '1'){
    $condition='`key`=?';
}

$item=$item0->find(Array($condition,$params['id']));


if(!$item)
	die('File doesn\'t exist');
	

if($params['size'])
{
	$params+=GW_Image::parseDimensions($params['size']);
	unset($params['size']);
}

	
if($params['width'] || $params['height'])
	$item->resize($params);


GW_Cache_Control::setExpires('+24 hour');
//GW_Cache_Control::checkFile($item->getFilename());

if($params['debug'])
{
	dump(Array
	(
		'params' => $params,
		'item' => $item,
		'cache_files'=> $item->getCacheFiles()
	));
	exit;
}	

	
if($_REQUEST['download']){
	header("Content-Type: application/x-download");	
	header('Content-Disposition: attachment; filename="'.$item->get('original_filename').'";');
	header("Accept-Ranges: bytes");
	header("Content-Length: ".$item->get('size'));
}else{
	header("Content-Type: ". Mime_Type_Helper::getByFilename($item->get('original_filename')) );	
}

readfile($item->getFilename());
?>