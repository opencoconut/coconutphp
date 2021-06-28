<?php

namespace Coconut;

class Client
{
    const ENDPOINT = "https://api.coconut.co/v2";
    public $api_key;
    public $region;
    public $endpoint;
    public $storage;
    public $notification;
    
    function __construct($api_key, $config = [])
    {
        $this->api_key = $api_key;
        if (isset($config["region"])) {
            $this->region = $config["region"];
        }
        if (isset($config["endpoint"])) {
            $this->endpoint = $config["endpoint"];
        }
        if (isset($config["storage"])) {
            $this->storage = $config["storage"];
        }
        if (isset($config["notification"])) {
            $this->notification = $config["notification"];
        }
        $this->api = new API($this);
        $this->job = new Job($this);
        $this->metadata = new Metadata($this);
    }
    
    function getEndpoint()
    {
        if ($this->endpoint != null) {
            return $this->endpoint;
        }
        if ($this->region != null) {
            return "https://api-" . $this->region . ".coconut.co/v2";
        }
        
        return self::ENDPOINT;
    }
}

