<?php
/**
 * used as GW_Composite_Data_Object attachment
 * 
 * Attachment object spec:
 * 
 * used methods:

 * setParams, setValues, save, getByOwnerObject, setOwnerObject
 * 
 * field 'owner' must be unique
 * 
 */
define('GW_SYS_IMAGES_DIR', GW::s('DIR/SYS_IMAGES'));

class GW_Image extends GW_Data_Object implements GW_Composite_Slave
{

	public $table = 'gw_images';
	public $dir = GW_SYS_IMAGES_DIR;
	public $file_permissions = 0666;
	public $auto_validation = false;
	public $auto_fields = false;
	public $original_file = false; //used with resize
	public $ignore_fields = Array('new_file' => 1);
	public $validators = Array
	    (
	    'image_file' => Array
		(
		'dimensions_min' => '0x0', //if smaller - throw error
		'dimensions_max' => '999999x999999', //if greater - throw error
		'dimensions_resize' => '10000x10000', //if greater - resize
		'size_max' => 20971520, //if greater - throw error
	    )
	);
	public $original;
	public $calculate_fields = ['size_human' => 1, 'full_filename' => 1, 'extension' => 'getType','dimensions'=>1];	

	function calculateField($name)
	{
		if ($name == 'size_human')
			return GW_Math_Helper::cFileSize($this->size, $prec = 3);

		if ($name == 'full_filename')
			return $this->dir . $this->get('filename');
		if($name == 'dimensions')
			return "{$this->width}x{$this->height}";

		parent::calculateField($name);
	}	
	
	function getFilename()
	{
		return $this->dir . parent::get('filename');
	}
	
	function fileExists()
	{
		return file_exists($this->getFilename());
	}

	function setFilename($value)
	{
		$this->set('filename', basename($value));
	}

	function getOwnerFormat($obj, $fieldname)
	{
		return get_class($obj) . '_' . $obj->get($obj->primary_fields[0]) . '_' . $fieldname;
	}

	function getByOwnerObject($master, $fieldname)
	{
		$this->setOwnerObject($master, $fieldname);
		return $this;
	}

	function getValue()
	{
		if(isset(GW_Composite_Data_Object::$linked_cache['GW_Image'][$this->owner])){
			return GW_Composite_Data_Object::$linked_cache['GW_Image'][$this->owner];
		}
		
		$item = $this->find(Array('owner=?', $this->owner));
		
		GW_Composite_Data_Object::$linked_cache['GW_Image'][$this->owner] = $item;
		
		return $item;
		
	}

	function setOwnerObject($owner_obj, $fieldname)
	{
		$this->set('owner', self::getOwnerFormat($owner_obj, $fieldname));
	}

	function getType()
	{
		return strtolower(pathinfo($this->get('original_filename'), PATHINFO_EXTENSION));
	}

	function generateFileName()
	{
		return $this->get('id') . '_' . $this->get('owner') . '.' . $this->getType();
	}

	function prepare()
	{
		//get image dimensions
		list($width, $height, $type) = @getimagesize($this->get('new_file'));

		$this->set('width', $width);
		$this->set('height', $height);
	}

	function validate()
	{
		$this->prepare();

		GW_Validator::getErrors('gw_image', $this);


		//if(!parent::validate())
		//	return false;
		//
			
		return $this->errors ? false : true;
	}

	function storeResize()
	{
		if (!isset($this->validators['image_file']['dimensions_resize']))
			return false;

		$size = $this->validators['image_file']['dimensions_resize'];

		$params = self::parseDimensions($size);

		GW_Image_Resize_Helper::resize($this, $params, $this->getFilename());
	}

	function storeFile()
	{
		$file = $this->dir . $this->generateFilename();

		if (file_exists($file) && !unlink($file))
			trigger_error('Can\'t delete old file "' . $file . '". Check permissions', E_USER_WARNING);

		$this->setFilename($file);

		copy($this->get('new_file'), $file);
		chmod($file, $this->file_permissions);

		if (!file_exists($file))
			trigger_error('Can\'t write file "' . $file . '". Check permissions', E_USER_ERROR);

		$this->storeResize();
	}

	/**
	 * $arguments 
	 * Array(
	 * 		width
	 * 		height
	 * 		method
	 * )
	 * if empty then returns original
	 */
	function resize($params)
	{
		$this->original_file = $this->getFileName();

		GW_Image_Resize_Helper::resizeAndCache($this, $params);
	}

	function getCacheFiles()
	{
		return GW_Image_Resize_Helper::getCacheFiles($this);
	}

	function removeOld()
	{
		if ($item = $this->find(Array('owner=?', $this->get('owner'))))
			$item->delete();
	}

	function setParams($arr)
	{
		if (!is_array($arr))
			trigger_error('Invalid argument', E_USER_ERROR);

		$this->validators['image_file'] = $arr;
	}

	static function parseDimensions($str)
	{
		$dim = Array();
		$str = explode('x', $str);

		if ($str[0] != '')
			$dim['width'] = (int) $str[0];

		if ($str[1] != '')
			$dim['height'] = (int) $str[1];

		return $dim;
	}

	function generateKey()
	{
		$this->key = md5($this->owner . $this->id);
	}

	private $after_save_done = false;

	function deleteComposite($id='*')
	{
		$this->removeOld();
	}

	function save()
	{
		if (isset($this->new_file))
			return parent::save();
	}

	function deleteCached()
	{
		GW_Image_Resize_Helper::deleteCached($this);
	}

	function rotate($left)
	{
		$file = $this->getFilename();
		$im = new GW_Image_Manipulation($file);
		$im->rotateSelf($left ? 90 : 270);
		$im->clean();

		$this->deleteCached();

		$this->saveValues(['v' => $this->v + 1]); //update file version
	}
	
	function cropSelf($opts)
	{
		$file = $this->getFilename();
		$im = new GW_Image_Manipulation($file);
		$im->cropSelf($opts);
		$im->clean();

		$this->deleteCached();

		$this->saveValues(['v' => $this->v + 1]); //update file version
	}	

	function eventHandler($event, &$context_data = [])
	{
		switch ($event) {
			case 'BEFORE_DELETE':
				if (file_exists($fn = $this->getFilename()))
					@unlink($fn);
				$this->deleteCached();
				break;

			case 'BEFORE_INSERT':
				$this->removeOld();
				break;

			case 'AFTER_INSERT':
				$this->after_save_done = true;
				$this->storeFile();
				$this->generateKey();

				$this->update(Array('filename', 'width', 'height', 'size', 'key'));
				break;
		}

		parent::eventHandler($event, $context_data);
	}
}
