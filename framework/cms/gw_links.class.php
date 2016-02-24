<?php

class GW_Links implements GW_Composite_Slave {

		var $owner_obj;
		var $values;
		var $params;
		var $table;
		var $owner_obj_id;
		var $id1 = "id"; //owner_object_id
		var $id2 = "id1"; //dest_object_id

		/**
		 * @return DB
		 */

		function &getDB() {
				return GW::$context->vars['db'];
		}

		public function getByOwnerObject($master, $fieldname) {
				$this->setOwnerObject($master, $fieldname);
				return $this;
		}

		public function setOwnerObject($master, $fieldname) {
				$this->owner_obj = $master;
				$this->owner_obj_id = $master->get($master->primary_fields[0]);
		}

		public function save() {
				if (!is_null($this->values))
						$this->updateBinds($this->values);
		}

		public function setValues($values) {
				$this->values = $values;
		}

		public function setParams($params) {
				$this->params = $params;

				if (!isset($this->params['table']))
						trigger_error('GW_Links: not specified table param', E_USER_ERROR);

				$this->table = $this->params['table'];

				if (isset($this->params['fieldnames']))
						list($this->id1, $this->id2) = $this->params['fieldnames'];
		}

		public function deleteComposite() {
				$db = $this->getDB();
				$db->delete($this->table, Array($this->id1 . '=?', $this->owner_obj_id));
		}

		public function getValue() {
				return $this->getBinds();
		}

		public function validate() {
				return true;
		}

		private function getBinds() {
				$db = $this->getDB();
				$list = $db->fetch_rows(Array("SELECT {$this->id2} FROM $this->table WHERE $this->id1=?", $this->owner_obj_id), false);

				$list1 = [];

				foreach ($list as $i => $rec)
						$list1[] = $rec[0];

				unset($list);

				return $list1;
		}

		private function removeBinds($binds) {
				if (!count($binds))
						return;

				$db = $this->getDB();

				$cond = "{$this->id1}=? AND (";

				foreach ($binds as $i => $id1)
						$cond.="{$this->id2}=? OR ";

				$cond = substr($cond, 0, -4) . ')';

				$filter = array_merge((array) $cond, (array) $this->owner_obj_id, $binds);

				$db->delete($this->table, $filter);
		}

		private function addBinds($binds) {
				if (!count($binds))
						return;

				$db = $this->getDB();

				$list = Array();

				foreach ($binds as $id1)
						$list[] = Array($this->id1 => $this->owner_obj_id, $this->id2 => $id1);

				$db->multi_insert($this->table, $list);
		}

		private function updateBinds($newbinds) {
				$newbinds = (array) $newbinds;
				$oldbinds = (array) $this->getBinds();

				$add = array_diff($newbinds, $oldbinds);
				$remove = array_diff($oldbinds, $newbinds);

				$this->removeBinds($remove);
				$this->addBinds($add);
		}

}
