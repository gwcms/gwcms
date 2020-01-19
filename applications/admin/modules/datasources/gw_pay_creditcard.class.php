<?php

class GW_Pay_Creditcard extends GW_Data_Object {

	public $validators = [
	    'name' => ['gw_string', ['required' => 1]],
	    'surname' => ['gw_string', ['required' => 1]],
		//'card_type'=>['gw_string', ['required'=>1]],
		//'expirity_time'=>['gw_string', ['required'=>1]],
		//'cvc'=>['gw_string', ['required'=>1,'pattern'=>'/\d\d\d/']],
	];

	function validate() {
		
		
		list($num,$cvc,$exp) = explode(',',$this->num_cvc_exp);
		
		
		
		if(!self::luhn_check($num))
			$this->setError(Gw::ln('/m/INVALID_CARD_NUMBER',['v'=>['number'=>$num]]),'number');
		
		if(!self::cvc_check($cvc))
			$this->setError(Gw::ln('/m/INVALID_CARD_CVC'),'cvc');
		
		if(!self::expires_check($exp))
			$this->setError(Gw::ln('/m/INVALID_CARD_EXPIRES'),'expires');
				
		 		
		parent::validate();

		return count($this->errors) == 0;
	}

	static function luhn_check($number) {

		// Strip any non-digits (useful for credit card numbers with spaces and hyphens)
		$number = preg_replace('/\D/', '', $number);

		// Set the string length and parity
		$number_length = strlen($number);
		$parity = $number_length % 2;

		// Loop through each digit and do the maths
		$total = 0;
		for ($i = 0; $i < $number_length; $i++) {
			$digit = $number[$i];
			// Multiply alternate digits by two
			if ($i % 2 == $parity) {
				$digit *= 2;
				// If the sum is two digits, add them together (in effect)
				if ($digit > 9) {
					$digit -= 9;
				}
			}
			// Total up the digits
			$total += $digit;
		}

		// If the total mod 10 equals 0, the number is valid
		return ($total % 10 == 0) ? TRUE : FALSE;
	}
	
	static function cvc_check($str)
	{
		return is_numeric($str);
	}
	
	static function expires_check($str)
	{
		@list($m, $y) = explode('/', $str);
		
		return "$y-$m" > date('Y-m');	
	}	

	function crypt($revert = false, $passenc = false) 
	{
		if($passenc==false)
			$passenc = GW_Config::singleton()->get('datasources__payments_creditcard/storage_pass');
		
		$pass = GW::db()->aesCrypt($passenc, GW::s('CC_ENC_STR'), true);

		if (!$revert) {
			$data = json_decode($this->data);
			$this->number_start = substr($this->num_cvc_exp, 0, 4);

			$this->num_cvc_exp = GW::db()->aesCrypt($this->num_cvc_exp, $pass);
			$this->crypt_test  = GW::db()->aesCrypt("cc_crypt_test", $pass);
			
			
			$this->encrypted = 1;
		} else {
			$this->num_cvc_exp = GW::db()->aesCrypt($this->num_cvc_exp, $pass, true);
			$this->encrypted = 0;
		}


		$this->updateChanged();
	}

}
