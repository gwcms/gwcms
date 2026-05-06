<?php

class GW_Change_Track_Render_Helper
{
	static function normalizeText($value)
	{
		$value = html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$value = preg_replace('/\s+/u', ' ', $value);
		return trim((string)$value);
	}

	static function countTextLines($value)
	{
		$value = (string)$value;
		if($value === '')
			return 0;
		
		return substr_count($value, "\n") + 1;
	}

	static function shouldUseSummaryLink($old, $new, $limit = 1000)
	{
		return max(mb_strlen((string)$old), mb_strlen((string)$new)) > $limit;
	}

	static function buildLargeTextSummary($old, $new)
	{
		$old = (string)$old;
		$new = (string)$new;
		
		$old_len = mb_strlen($old);
		$new_len = mb_strlen($new);
		$old_lines = self::countTextLines($old);
		$new_lines = self::countTextLines($new);
		
		return [
			'old_len' => $old_len,
			'new_len' => $new_len,
			'old_lines' => $old_lines,
			'new_lines' => $new_lines,
			'chars_delta' => $new_len - $old_len,
			'lines_delta' => $new_lines - $old_lines,
		];
	}

	static function buildHumanSummaryFromTexts($old, $new)
	{
		$summary = self::buildLargeTextSummary($old, $new);
		$title = GW::l('/G/changetrack/HUMAN_DIFF/TEXT_WAS_UPDATED');
		
		if($summary['chars_delta'] > 0)
			$title = GW::l('/G/changetrack/HUMAN_DIFF/TEXT_WAS_EXPANDED');
		elseif($summary['chars_delta'] < 0)
			$title = GW::l('/G/changetrack/HUMAN_DIFF/TEXT_WAS_SHORTENED');
		
		return [
			'title' => $title,
			'details' => [
				['text' => GW::l('/G/changetrack/HUMAN_DIFF/TEXT_LENGTH', ['v' => ['old' => $summary['old_len'], 'new' => $summary['new_len']]]), 'type' => 'neutral'],
				['text' => GW::l('/G/changetrack/HUMAN_DIFF/LINES', ['v' => ['old' => $summary['old_lines'], 'new' => $summary['new_lines']]]), 'type' => 'neutral'],
			],
			'summary' => $summary,
		];
	}

	static function buildHumanSummaryFromPatch($patch)
	{
		$patch = (string)$patch;
		$result = [
			'title' => GW::l('/G/changetrack/HUMAN_DIFF/LARGE_TEXT_CHANGE'),
			'details' => [],
		];
		
		if(preg_match('/@@ -(\d+),?(\d*) \+(\d+),?(\d*) @@/', $patch, $m)){
			$old_start = (int)$m[1];
			$old_size = $m[2] === '' ? 1 : (int)$m[2];
			$new_start = (int)$m[3];
			$new_size = $m[4] === '' ? 1 : (int)$m[4];
			
			if($new_size > $old_size)
				$result['title'] = GW::l('/G/changetrack/HUMAN_DIFF/TEXT_WAS_EXPANDED');
			elseif($new_size < $old_size)
				$result['title'] = GW::l('/G/changetrack/HUMAN_DIFF/TEXT_WAS_SHORTENED');
			else
				$result['title'] = GW::l('/G/changetrack/HUMAN_DIFF/TEXT_WAS_UPDATED');
			
			$result['details'][] = ['text' => GW::l('/G/changetrack/HUMAN_DIFF/CHANGED_NEAR_POSITION', ['v' => ['pos' => $old_start]]), 'type' => 'neutral'];
			$result['details'][] = ['text' => GW::l('/G/changetrack/HUMAN_DIFF/BLOCK_SIZE', ['v' => ['old' => $old_size, 'new' => $new_size]]), 'type' => 'neutral'];
			
			if($old_start !== $new_start)
				$result['details'][] = ['text' => GW::l('/G/changetrack/HUMAN_DIFF/NEW_BLOCK_STARTS_NEAR_POSITION', ['v' => ['pos' => $new_start]]), 'type' => 'neutral'];
		}
		
		preg_match_all('/^\+(?!\+\+).*$/m', $patch, $added);
		preg_match_all('/^\-(?!\-\-).*$/m', $patch, $removed);
		$added_cnt = count($added[0]);
		$removed_cnt = count($removed[0]);
		
		$added_preview = [];
		foreach($added[0] as $line){
			$line = ltrim((string)$line, '+');
			$line = rawurldecode($line);
			$line = self::normalizeText($line);
			if($line !== '')
				$added_preview[] = $line;
		}
		
		$removed_preview = [];
		foreach($removed[0] as $line){
			$line = ltrim((string)$line, '-');
			$line = rawurldecode($line);
			$line = self::normalizeText($line);
			if($line !== '')
				$removed_preview[] = $line;
		}
		
		$total_preview_len = 0;
		foreach($added_preview as $line)
			$total_preview_len += mb_strlen($line);
		foreach($removed_preview as $line)
			$total_preview_len += mb_strlen($line);
		
		$use_small_preview = ($added_cnt + $removed_cnt) <= 2 && $total_preview_len > 0 && $total_preview_len <= 180;
		
		if($use_small_preview){
			foreach($added_preview as $line)
				$result['details'][] = ['text' => GW::l('/G/changetrack/HUMAN_DIFF/ADDED_TEXT', ['v' => ['text' => $line]]), 'type' => 'added'];
			
			foreach($removed_preview as $line)
				$result['details'][] = ['text' => GW::l('/G/changetrack/HUMAN_DIFF/REMOVED_TEXT', ['v' => ['text' => $line]]), 'type' => 'removed'];
		}
		
		if(($added_cnt || $removed_cnt) && !$use_small_preview)
			$result['details'][] = ['text' => GW::l('/G/changetrack/HUMAN_DIFF/ADDED_REMOVED_CHUNKS', ['v' => ['added' => $added_cnt, 'removed' => $removed_cnt]]), 'type' => 'neutral'];
		
		return $result;
	}
}
