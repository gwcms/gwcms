<?php

class Cron
{
	/**
	 * match example
	 * ..:05:.. 
	 * 	valanda:'..' (match'ins betkuria valanda)
	 *  minute: '05' (match'ins kai laikas bus 5minutes)
	 *  sekunde: '..' (matchins betkuria sekunde)
	 *  
	 *  kiti pavyzdziai 
	 *  	"0\d" matchins betkuri skaiciu 01,02,03,...,09
	 *  	".[02468]" matchins lyginius skaicius 00,02,04,...58
	 *  
	 *  intervalas nurodomas tam kad uzduotis nebutu vykdoma dar kart netrukus
	 *  galima nurodyti time match kad butu vykdoma kiekvienos valandos pirma minute
	 *  bet intervalas kas dvi valandas, jeigu ivyktu klaida ir nebutu ivykdyta pirma valanda butu ivykdoma antra
	 *  
	 *  metodas patikrina intervala ir jeigu matchina tai issaugo kad metodas yra dabar ivykdytas
	 *  
	 *  galima paleisti skripta nurodzius intervala cron.php 
	 *   
	 */
	
	function checkAndRunInterval($time_match, $interval)
	{
		$config = GW_Config::singleton();
		
		if(strpos($time_match,' ')===false)
			$time_match='....-..-.. '.$time_match;
	
		$match =  preg_match("/$time_match/",date('Y-m-d H:i:s'),$m)  ? 1 : 0 ;
		
		//dump("Match:$match $time_match ".date('Y-m-d H:i:s'));
	
		$dif=time() - strtotime($config->get($cron_id="ctask $time_match $interval"));
		$dif = $dif / 60;
	
		if( $match && ($dif > $interval*0.1 ) || $GLOBALS['argv'][1]==$interval){
			dump('['.date('H:i:s')."] run $interval");
			$config->set($cron_id, date('Y-m-d H:i:s'));
			return true;
		}else{
			return false;
		}
	}
	
	function process()
	{
		$crontask0 = new GW_CronTask;
		$time_matches = $crontask0->getAllTimeMatches();
		
		foreach($time_matches as $tm)
		{
			list($time_match, $interval) = explode('#', $tm);
			
			if(self::checkAndRunInterval($time_match, $interval))
			{
				//run all interval tasks
				$crontask0->getByTimeMatchExecute($tm);					
			}
		}
		
	}

}

$c = new Cron;
$c->process();