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
				$test_actions[]=[$method, $this->$method];
			
			if(stripos($method, 'viewTest')===0)
				$test_views[]=[substr($method,4), $this->$method];			
		}
				
		$this->tpl_vars['test_actions']=$test_actions;
		$this->tpl_vars['test_views']=$test_views;
	}
	
	
	
	public $viewtestViewStats = ["info"=>"Info about server load"];
	
	function viewtestViewStats()
	{
		$rows = GW::db()->fetch_rows("SELECT 
    INET_NTOA(s.ip) AS ip,
    SUM(s.cnt) AS total_requests,
    SUM(CASE WHEN s.year = YEAR(NOW()) 
              AND s.month = MONTH(NOW()) 
              AND s.day = DAY(NOW()) THEN s.cnt ELSE 0 END) AS today_requests,
    s.cnt AS current_hour_requests,
    v.host,
    v.country
FROM request_ip_stats s
LEFT JOIN request_ip_verify v ON v.ip = s.ip
GROUP BY s.ip
ORDER BY total_requests DESC
LIMIT 50;");
		//d::dumpas($rows);
		echo "<h3>Active clients</h3>";
		echo GW_Data_to_Html_Table_Helper::doTable($rows);
		$this->fillipstats($rows);
		
		$rows = GW::db()->fetch_rows("SELECT 
    INET_NTOA(ip) AS ip,
    CASE state
        WHEN 1 THEN 'failed/pending'
        WHEN 2 THEN 'succeed'
        ELSE 'unknown'
    END AS state_text,
    expires,
    country,
    host,
    updated
FROM request_ip_verify
WHERE state IN (1, 2)
ORDER BY state, updated DESC;");
		echo "<h3>Forced to verified and failed/pending or succeed</h3>";
		echo GW_Data_to_Html_Table_Helper::doTable($rows);
		
		
		echo "<h3>Hourly rates</h3>";
		echo GW_Data_to_Html_Table_Helper::doTable($this->getHourlyRequests());
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
	
	function fillIpStats($rows)
	{
		foreach($rows as $row){
			$ip = $row['ip'];
			
			if($row['country'])
				continue;
			
			$cc = geoip_country_code_by_name($ip);
			$ipint = sprintf('%u', ip2long($ip));
			GW::db()->query("
				    INSERT INTO request_ip_verify (ip, state, expires, country, host)
				    VALUES ($ipint, 0, DATE_ADD(NOW(), INTERVAL 10 DAY), '" . GW_DB::escape($cc) . "', '" . GW_DB::escape(gethostbyaddr($ip)) . "')
				    ON DUPLICATE KEY UPDATE state=0, expires=DATE_ADD(NOW(), INTERVAL 10 DAY), country=VALUES(country)
				");
		}
	}
	
}



