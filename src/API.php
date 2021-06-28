<?php
namespace Coconut;

class API
{
    const USER_AGENT = 'Coconut/v2 PHPBindings/' . Coconut::VERSION;
    public $cli;
    
    function __construct($cli)
    {
        $this->cli = $cli;
    }
    
    function request($method, $path, $data = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->cli->getEndpoint() . $path);
        curl_setopt($ch, CURLOPT_USERPWD, $this->cli->api_key . ":");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err = curl_errno($ch);
        curl_close($ch);
        if (!$curl_err) {
            if ($http_code > 399) {
                if ($http_code == 400 || $http_code == 401) {
                    $err = json_decode($result);
                    throw new Error($err->{"message"} . " (" . $err->{"error_code"} . ")");
                } else {
                    throw new Error("Server returned HTTP status " . $http_code . " (server_error)");
                }
            }
        } else {
            throw new Error("A Curl Error occured (request_error)");
        }
        
        return json_decode($result);
    }
}

