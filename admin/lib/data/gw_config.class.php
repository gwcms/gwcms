<?php

class GW_Config
{
	public $prefix;
	var $table='gw_config';

	function __construct($prefix='')
	{
		$this->prefix=$prefix;
	}

	function &getDB()
	{
		return GW::$db;
	}

	function set($key,$value)
	{
		$db =& $this->getDB();
		$db->save($this->table,Array('id'=>$this->prefix.$key,'value'=>$value));
	}

	function get($key,&$time=0)
	{
		$db =& $this->getDB();

		$key=addslashes(substr($this->prefix.$key,0,50));
		$rez = $db->fetch_row("SELECT * FROM {$this->table} WHERE id='$key'");
		$time = $rez['time'];
		return $rez['value'];
	}

	function setValues($vals)
	{
		foreach($vals as $key => $val)
			$this->set($key, $val);
	}	
	
	function getAge($key)
	{
		return time()-strtotime($this->getTime($key));
	}
	
	function getTime($key)
	{
		$this->get($key,$time);
		return $time;
	}

	function __set($key,$value)
	{
		return $this->set($key,$value);
	}
	
	function __get($key)
	{
		return $this->get($key);
	}
	
	
	//magic methods
	
    private static $instance;

    public static function singleton()
    {
        if (!isset(self::$instance)) 
        {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        
        return self::$instance;
    }

    public static function __callStatic($name, $arguments) 
    {
       	if( stripos($name, 'static') !== false)
    	{    		
    		//Example call GW_Config::setStatic('key','value');
    		//will work same as $o = GW_Config::singleton(); $o->set('key','value')
    		
    		$func = str_ireplace('static','',$name);

	    		    		    	
	    	return call_user_func_array(Array( self::singleton() ,$func), $arguments);   		
    	}
    	else
    	{
    		trigger_error("Unhandled static call", E_USER_ERROR);
    	}
        
    }    
    
}