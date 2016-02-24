<?php

class GW_Report_Errors {

		static function msg($subject, $body) {
				mail('errors@gw.lt', $subject, $body);

				GW::getInstance('GW_Message')->msg(9, $subject, $body, 1);
		}

}
