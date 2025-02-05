<?php
class BulkGateSMSApi {
    private $apiUrl = "https://portal.bulkgate.com/api/1.0/simple/transactional";
    private $appId;
    private $appToken;

    public function __construct($appId, $appToken) {
        $this->appId = $appId;
        $this->appToken = $appToken;
    }

    public function sendSMS($phoneNumber, $message, $sender = "labway.eu") {
        $data = [
            "application_id" => $this->appId,
            "application_token" => $this->appToken,
            "number" => $phoneNumber,
            "text" => $message,
            "sender_id_value" => $sender,
	    'sender_id' => "gText"
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}