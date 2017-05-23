<?php

class GW_Timer
{

	var $start;
	var $time_sum = 0;

	function __construct()
	{
		$this->start = microtime(1);
	}

	function start()
	{
		$this->start = microtime(1);
	}

	function pause()
	{
		$this->time_sum += microtime(1) - $this->start;
	}

	function result($precision = 2)
	{
		return sprintf('%01.' . (int) $precision . 'f', $this->time_sum);
	}

	function stop($precision = 2)
	{
		$this->pause();
		$this->start();
		return $this->result($precision);
	}
}
