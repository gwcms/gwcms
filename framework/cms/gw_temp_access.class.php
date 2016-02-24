<?php

class GW_Temp_Access extends GW_Composite_Data_Object {

	public $table = 'gw_temp_access';

	function getToken($user_id, $expires = '10 minute') {
		$token = GW_String_Helper::getRandString(50);

		$access = $this->createNewObject(['user_id' => $user_id, 'token' => $token, 'expires' => date('Y-m-d H:i:s', strtotime('+' . $expires))]);
		$access->insert();

		return $token;
	}

	function getTempAccess($user_id, $token) {
		$this->getDB()->query("DELETE FROM `$this->table` WHERE expires < NOW()");

		if ($found = $this->find(['user_id=? AND token=?', $user_id, $token])) {
			$found->delete();
			return true;
		}

		return false;
	}

}
