<?php

class GW_Country extends GW_Data_Object {

		public $table = 'gw_countries';

		function getOptions($lang = 'lt') {
				return $this->getAssoc(['code', 'title_' . $lang], '', ['order' => 'title_' . $lang . ' ASC']);
		}

		/**
		 * Used to update titles, codes from google maps autocomplete location service
		 */
		function updateCountry($code, $title_en) {
				if ($country = $this->find(['code=?', $code])) {
						if ($country->title_en != $title_en)
								$country->saveValues(['title_en' => $title_en]);
				}else {
						$new = $this->createNewObject(['code' => $code, 'title_en' => $title_en]);
						$new->insert();
				}
		}

}
