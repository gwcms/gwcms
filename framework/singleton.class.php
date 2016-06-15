<?php

trait Singleton
{

	/**
	 * @return GW_Data_Object 
	 */
	public static function singleton()
	{
		return GW::getInstance(get_called_class());
	}

	public static function singletonset($vals = [])
	{
		$o = GW::getInstance(get_called_class());


		foreach ($vals as $key => $val)
			$o->$key = $val;

		return $o;
	}
}
