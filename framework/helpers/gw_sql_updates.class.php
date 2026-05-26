<?php

class GW_SQL_Updates
{
	static function listPending()
	{
		$lastupdates = GW::getInstance('GW_Config')->get('gwcms/last_sql_updates');
		$list_files = glob(GW::s('DIR/ROOT').'sql/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]-[0-9]*.sql');
		$updates = [];

		foreach($list_files as $filename)
		{
			if(basename($filename) > $lastupdates)
				$updates[] = $filename;
		}

		return [$lastupdates, $updates];
	}

	static function executeSql($sql, $db=false)
	{
		$db = $db ?: GW::db();
		$result = [];
		$sqls = explode(';', $sql);

		foreach($sqls as $sql)
		{
			if(!trim($sql))
				continue;

			$db->query($sql, true);

			$result[] = [
				'sql' => $sql,
				'affected' => $db->affected(),
				'error' => $db->error,
				'error_query' => $db->error_query
			];
		}

		return $result;
	}

	static function importPending($db=false)
	{
		list($lastupdates, $updates) = self::listPending();
		$imported = [];

		foreach($updates as $updatefile)
		{
			$queries = self::executeSql(file_get_contents($updatefile), $db);

			GW::getInstance('GW_Config')->set('gwcms/last_sql_updates', basename($updatefile));

			$imported[] = [
				'file' => $updatefile,
				'queries' => $queries
			];
		}

		return [$lastupdates, $imported];
	}
}
