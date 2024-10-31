<?php

class Tele2SMSApi
{
    private $apiUrl = "https://api.smsverslui.tele2.lt/api/notificationsms";
    private $authToken;

    public function __construct($authToken)
    {
        $this->authToken = $authToken;
    }

    /**
     * Send SMS to specified recipients.
     *
     * @param string $senderName Name of the sender (must be active and purchased).
     * @param array $recipients Array of recipient phone numbers with country code.
     * @param string $message Message to be sent.
     * @return array Response from API with request ID or error details.
     */
    public function sendSMS($senderName, $recipients, $message)
    {
        $data = [
            'senderName' => $senderName,
            'recipients' => $recipients,
            'message' => $message
        ];

        $response = $this->makeRequest('POST', $this->apiUrl, $data);
        return $response;
    }

    //po 5s grazina state=1, maziau nei 5 sek nieko negrazina
    /**
     * Retrieve information about a previously sent message.
     *
     * @param string $requestId UUID of the request to retrieve details.
     * @return array Response from API with message status or error details.
     */
    public function getSMSStatus($requestId)
    {
        $url = $this->apiUrl . '/' . $requestId;
        $response = $this->makeRequest('GET', $url);
        return $response;
    }

    /**
     * Make an HTTP request to the API.
     *
     * @param string $method HTTP method (GET, POST).
     * @param string $url API endpoint.
     * @param array|null $data Data to send in request body (for POST requests).
     * @return array API response as associative array.
     */
    private function makeRequest($method, $url, $data = null)
    {
        $headers = [
            'Authorization: Bearer ' . $this->authToken,
            'Content-Type: application/json'
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
        ];

        if ($method === 'POST' && $data) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}

