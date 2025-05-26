<?php


class gw_mail_inbox extends GW_Data_Object
{
	//public $table = 'mt_triprel_mails';
	private $uncompressedbody='';
	public $calculate_fields = ['title'=>1];	
	
	public $encode_fields = [
	    'attach_list'=>'jsono',
	    'data'=>'jsono',
	];
		
	
	public $ownerkey = 'emails/inbox';
	public $extensions = ['changetrack'=>1, 'attachments'=>1,];
	public $ignored_change_track=['body'=>1,'update_time'=>1];
		

	function compressBody()
	{
		$this->body = gzcompress($this->body, 9);
	}
	
	function decompressBody()
	{
		if($tmp = @gzuncompress($this->body)){
			$this->uncompressedbody = $tmp;
			return $tmp;
		}
				
		return $this->body;
	}


	function getBody()
	{
		if(!$this->uncompressedbody)
			$this->decompressBody();
		
		return $this->uncompressedbody;
	}
	
	
	function calculateField($name) {
		
		if($name=="title" && $this->id)
			return GW_String_Helper::truncate ($this->subject, 50);
		
	}
		
	

}