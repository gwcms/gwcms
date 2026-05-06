<?php

class GW_Config_Change_Track_Helper
{
	static function isDiffWorth($new, $old)
	{
		if (!is_string($new) || !is_string($old)) {
			return false;
		}

		$maxlen = max(mb_strlen($new), mb_strlen($old));

		return $maxlen > 80 || strpos($new, "\n") !== false || strpos($old, "\n") !== false;
	}

	static function trackValues($cfg, $vals, $opts = [])
	{
		$user_id = GW::$context->app->user->id ?? -1;
		$note = $opts['note'] ?? null;

		foreach ($vals as $key => $newval) {
			$oldval = $cfg->get($key);

			if ((string)$oldval === (string)$newval) {
				continue;
			}

			$fullkey = $cfg->prefix . $key;
			$track = GW_Config_Change_Track::singleton()->createNewObject();
			$row = [
				'fullkey' => $fullkey,
				'user_id' => $user_id,
				'note' => '',
				'new' => [],
				'old' => [],
				'diff' => [],
			];

			if ($note) {
				$row['note'] = $note;
			}

			if (self::isDiffWorth($newval, $oldval)) {
				$row['diff'] = ['value' => GW_String_Helper::createDiff((string)$newval, (string)$oldval)];
			} else {
				$row['new'] = ['value' => $newval];
				$row['old'] = ['value' => $oldval];
			}

			$track->setValues($row);
			$track->insert();
		}
	}

	static function prepareCountByPrefix($prefix)
	{
		$prefix_esc = addslashes(substr($prefix, 0, 100));
		return GW::db()->fetch_assoc("SELECT fullkey, COUNT(*) cnt FROM gw_config_change_track WHERE fullkey LIKE '{$prefix_esc}%' GROUP BY fullkey");
	}

	static function getChangesByFullKey($fullkey)
	{
		$list = GW_Config_Change_Track::singleton()->findAll(['fullkey=?', $fullkey], ['order' => 'id DESC']);
		$changes = [];

		foreach ($list as $change) {
			if ($change->new && isset($change->new->value)) {
				$changes[] = [$change, $change->new->value, $change->old->value ?? null];
			}

			if ($change->diff && isset($change->diff->value)) {
				$changes[] = [$change, $change->diff->value];
			}
		}

		return $changes;
	}

	static function getRevertedContent($fullkey, $headversion, $changeid)
	{
		$list = self::getChangesByFullKey($fullkey);
		$content = $headversion;
		$changesitm = null;

		foreach ($list as $changesmeta) {
			$changesitm = $changesmeta[0];
			$content = GW_String_Helper::applyDiff($changesmeta[1], $content);

			if ($changeid == $changesitm->id) {
				break;
			}
		}

		return [$content, $changesitm];
	}
}
