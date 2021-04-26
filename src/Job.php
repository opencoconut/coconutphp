<?php

namespace Coconut;

class Job {
  function __construct($cli) {
    $this->cli = $cli;
    $this->api = $cli->api;
  }

  function create($data=array()) {
    if($this->cli->storage) {
      $data["storage"] = $this->cli->storage;
    }

    if($this->cli->notification) {
      $data["notification"] = $this->cli->notification;
    }

    return $this->api->request("POST", "/jobs", $data);
  }

  function retrieve($jid) {
    return $this->api->request("GET", "/jobs/" . $jid);
  }
}

?>