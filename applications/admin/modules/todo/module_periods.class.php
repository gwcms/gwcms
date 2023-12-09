<?php


/*


CREATE TABLE `gw_todo_periods` (
  `id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb3_lithuanian_ci NOT NULL,
  `from` datetime NOT NULL,
  `to` datetime NOT NULL,
  `description` text COLLATE utf8mb3_lithuanian_ci NOT NULL,
  `remind_before` varchar(10) COLLATE utf8mb3_lithuanian_ci NOT NULL,
  `remind_emails` varchar(255) COLLATE utf8mb3_lithuanian_ci NOT NULL,
  `remind_text` text COLLATE utf8mb3_lithuanian_ci NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_lithuanian_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_todo_periods`
--
ALTER TABLE `gw_todo_periods`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_todo_periods`
--
ALTER TABLE `gw_todo_periods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

 */

class Module_Periods  extends GW_Common_Module
{	
	
	
	
	function __eventAfterList($list)
	{
		GW_Composite_Data_Object::prepareLinkedObjects($list, 'user');
	}
	
	
	
	
	
	function getListConfig()
	{		
		$cfg = parent::getListConfig();
		
		$cfg['fields']['user_title'] = 'Lf';
		$cfg['fields']['changetrack'] = 'L';	
		$cfg['fields']['comments'] = 'f';
		
		$cfg['inputs']['title']=['type'=>'text'];	
		$cfg['inputs']['from']=['type'=>'date'];	
		$cfg['inputs']['to']=['type'=>'date'];	
		$cfg['inputs']['description']=['type'=>'textarea'];	
		$cfg['inputs']['remind_before']=['type'=>'select','options'=>['1 month','1 day','7 day'],'options_fix'=>1, 'empty_option'=>1];	
		$cfg['inputs']['remind_emails']=['type'=>'text','hidden_note'=>'separator - semicolon character - ;'];	
		$cfg['inputs']['remind_text']=['type'=>'textarea'];	
		
		$cfg['inputs']['user_id']=['type'=>'select_ajax', 'modpath'=>"users/usr",'default'=>$this->app->user->id, 'empty_option'=>1, 'options'=>[], 'preload'=>1];	
		$cfg['inputs']['price']=['type'=>'number'];
		
		$cfg['inputs']['remind_mail_tpl']= [
		    'type'=>'select_ajax', 
		    'modpath'=>"emails/email_templates", 'preload'=>1, 'options'=>[], 'source_args'=>['byid'=>1], 
		    'after_input_f'=>"editadd"
		];

		
		
		$cfg['fields']['count'] = 'L';	

		return $cfg;
	}
	
	
	//per quick search suveiks
	function overrideFilterComments($value)
	{
		$value = GW_DB::escape($value);
		$cond = " (SELECT count(*) FROM `gw_todo` AS aab WHERE aab.parent_id=a.id AND description LIKE '%$value%')>0 ";


		return $cond;
	}
		

	function eventHandler($event, &$context)
	{
		switch($event)
		{
			case "BEFORE_SAVE":
				$item = $context;
				
				$item->user_id = $this->app->user->id;
			break;

		}
		
		parent::eventHandler($event, $context);
	}	
	

	function doPeriodEndNotifications()
	{
		$curdate = date('Y-m-d');
		$list = $this->model->findAll(['active=1 AND remind_date < ? AND remind_mail_tpl > 0 AND remind_snooze_until < ?', $curdate, $curdate]);
				
		
		
		foreach($list as $item){		
		
		
			$vars=[];
			$vars['period_start'] = $item->from;
			$vars['period_end'] = $item->to;
			$vars['title'] = $item->title;
			$vars['link'] = $url = $this->app->buildUri('todo/periods/form',['id'=>$item->id],['app'=>'admin','absolute'=>1]);

			$template_id = $item->remind_mail_tpl;
			$to = $item->user->email;

			$opts = [
			    'to'=>$to,
			    'tpl'=>GW_Mail_Template::singleton()->find($template_id),
			    'vars'=>$vars,
			    //'scheduled'=>date('Y-m-d H:i', strtotime('+'.(($i++)*2).' minute'))
			    //'attachments'=>[$filename=>$pdf]
			];

			if(isset($_GET['test'])){
				$debug = [
				    'body'=>GW_Mail_Template::singleton()->find($template_id)->body_lt,
					'to'=>$to,
					'scheduled'=>$opts['scheduled'],
					'link'=>$vars['link'],
					//'group'=>$group->title,
					'period'=>$item->title
				];

				d::dumpas($debug);
			}


			if(isset($_GET['confirm'])){
				$status = GW_Mail_Helper::sendMail($opts);	
			}else{
				$status = false;
			}

			$msg = $item->title.': '.GW::ln('/g/MESSAGE_SENT_TO',['v'=>['email'=>$to]]).". Status ".($status?'OK':'-');
			$this->setMessage($msg);
						
		}
		
		if(!$this->sys_call){
			$this->confirm('Patvirtinkite siuntimą');
		
			$this->jump();
		}		
		
	}
	
	
	function doSnoose()
	{
		$item = $this->getDataObjectById();
		
		$item->fireEvent('BEFORE_CHANGES');
		$item->remind_snooze_until = date('Y-m-d', strtotime('+ '.$_GET['day'].' DAY'));
		$item->updateChanged();
		
		$this->setMessage("$item->title snoosed until ".$item->remind_snooze_until);
		$this->jump();
		
	}
	

}


