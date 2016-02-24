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
define('GW_SYS_FILES_DIR', GW::s('DIR/SYS_FILES'));

class GW_File extends GW_Data_Object implements GW_Composite_Slave {

		var $table = 'gw_files';
		var $dir = GW_SYS_FILES_DIR;
		var $file_permissions = 0666;
		var $auto_validation = false;
		var $auto_fields = false;
		var $original_file = false; //used with resize
		var $ignore_fields = Array('new_file' => 1);
		public $calculate_fields = ['size_human' => 1, 'full_filename' => 1, 'extension' => 'getType'];
		var $validators = Array
			(
			'file' => Array
				(
				'size_max' => 50971520, //if greater - throw error
			//'allowed_extensions'=>'pdf,odt,doc', //example
			//'allowed_extensions'=>'', //if greater - throw error
			)
		);

		function calculateField($name) {

				if ($name == 'size_human')
						return GW_Math_Helper::cFileSize($this->size, $prec = 3);

				if ($name == 'full_filename')
						return $this->dir . $this->get('filename');

				parent::calculateField($name);
		}

		function getFilename() {
				return $this->dir . parent::get('filename');
		}

		function setFilename($value) {
				$this->set('filename', basename($value));
		}

		function getOwnerFormat($obj, $fieldname) {
				return get_class($obj) . '_' . $obj->get($obj->primary_fields[0]) . '_' . $fieldname;
		}

		function getByOwnerObject($master, $fieldname) {
				$this->setOwnerObject($master, $fieldname);
				return $this;
		}

		function getValue() {
				return $this->find(Array('owner=?', $this->owner));
		}

		function setOwnerObject($owner_obj, $fieldname) {
				$this->set('owner', self::getOwnerFormat($owner_obj, $fieldname));
		}

		function getType() {
				return strtolower(pathinfo($this->get('original_filename'), PATHINFO_EXTENSION));
		}

		function generateFileName() {
				return $this->get('id') . '_' . $this->get('owner') . '.' . $this->getType();
		}

		function save() {
				if (isset($this->new_file))
						return parent::save();
		}

		function validate() {

				GW_Validator::getErrors('gw_file', $this);


				//if(!parent::validate())
				//	return false;
				//
			
		return $this->errors ? false : true;
		}

		function storeFile() {
				$file = $this->dir . $this->generateFilename();

				if (file_exists($file) && !unlink($file))
						trigger_error('Can\'t delete old file "' . $file . '". Check permissions', E_USER_WARNING);

				$this->setFilename($file);

				copy($this->get('new_file'), $file);
				chmod($file, $this->file_permissions);

				if (!file_exists($file))
						trigger_error('Can\'t write file "' . $file . '". Check permissions', E_USER_ERROR);
		}

		function removeOld() {
				if ($item = $this->find(Array('owner=?', $this->get('owner'))))
						$item->delete();
		}

		function setParams($arr) {
				if ($arr && !is_array($arr)) {
						trigger_error('Invalid argument', E_USER_ERROR);
				} elseif ($arr) {
						$this->validators['file'] = $arr;
				}
		}

		function generateKey() {
				$this->key = md5($this->owner . $this->id);
		}

		private $after_save_done = false;

		function deleteComposite() {
				$this->removeOld();
		}

		function eventHandler($event, &$context_data = []) {
				switch ($event) {
						case 'BEFORE_DELETE':
								if (file_exists($fn = $this->getFilename()))
										@unlink($fn);
								break;

						case 'BEFORE_INSERT':
								$this->removeOld();
								break;

						case 'AFTER_INSERT':
								$this->after_save_done = true;
								$this->storeFile();
								$this->generateKey();

								$this->update(Array('filename', 'key'));
								break;
				}

				parent::eventHandler($event, $context_data);
		}

		function getIcon($source) {
				$filename = GW::s('DIR/APPLICATIONS') . strtolower(GW::$context->app->app_name) . '/' . $source . '/type_' . $this->extension . '.png';

				if (file_exists($filename))
						return 'type_' . $this->extension . '.png';

				return 'type_file.png';
		}

}
