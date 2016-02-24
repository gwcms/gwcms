<?php

class GW_Test_Internet {

		/**
		 * Patikrinti ar galima susijungti su serveriu
		 */
		function checkOne($domain, $port = 80, &$error) {
				return (bool) @fsockopen($domain, $port, $num, $error, 5);
		}

		/**
		 * Patirkinti ar eina uzmegzti rysi su pora serveriu
		 * ihardcodinti populiarus
		 */
		function __check(&$error) {
				if (!self::checkOne('www.delfi.lt', 80, $error))
						if (!self::checkOne('www.google.com', 80, $error))
								return false;

				return true;
		}

		/**
		 * Patikrinti būklę ir išsaugoti į gw_config
		 */
		function check(&$error) {
				$state = self::__check($error);

				self::saveState($state);

				return $state;
		}

		function saveState($state) {
				$cfg = GW::getInstance('GW_Config');
				;
				$cfg->set('system/internet', (int) $state . '|' . date('Y-m-d H:i:s'));
		}

		/**
		 * Gauti paskutinio tikrinimo rezultata
		 * jei paskutinis tikrinimas buvo veliau nei pries 2 minutes
		 * padaryti tikrinima
		 */
		function lastCheck() {
				$cfg = GW::getInstance('GW_Config');
				;
				list($state, $time) = explode('|', $cfg->get('system/internet'));

				$diff = time() - strtotime($time);

				if ($diff > 2 * 60)
						return self::check();

				return $state;
		}

}
