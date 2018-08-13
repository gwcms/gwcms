<?php

class GW_Imap
{
	function __construct($hostport, $user, $pass)
	{
		$this->conn = imap_open('{'.$hostport.'/imap/ssl/novalidate-cert}', $user, base64_decode($pass));
	}
	
	

	function __decodeMessage($message, $encoding)
	{
		switch ($encoding) {
			# 7BIT
			case 0:
				return $message;
			# 8BIT
			case 1:
				return quoted_printable_decode(imap_8bit($message));
			# BINARY
			case 2:
				return imap_binary($message);
			# BASE64
			case 3:
				return imap_base64($message);
			# QUOTED-PRINTABLE
			case 4:
				return quoted_printable_decode($message);
			# OTHER
			case 5:
				return $message;
			# UNKNOWN
			default:
				return "unknown encoding:\n\n".$message;
		} 		
	}
	
	
	function flattenParts($messageParts, $flattenedParts = array(), $prefix = '', $index = 1, $fullPrefix = true) {

		foreach($messageParts as $part) {
			$flattenedParts[$prefix.$index] = $part;
			if(isset($part->parts)) {
				if($part->type == 2) {
					$flattenedParts = self::flattenParts($part->parts, $flattenedParts, $prefix.$index.'.', 0, false);
				}
				elseif($fullPrefix) {
					$flattenedParts = self::flattenParts($part->parts, $flattenedParts, $prefix.$index.'.');
				}
				else {
					$flattenedParts = self::flattenParts($part->parts, $flattenedParts, $prefix);
				}
				unset($flattenedParts[$prefix.$index]->parts);
			}
			$index++;
		}

		return $flattenedParts;

	}	
	
	function getPart($messageNumber, $partNumber, $encoding) {
		
		

		$data = imap_fetchbody($this->conn, $messageNumber, $partNumber);
		

		//d::dumpas([$data,$partNumber, $encoding]);
		
		switch ($encoding) {
			# 7BIT
			case 0:
				return $data;
			# 8BIT
			case 1:
				return quoted_printable_decode(imap_8bit($data));
			# BINARY
			case 2:
				return imap_binary($data);
			# BASE64
			case 3:
				return imap_base64($data);
			# QUOTED-PRINTABLE
			case 4:
				return quoted_printable_decode($data);
			# OTHER
			case 5:
				return $data;
			# UNKNOWN
			default:
				return "unknown encoding:\n\n".$data;
		} 		
	}
	
	

	function getFilenameFromPart($part) {

		$filename = '';

		if($part->ifdparameters) {
			foreach($part->dparameters as $object) {
				if(strtolower($object->attribute) == 'filename') {
					$filename = $object->value;
				}
			}
		}

		if(!$filename && $part->ifparameters) {
			foreach($part->parameters as $object) {
				if(strtolower($object->attribute) == 'name') {
					$filename = $object->value;
				}
			}
		}

		return $filename;

	}	
	
	
	function initStructure($message)
	{
		if(!isset($message->structure))
		{
			$message->structure = imap_fetchstructure($this->conn, $message->mailid);;
			//$message->overview = imap_fetch_overview($this->conn, $message->mailid);

			if(isset($message->structure) && is_array($message->structure->parts))
				$message->structure->parts = self::flattenParts($message->structure->parts);
			
		}		
	}
	
	function fetchContents($message, $partfetch='all')
	{

		$this->initStructure($message);
		
		$struct = $message->structure;
		
		//no parts
		if(!isset($struct->parts) || !is_array($struct->parts)){
			
			
			$message->body = $this->getPart($message->mailid, 1, $struct->encoding);
			return true;
		}
		
		
		
		//with parts		
		
		if(!isset($message->attachments))
			$message->attachments = [];
		
		//flat structure walk (self::flattenParts)
		foreach($message->structure->parts as $partNumber => $part) {
						
			
			if($part->type==0 && $part->subtype=="HTML"){
				if($partfetch!='body')
						continue;
				
				$message->body = $this->getPart($message->mailid, $partNumber, $part->encoding);				
			}else{
				$filename = $this->getFilenameFromPart($part);

				$message->attachments_structure[$partNumber]=(object)['filename'=>$filename, 'encoding'=>$part->encoding];

				if($partfetch!='attachments')
						continue;					

				if($filename) {
					// it's an attachment

					// now do something with the attachment, e.g. save it somewhere
					$message->attachments[]=[
					    'filename'=>$filename, 
					    'data'=>$this->getPart($message->mailid, $partNumber, $part->encoding),
					    'size'=>$part->bytes
					];
				}
				else {
					// don't know what it is
				}				
			}
		}
	}
	

	function getSubject($subject)
	{
		$subj = stripos($subject,'=?utf-8?')!==false ? @iconv_mime_decode($subject, 1, "UTF-8") : $subject;
		$subj = trim($subj);
		
		
		if(strlen($subj) > 500)
			$subj = substr($subj, 0, 500).'...';
		
		return $subj;
	}
	
	function getMessage(&$message, $header=false, $body=false)
	{
		if($header){
			$head = imap_headerinfo($this->conn, $message->mailid);				
			
			//$head=json_encode($head, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			$message->subject = $this->getSubject($head->subject);
			$message->from = isset($head->from[0]->personal) ? $head->from[0]->personal : $head->fromaddress;
			$message->to = $head->toaddress ?? false;
			
			//if($message->to)
			//	$message->toeml = GW_Email_Validator::separateDisplayNameEmail($message->to);;
			
			$message->uid = imap_uid($this->conn, $message->mailid);
			$message->head = $head;
		}
		
		if($body)
		{
			$this->fetchContents($message, 'body');
		}
	}
	

	function getMessages($rules, $options)
	{
		$start =  isset($options['start']) ? $options['start'] : 1;
		$end = imap_num_msg($this->conn);

		$list = [];
		$steps=0;

		$last_msg_id = 0;
		$lastuid="";
		
		
		for($mailid=$start; $mailid <= $end; $mailid++)
		{
			if(isset($options['limit']) &&  $steps > $options['limit'])
				goto sFinish;			
			
			$message = (object)['mailid'=>$mailid];
			$this->getMessage($message, true);
			$lastuid = $message->uid;
			
			$steps++;
			$match = false;
			//d::ldump([$mailid, $message->subject]);

			foreach($rules as $ruleid => $rule)
			{
				if(isset($rule['subject'])){
					if($rule['subject'][0]=='/'){
						//pregmatch search
						if($match = preg_match($rule['subject'], $message->subject) )
							break;
					}else{
						//strpos search
						if($match = strpos($message->subject, $rule['subject'])!==false)
							break;
						
					}
				}

				if(isset($rule['from']))
					if( $match = preg_match($rule['from'], $message->from) )
						break;					
			}

			
			if(!$match)
				continue;;


			//already imported
			
			//
			//if(!isset($options['force']) && $message->head->Flagged=='F')
			//	continue;

			//mark as imported
			//imap_setflag_full($this->conn, $mailid, "\\Seen \\Flagged"); //ST_UID nedeti!

			//in case we need to reset
			//imap_clearflag_full($this->conn, $mailid, "\\Seen \\Flagged");

			if(isset($options['withcontents']))
				$this->getMessage($message, false, true);

			$message->ruleid = $ruleid;

			$list[$mailid]=$message;

			if(isset($options['single']))
				return $message;



			//test
			//if(strpos($message->subject, 'Wizz Air elektroninė sąskaita-faktūra')!==false){
			//	goto sFinish;
			//}				
			//d::ldump([$header, var_export($status)]);
		}

		sFinish:


		return ['messages'=>$list, 'steps'=>$steps, 'lastuid'=>$lastuid];
	}
	
	function flagMessages($ids)
	{
		$status = imap_setflag_full($this->conn, implode(', ', $ids) , "importuotas");
	}
	
	function getIdByUid($uid)
	{
		return imap_msgno($this->conn, $uid);
	}
	
	function close()
	{
		imap_expunge($this->conn);
		imap_close($this->conn);
	}
	
}



