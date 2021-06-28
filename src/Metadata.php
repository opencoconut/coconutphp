<?php

namespace Coconut;

class Metadata
{
    private $api;
    
    function __construct($cli)
    {
        $this->api = $cli->api;
    }
    
    function retrieve($jid)
    {
        return $this->api->request("GET", "/metadata/jobs/" . $jid);
    }
}

?>