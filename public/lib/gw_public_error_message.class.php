<?php


class GW_Public_Error_Message extends GW_Error_Message
{
	function getLangFile($file_id)
	{
		return GW::$dir['PUB_LANG']."{$file_id}_errors.lang.xml";
	}
}
