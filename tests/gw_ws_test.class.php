<?php


class gw_ws_test extends GW_TestClass
{
	
	function __construct($testclass) {
		$this->init();
	}
	
	function init()
	{
		GW::db();
	}
	
	
	function testGetConfig()
	{
		$info = [];
		$t = new GW_Timer;
		
		/**
		 * 1 gauti configa (updatinti temp pass)
		 * 2 prisijungti kaip brauzerio juzeris
		 * 3 prisijungti kaip control juzeris
		 * 4 pasiusti zinute brauzerio juzeriui
		 */
		
		
		$userObj = GW_User::singleton()->find('id=9');
		
		//d::dumpas($user);
		
		$config = GW_WebSocket_Helper::getFrontConfig($userObj, true);		
		
		//d::dumpas($config);
		
		
		$user = $config['user'];
		$pass = $config['apikey'];
		$host = $config['host'];
		$port = $config['port'];
		d::ldump($userObj->username);
		
		
		$client = new WebSocket\Client($uri="wss://$user:$pass@$host:$port/irc");
			
		$received = false;
		
		$client->registerEvent('incoming_messageprivate', function($data) use (&$received)  {
			//print_r(['new message'=>$data]);
			$received = $data;

		});
		
		
		
		$client->__fastConnect([]);
		
		$info['client_connect'] = $t->stop();
			
		
		$this->assertEquals($client->is_connected, true);
		

		//4
		GW_WebSocket_Helper::notifyUser($userObj->username, $testmesgage = "Testine zinute".date('Y-m-d h:i:s'));
		
		$info['control_user_message_sent'] = $t->stop();
		
		
		$i=0;
		while($i<100)
		{
			$client->heartBeat();
			
			$i++;
			
			if($received)
			{
				$info['received_message'] = $t->stop();
				$this->assertEquals($received['data'], json_encode(['text'=>$testmesgage]));
				break;
			}
			
			usleep(10000);
		}
		
		
		
		print_r($info);
		
	}
	
	function initControlUser()
	{
		return GW_WebSocket_Helper::initControlUserWsc();
	}
	
	
}