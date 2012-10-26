<?


class GW_DB_Queries extends GW_Data_Object
{
	var $table = 'gw_db_queries';
	var $calculate_fields=Array('title'=>'getTitle');
	
	function getTitle()
	{
		return $this->get('name');
	}
	
}