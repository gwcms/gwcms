<?php

class GW_Time_Helper
{
	
	static function round2secs($time, $step_secs=10)
	{
		list($mins,$secs) = explode(':',  self::leadingZeros($time));
		
		return self::leadingZeros($mins.":".round($secs/$step_secs)*$step_secs);
	}
	
	static function leadingZeros($time)
	{
		list($mins, $secs) = explode(':', $time);
		return sprintf('%02d', $mins).':'.sprintf('%02d', $secs);
	}
}