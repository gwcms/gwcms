<?php



class Module_Items extends GW_Common_Module
{	

	public $default_view = 'list';
	
	function init()
	{
	
		parent::init();	
		
		
		$this->tpl_vars['payurl'] = GW::s('SITE_URL').$this->app->ln.'/direct/payments/requests?key=';
	}	
	
	
	function viewDefault()
	{
		
	}
	
	function __eventAfterSave($item)
	{
		if(!$item->key){
			$item->key = $item->id.GW_String_Helper::getRandString(6);
			$item->updateChanged();
		}
	}
	
	function __eventAfterList($list)
	{
		$this->attachFieldOptions($list, 'admin_id', 'GW_User');	
		
		
	}
	
	function doPaymentAccepted()
	{
		$admin_mail = $this->modconfig->paysuccess_notify_email;
		
		if(!$admin_mail)
			exit;
		
		$request = $this->getDataObjectById();
		
		$orderurl = $this->buildUri('form', ['id'=>$request->id], ['absolute'=>1]);
		
		$admin = GW_User::singleton()->find(['id=?', $request->admin_id]);
		
		$str = [];
		
		
		$project = str_replace(['http://','https://'],'',Navigator::getBase(true));
		
		$str[] = "<b>Admin:</b> {$admin->username}";
		$str[] = "<b>Admin url:</b> <a href='$orderurl'>$orderurl</a>";
		$str[] = "<b>Client email:</b> $request->customer_email";
		$str[] = "<b>Pay time:</b> $request->paytime";
		
		if($request->admin_note)
			$str[] = "<b>Admin note:</b> ".GW_String_Helper::truncate($request->admin_note);
				
		$str[] = "<b>Amount:</b> $request->amount EUR";
		
		if($request->pay_test){
			$str[] = "<b style='color:red'>THIS IS TEST PAYMENT</b>";
		}		

		
		$opts = [
			'to' => $admin_mail,
			'subject' => "New payment payd (id:{$request->id}) in $project",
			'body' => implode("<br />", $str),
		    //'attachments'=>[$filename=>$pdf]
		];
				
		if($request->email!='vidmantas.work@gmail.com')
			$opts['bcc'] = GW_Mail_Helper::getAdminAddr();
		
		
		
		$msg = GW::ln('/m/MESSAGE_SENT_TO',['v'=>['email'=>$admin_mail]]);
		//$this->setMessage();
		
		$stat = GW_Mail_Helper::sendMail($opts);
		d::dumpas($stat);

		exit;
	}
}
