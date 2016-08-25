<?php


class gw_service_test_user extends gw_testservice
{
	
	function init()
	{
		
		//$this->test_obj = new GW_General_RPC;
		//$this->test_obj->url = "http://192.168.0.24/acs/service/user/";
		
		$this->testobj->basicAuthSetUserPass('aaa','bbb');
	}
	
	//-------------PERKELTI I DEMO.PHP------------------------------
	
	function randStr($length=15, $setp=false)
	{
		$set=$setp ? $setp : "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
		
		$str = '';
		$max = strlen($set)-1;
		
		for($i=0;$i<$length;$i++)
			$str.=$set[rand(0,$max)];

		return $str;
	}
	
	private $userid;
	private $token;
	private $testuser;
	private $testpass;
	private $testuserdata;
	
	//testuojam sekminga registracija.	
	function testRegister()
	{
		$personid=$this->randStr(11,'0123456789');
		$this->testuser = $personid;
		$this->testpass = $this->randStr();
		
		
		$this->testuserdata = [
			'username'=>$this->testuser, //toks pat kaip asmens kodas
			'pass_new'=>$this->testpass,
			'pass_new_repeat'=>$this->testpass,
			'email'=>$this->testuser.'@mailinator.com',
			'gender'=>['m','f'][rand(0,1)],
			'name'=>'testname'.$this->randStr(5),
			'surname'=>'testname'.$this->randStr(5),
			'phone'=>'+3706'.$this->randStr(7,'0123456789'), //asmeninis tel nr
			'person_id'=>$personid, ///11digits
			'person_document_id'=>$this->randStr(9),//2leters 7-numbers
			'birth_date'=>rand(1950,1996).'-'.rand(1,12).'-'.rand(1,30),
			'workplace'=>['Aventus','LitHome','betkoks tekstas iki 255 simb'][rand(0,2)], // darbo vieta
			'workplace_occupation'=>['Administratorius','Direktorius','Buhalteris','betkoks tekstas iki 255 simb'][rand(0,3)], // pareigos
			'workplace_phone'=>'+3706'.$this->randStr(7,'0123456789'), //darbovietes tel nr
			'actual_addr_street'=>['Kubiliaus 6','Kubiliaus 7','betkoks tekstas iki 150 simb'][rand(0,2)], //gyvenamos vietos gatve, nr
			'actual_addr_city'=>['Vilnius','Kaunas','betkoks tekstas iki 100simb'][rand(0,2)], //gyvenamasos vietos miestas
			'legal_addr_street'=>['Kubiliaus 6','Kubiliaus 7','betkoks tekstas iki 150 simb'][rand(0,2)], //registracijos gatve, nr
			'legal_addr_city'=>['Vilnius','Kaunas','betkoks tekstas iki 100simb'][rand(0,2)], //registracijos miestas
			'contact_person'=>['Test kont asm1','Vardenis Pavardenis','betkoks tekstas iki 150simb'][rand(0,2)],
			'contact_person_phone'=>['Test kont asm1','Vardenis Pavardenis','betkoks tekstas iki 150simb'][rand(0,2)],
			'bank_account'=>"GE".$this->randStr(23,'0123456789'), //25 simb banko sask nr
			'salary'=>rand(5,30)*100,//atlyginimas
			'registration_ip'=>rand(1,255).'.'.rand(1,255).'.'.rand(1,255).'.'.rand(1,255), //is kur registruojasi
			'campaign_id'=>[1,15,65][rand(0,2)],//integer
			'referer'=>'http://bekokssaitas/max/simboliu/255/',//is kur atejo registracija
			'newsletter'=>rand(0,1),//sutinka arba nesutinka gauti naujienlaiski
			'active'=>1,
			'lang'=>['lt','ru','ge'][rand(0,2)]
		];
		
		$resp = $this->testobj->register([],['user'=>$this->testuserdata]);
		

		

		
		$this->assertTrue(isset($resp->user_id) && is_int($resp->user_id));
		$this->assertTrue(isset($resp->register) && $resp->register == 'OK');
		$this->assertTrue(isset($resp->token) && strlen($resp->token) > 5);		
	}
	
	//bandom uzsiregistrauoti su tuo paciu asmens kodu
	function testRegisterErrors()
	{
		
		//duplicate test

		$resp = $this->testobj->register([],['user'=>$this->testuserdata]);
		
		$this->assertTrue(isset($resp->errors) && isset($resp->errors->email) && $resp->errors->email == '/USER/EMAIL_ALREADY_REGISTERED');

	}	
	
	//testuojam prisijungima
	function testGoodLogin()
	{
		//test good login
		
		$resp = $this->testobj->login([],$args=[
			'user'=>$this->testuser,
			'pass'=>$this->testpass,
			'user_agent'=>'firefox',
			'ip'=>'123.123.123.123'
			]
		);
		
		$this->assertEquals($resp->user->username, $this->testuser);
		
		$this->userid = $resp->user->id;
		$this->token = $resp->user->token;	
	}
	
	//testuojam klaidu pateikima
	function testBadLogin()
	{
		//test bad login
		
		$resp = $this->testobj->login([],[
			'user'=>'demo',
			'pass'=>'123456aaa',
			'user_agent'=>'firefox',
			'ip'=>'123.123.123.123'
			]);
		
		$this->assertEquals($resp->error, '1');		
	}	
	
	
	//bandom atnaujinti tel nr
	function testUpdate()
	{
		$testval = '+3706'.$this->randStr(7,'0123456789'); //asmeninis tel nr
		
		$resp2 = $this->testobj->update([],['user'=>[
			'phone'=>$testval
		], 'token'=>$this->token, 'userid'=>$this->userid]);
		
		
		$respinf = $this->testobj->info([], ['userid'=>$this->userid, 'token'=>$this->token]);
		
		$this->assertEquals($respinf->user->phone, $testval);
	}
	
	//bandom atnaujinti su klaidom
	function testErrorOnUpdate()
	{
		$resp3 = $this->testobj->update([],['user'=>['email'=>'asdfs'], 'token'=>$this->token,'userid'=>$this->userid]);
		
		
		$this->assertEquals($resp3->updateuser, 'FAIL');
		$this->assertEquals($resp3->errors->email, '/G/VALIDATION/EMAIL/INVALID_EMAIL');
	}
		
	//atsijungimas nuo paskyros
	//gales skaiciuot prisijungimo laika ar panasias statistikas
	function testLogout()
	{
		$resp = $this->testobj->logout([],['token'=>$this->token, 'userid'=>$this->userid]);
		
		$this->assertEquals($resp->logout, "OK");
	}
	
	//-------------PERKELTI I DEMO.PHP------------------------------
}