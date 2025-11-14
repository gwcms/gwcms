<?php


class Module_Loadstats extends GW_Common_Module
{	
	public $default_view = 'default';
		
	function init()
	{
		$this->model = new stdClass();
		
		parent::init();
	}

	
	function viewDefault()
	{
		$test_actions = [];
		$test_views = [];
		
		$list = get_class_methods ($this);
		foreach($list as $method){

			if(stripos($method, 'doTest')===0)
				$test_actions[]=[$method, $this->$method??['info'=>'-']];
			
			if(stripos($method, 'viewTest')===0)
				$test_views[]=[substr($method,4), $this->$method??['info'=>'-']];			
		}
				
		$this->tpl_vars['test_actions']=$test_actions;
		$this->tpl_vars['test_views']=$test_views;
	}
	
	
	
	function viewtestViewStats()
	{
		$this->viewTestAciveClients();
		$this->viewTestForcedOrVerified();
	}
	
	public $viewtestViewStats = ["info"=>"Info about server load"];
	
	function viewtestViewStatsOverallHourly()
	{
		echo "<h3>Hourly rates</h3>";
		$t = new GW_Timer;
		echo GW_Data_to_Html_Table_Helper::doTable($this->getHourlyRequests());
		echo '<small class="text-muted">'.$t->stop().' secs</small>';
	}

	
	function getHourlyRequests(){
		
		$rows = GW::db()->fetch_rows("SELECT 
			CONCAT(year, '-', LPAD(month,2,'0'), '-', LPAD(day,2,'0'), ' ', LPAD(hour,2,'0'), ':00') AS time,
			SUM(cnt) AS total_requests
		    FROM request_ip_stats
		    GROUP BY year, month, day, hour
		    ORDER BY year DESC, month DESC, day DESC, hour DESC
		    LIMIT 48; -- last 48 hours");
		
		return $rows;
	}
	
	function viewTestAciveClients()
	{	
		$t = new GW_Timer;
		$rows = GW::db()->fetch_rows_key("
			SELECT 
				ip,
				SUM(CASE 
					WHEN s.year = YEAR(NOW())
					 AND s.month = MONTH(NOW())
					 AND s.day = DAY(NOW())
					 AND s.hour = HOUR(NOW())
				     THEN s.cnt ELSE 0 END) AS per_hour,
				SUM(s.cnt) AS total,
				ua
			    FROM request_ip_stats s
			    GROUP BY s.ip
			    ORDER BY total DESC
			    LIMIT 50
		", 'ip');
				
		$rows2 = GW::db()->fetch_rows_key('SELECT  
				ip,
				a.host,
				a.country,
				ua.str AS user_agent
			FROM request_ip_verify AS a
			LEFT JOIN gw_uni_schema AS ua ON a.ua = ua.id
			WHERE '.GW_DB::inCondition('ip', array_keys($rows)), 'ip');
		
			$ips = [];
		
			$empty =['country'=>'', 'user_agent'=>''];
			
			foreach($rows as $ip => &$row){
				
				$row['ip'] = long2ip($row['ip']);
				$ips[$row['ip']]=1;
				
				
				GW_Array_Helper::copy($rows2[$ip] ?? $empty, $row, ['host', 'country', 'user_agent']);
			}
			
			
		//lookup by user
		$rows3 = GW::db()->fetch_rows_key('SELECT  
				last_ip AS ip,
				concat(id,", ",email," ",name," ",surname) AS user_info
			FROM gw_users AS a
			WHERE '.GW_DB::inConditionStr('last_ip', array_keys($ips)), 'ip');
		
			foreach($rows as $ip => &$row){
				
				if(isset($rows3[$row['ip']]))
					GW_Array_Helper::copy($rows3[$row['ip']], $row, ['user_info']);
			}			
			
		
		//d::dumpas($rows);
		echo "<h3>Active clients</h3>";
		$this->fillipstats($rows);
		$this->procRows($rows);
		$rows = array_values($rows); //lose index long col
		foreach($rows as &$row)
			unset($row['ua']);
		
		$opts = ['valformat'=>['ip'=>0, 'user_agent'=>'trunc40']];
		echo GW_Data_to_Html_Table_Helper::doTable($rows, $opts);	
		echo '<small class="text-muted">'.$t->stop().' secs</small>';
	}
	
	function viewTestForcedOrVerified()
	{
		$t = new GW_Timer;
		$rows = GW::db()->fetch_rows("
			SELECT 
				INET_NTOA(ip) AS ip,
				CASE state
				    WHEN 1 THEN 'failed/pending'
				    WHEN 2 THEN 'succeed'
				    WHEN 3 THEN 'whitelist'
				    ELSE 'unknown'
				END AS state_text,
				expires,
				country,
				host,
				updated,
				ua.str
			FROM request_ip_verify AS a
			LEFT JOIN gw_uni_schema AS ua ON a.ua = ua.id
			WHERE state > 0
			ORDER BY state, updated DESC;
		");
				echo "<h3>Forced to verify and failed/pending or succeed</h3>";
		$this->procRows($rows);
		$opts = ['valformat'=>['ip'=>0]];
		echo GW_Data_to_Html_Table_Helper::doTable($rows,$opts);
		echo '<small class="text-muted">'.$t->stop().' secs</small>';		
	}
	
	function procRows(&$rows)
	{
		foreach($rows as &$row){
				
			
			$row['ip'] = "<a href='".$this->buildUri('testip', ['ip'=>$row['ip']])."'>".$row['ip']."</a>";
		}
		
	}
	
	function fillIpStats(&$rows)
	{
		foreach($rows as $i => $row){
			$ip = $row['ip'];
			
			if($row['country'] ?? false)
				continue;
			
			$dat=GW_Bot_Detect::markIp(['ip'=>$ip, 'ua'=>$row['ua']]);
			
			$rows[$i]['country'] = $dat['country'];
			$rows[$i]['host'] = $dat['host'];
			
		}
	}
	
	
	function viewfooterhourly(){
		

		$that = $this;
		
		$rows= GW_Temp_Data::singleton()->rwCallback([
			    'name'=>"hourlyrequest_cache",
			    'format'=>'serialize',
			    'expires'=> '10 minutes'], function() use (&$that) {
					
			return  array_reverse($that->getHourlyRequests());

		});
		
		$stats = [];
		foreach($rows as $row)
			$stats[$row['time']] = $row['total_requests'];
		
		$this->tpl_vars['hourly_stats'] = $stats;
	}
	
	
	function viewTestIp(){
		
		$ip = $_GET['ip'] ?? false;	
		
		if(!$ip){
			$form = ['fields'=>[
				'ip'=>['type'=>'ip','default'=>$_SERVER['REMOTE_ADDR'], 'required'=>1],
			],'cols'=>4];

			if(!($answers=$this->prompt($form, "Examine ip")))
				return false;	

			$ip = $answers['ip'];			
		}
		
		
		list($ip, $ipint) = GW_Bot_Detect::ip2int($ip);
		
			$rows = GW::db()->fetch_rows("
			SELECT 
				concat(year,'-',month,'-',day,' ',hour) AS time,
				cnt
			FROM request_ip_stats s
			WHERE ip = $ipint
			LIMIT 50;
		");
			
		echo GW_Data_to_Html_Table_Helper::doTable($rows);
		
		$row = GW::db()->fetch_row("
			SELECT 
				INET_NTOA(ip) AS ip,
				CASE state
				    WHEN 0 THEN '-'	
				    WHEN 1 THEN 'failed/pending'
				    WHEN 2 THEN 'succeed'
				    WHEN 3 THEN 'whitelist'
				    ELSE 'unknown'
				END AS state_text,
				expires,
				country,
				host,
				updated,
				a.ua AS user_agent_id,
				ua.str AS user_agent
			FROM request_ip_verify AS a
			LEFT JOIN gw_uni_schema AS ua ON a.ua = ua.id
			WHERE ip=$ipint
			ORDER BY state, updated DESC;
		");		
		
		echo GW_Data_to_Html_Table_Helper::doTableSingleRecord($row);
	}
	
	
	public $doTestViewStats = ["info"=>"Change ip state"];
		
	function doTestApproveIp()
	{
		$form = ['fields'=>[
				'ip'=>['type'=>'ip','default'=>$_SERVER['REMOTE_ADDR'], 'required'=>1],
				'state'=>['type'=>'select', 'options'=>GW::l('/M/SYSTEM/OPTIONS/bot_detect_states'),'default'=>3]
			],'cols'=>4];

		if(!($answers=$this->prompt($form, "Add ip to whitelist or other state")))
			return false;	

		$ip = $answers['ip'];
		

		//whitelisted
		GW_Bot_Detect::markIp(['ip'=>$ip, 'state'=>$state, 'tag'=>$this->event->id]);

		
		$this->setMessage("Ip $ip Whitelisted");
		$this->jump();
	}
	
}




