<?php

namespace Coconut;

class Metadata {
  function __construct($cli) {
    $this->api = $cli->api;
  }

  function retrieve($jid) {
    return $this->api->request("GET", "/metadata/jobs/" . $jid);
  }
}

?>