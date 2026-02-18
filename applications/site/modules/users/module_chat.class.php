<?php

class Module_Chat extends GW_Public_Module {

	function init() {
		$this->model = new GW_Chat_Message;

		//tol kol dirbu su siuo moduliu - reikia kad lang failus importuotu i db
		//GW::$devel_debug = true;


		$this->paging_enabled = 1;
		$this->list_params['page_by'] = 26;
		$this->list_params['paging_enabled'] = 1;
		$this->list_params['page'] = isset($_GET['page']) ? $_GET['page'] : 1;

		$this->links['conversation'] = $this->app->buildURI($this->getViewPath('conversation'));
		$this->options['countries'] = GW_Country::singleton()->getOptions('en');
	}

	function viewDefault() 
	{
		$this->userRequired();

		if ($this->paging_enabled && $this->list_params['paging_enabled'] && $this->list_params['page_by']) {
			$page = $this->list_params['page'] ? $this->list_params['page'] - 1 : 0;
			$params['offset'] = $this->list_params['page_by'] * $page;
			$params['limit'] = $this->list_params['page_by'];
		}

		$params['order'] = 'insert_time DESC';

		$uid = $this->app->user->id;
		$list = GW_Chat_Message::singleton()->findAll(["(from_id=? OR to_id=?) AND last=1", $uid, $uid], $params);
		$this->__procList($list);

		$this->setUpPaging(GW_Chat_Message::singleton()->lastRequestInfo());

		$this->tpl_vars['list'] = $list;

		$this->tpl_name = 'conversation_list';
	}

	function __procList($list) {
		$myid = $this->app->user->id;

		foreach ($list as $item) {
			if (($item->from_id == $myid && $item->to_id == $myid) || $item->from_id == $myid) {
				$item->type = 'out';
				$item->uid = $item->to_id;
			} else {
				$item->type = 'in';
				$item->uid = $item->from_id;
			}

			$item->user = GW_Customer::singleton()->find(['id=? AND active=1', $item->uid]);
			$item->conv_link = $this->links['conversation'] . '/' . $item->uid;
			$item->userlink = $this->app->buildURI(GW::s('SITE/PATH_TRANS/users/userslist/_') . '/profile', ['id' => $item->uid]);
		}
	}

	function __viewConversationRecent() {
		$params = [];
		$params['order'] = 'insert_time DESC';
		$params['limit'] = 10;
		$uid = $this->app->user->id;
		$list = GW_Chat_Message::singleton()->findAll(["(from_id=? OR to_id=?) AND last=1", $uid, $uid], $params);
		$this->__procList($list);
		$this->tpl_vars['recent_chats'] = $list;
	}

	function viewConversation($user) {
		$uid = (int) $user[0];
		$muid = $this->app->user->id;
		$cnd = ["(from_id=? AND to_id=?) OR (from_id=? AND to_id=?)", $muid, $uid, $uid, $muid];

		$list = GW_Chat_Message::singleton()->findAll($cnd, ['order' => 'insert_time DESC']);

		$this->tpl_vars['list'] = $list;

		if (isset($list[0])) {
			$this->__procList([$list[0]]);
			$this->tpl_vars['message'] = $list[0];
		}


		$this->tpl_vars['chatuser'] = GW_Customer::singleton()->find(['id=? AND active=1', $uid]);

		$this->links['markasseen'] = $this->buildDirectUri(false, ['act' => 'doMarkAsRead', 'uid' => $uid]);

		$this->__viewConversationRecent();


		$this->tpl_name = 'conversation_user';
	}
	

	/*
	function doTestPush()
	{
		$url = Navigator::backgroundRequest('admin/en/customers/users?act=doPushUserMessage&id=9');
		
		die($url);
		//d::dumpas([$url, file_get_contents($url)]);	
	}
	 *
	 */
	

	function doMessage() {
		$vals = $_POST['item'];
		$msg = GW_Chat_Message::singleton()->createNewObject();
		$msg->to_id = $vals['uid'];
		$msg->from_id = $this->app->user->id;
		$msg->message = $vals['message'];
		
		
		//push notifications on new message
		//TODO: nukelti push i background, kad greiciau atsakyti
		if($rec=GW_Customer::singleton()->find(['id=?',$msg->to_id])){
			Navigator::backgroundRequest('admin/en/customers/users?act=doPushUserMessage&id='.(int)$rec->id);
		}else{
			$msg->errors[]='/m/INVALID_RECIPIENT';
		}


		if ($msg->validate()) {
			$msg->insert();
			$this->setPlainMessage('/m/MESSAGE_SENT');
		} else {
			$this->setError($msg->errors);
		}

		
		
		if(isset($_GET['return_to'])){
			//d::dumpas('Location: '.$_GET['RETURN_TO']);
			$this->app->jump($_GET['return_to']);
		}else{
			$this->app->jump();
		}
	}

	function doMarkAsRead() {
		$uid = $_GET['uid'];

		$muid = $this->app->user->id;
		$cnd = ["(from_id=? AND to_id=?) AND seen=0", $uid, $muid];

		$list = GW_Chat_Message::singleton()->findAll($cnd);

		foreach ($list as $item) {
			$item->saveValues(['seen' => 1, 'seen_time' => date('Y-m-d H:i:s')]);
		}
		exit;
	}

}
