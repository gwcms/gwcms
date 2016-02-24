<?php

trait Singleton {

		/**
		 * @return GW_Data_Object 
		 */
		public static function singleton() {
				return GW::getInstance(get_called_class());
		}

}
