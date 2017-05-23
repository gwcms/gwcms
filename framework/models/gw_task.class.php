<?php

//running > 0 - pid
//running = 601 // sistema identifikavo proceso luzima
//running = -1 // paruosti vykdimui
//running = -2 // paimti vykdymui


class GW_Task extends GW_Data_Object
{

	var $table = 'gw_tasks';
	var $encode_fields = Array('arguments' => 'json');
	var $calculate_fields = Array('list_color' => 'listColor');

	function getForExecution()
	{
		$list = $this->findAll('time < "' . date('Y-m-d H:i:s') . '" AND running = -1');

		foreach ($list as $item) {
			$item->running = -2;
			$item->update(Array('running'));
		}

		return $list;
	}

	function canSingleInstanceRun()
	{
		return $this->count(Array('name=? AND (running =-1 OR running > 0) AND id!=?', $this->name, $this->id)) == 0;
	}

	function add($name, $args = Array())
	{
		$item = $this->createNewObject();
		$item->set('name', $name);
		$item->set('arguments', $args);
		$item->set('time', date('y-m-d H:i:s'));

		$item->setAsNewest();

		$item->insert();



		GW_App_System::triggerUSR1();
	}

	function addSingle($name, $args = Array())
	{
		if (!$this->count(Array("name=? AND running=-1", $name)))
			$this->add($name, $args);
		else
			GW_App_System::triggerUSR1();
	}

	function isRunning()
	{
		return $this->running > 0;
	}

	function checkRunning()
	{
		if ($this->isRunning())
			return GW_Proc_Ctrl::isRunning($this->running, 'task.php');
	}

	function procKill($hard = false)
	{
		if ($this->checkRunning())
			posix_kill($this->running, $hard ? 9 : 15);
	}

	function listColor()
	{
		if ($this->isRunning())
			return $this->checkRunning() ? '#dfd' : '#fdd';
	}

	function getOverTimeLimit()
	{
		return $this->findAll('running!=0 AND halt_time>time AND halt_time<"' . date('Y-m-d H:i:s') . '"');
	}

	function setAsNewest()
	{
		$this->getDB()->update($this->table, ['`name`=?', $this->name], ['newest' => 0]);
		$this->newest = 1;
	}
}
