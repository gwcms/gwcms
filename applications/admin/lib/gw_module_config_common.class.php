<?php

class GW_Module_Config_Common extends GW_Common_Module
{
	public $default_view = 'default';

	protected function getConfigModel()
	{
		return $this->model;
	}

	protected function getConfigViewItem()
	{
		return $this->getConfigModel();
	}

	protected function normalizeConfigValues(&$vals)
	{
		foreach ($vals as $key => $val) {
			if (is_array($val)) {
				$vals[$key] = json_encode($val);
			}
		}
	}

	protected function beforeConfigSave(&$vals)
	{
		$this->fireEvent("BEFORE_SAVE", $vals);
	}

	protected function persistConfigValues($vals)
	{
		$this->saveConfigTracked($this->getConfigModel(), $vals);
	}

	protected function afterConfigSave(&$vals)
	{
		$this->fireEvent("AFTER_SAVE", $this->getConfigModel());

		if (method_exists($this, '__afterSave')) {
			$this->__afterSave($vals);
		}
	}

	protected function notifyConfigSaveSuccess()
	{
		$this->setPlainMessage('/g/SAVE_SUCCESS');
	}

	function viewDefault()
	{
		$this->initConfigChangeTrack($this->getConfigModel());
		return ['item' => $this->getConfigViewItem()];
	}

	function doSave()
	{
		$vals = $_REQUEST['item'];

		$this->normalizeConfigValues($vals);
		$this->beforeConfigSave($vals);
		$this->persistConfigValues($vals);
		$this->notifyConfigSaveSuccess();
		$this->afterConfigSave($vals);
		$this->jump();
	}
}
