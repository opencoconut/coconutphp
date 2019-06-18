<?php

namespace Coconut;

class Coconut {

  const COCONUT_URL = "https://api.coconut.co";
  const USER_AGENT = "Coconut/2.6.0 (PHP)";

  public static function submit($config_content, $api_key=null) {
    $coconut_url = self::COCONUT_URL;

    if(!$api_key) {
      $api_key = getenv("COCONUT_API_KEY");
    }

    if($url = getenv("COCONUT_URL"))
      $coconut_url = $url;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $coconut_url . "/v1/job");
    curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $config_content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Length: ' . strlen($config_content),
      'Content-Type: text/plain',
      'Accept: application/json')
    );

    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
  }

  public static function get($path, $api_key=null) {
    $coconut_url = self::COCONUT_URL;

    if(!$api_key) {
      $api_key = getenv("COCONUT_API_KEY");
    }

    if($url = getenv("COCONUT_URL"))
      $coconut_url = $url;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $coconut_url . $path);
    curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: text/plain',
      'Accept: application/json')
    );

    $result = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($http_status != 200) {
      return null;
    }

    return json_decode($result);
  }

  public static function config($options=array()) {
    $conf = array();
    if(isset($options['conf'])) {
      $conf_file = $options['conf'];
      if($conf_file != null) {
        $conf = explode("\n", trim(file_get_contents($conf_file)));
      }
    }

    if(isset($options['vars'])) {
      $vars = $options['vars'];
      if($vars != null) {
        foreach ($vars as $name => $value) {
          $conf[] = 'var ' . $name . ' = ' . $value;
        }
      }
    }

    if(isset($options['source'])) {
      $source = $options['source'];
      if($source != null) {
        $conf[] = 'set source = ' . $source;
      }
    }

    if(isset($options['webhook'])) {
      $webhook = $options['webhook'];
      if($webhook != null) {
        $conf[] = 'set webhook = ' . $webhook;
      }
    }

    if(isset($options['outputs'])) {
      $outputs = $options['outputs'];
      if($outputs != null) {
        foreach ($outputs as $format => $cdn) {
          $conf[] = '-> ' . $format . ' = ' . $cdn;
        }
      }
    }

    // Reformatting the generated config
    $new_conf = array();

    $vars_arr = array_filter($conf, function($l) {
      return (0 === strpos($l, 'var'));
    });

    sort($vars_arr);
    $new_conf = array_merge($new_conf, $vars_arr);
    $new_conf[] = '';

    $set_arr = array_filter($conf, function($l) {
      return (0 === strpos($l, 'set'));
    });

    sort($set_arr);
    $new_conf = array_merge($new_conf, $set_arr);
    $new_conf[] = '';

    $out_arr = array_filter($conf, function($l) {
      return (0 === strpos($l, '->'));
    });

    sort($out_arr);
    $new_conf = array_merge($new_conf, $out_arr);

    return join("\n", $new_conf);
  }
}

class Coconut_Job {
    public static function create($options=array()) {
        $api_key = null;
        if(isset($options['api_key'])) {
            $api_key = $options['api_key'];
        }

        return Coconut::submit(Coconut::config($options), $api_key);
    }

    public static function get($jid, $options=array()) {
        $api_key = null;
        if(isset($options['api_key'])) {
            $api_key = $options['api_key'];
        }

        return Coconut::get('/v1/jobs/' . $jid, $api_key);
    }

    public static function getAllMetadata($jid, $options=array()) {
        $api_key = null;
        if(isset($options['api_key'])) {
            $api_key = $options['api_key'];
        }

        return Coconut::get('/v1/metadata/jobs/' . $jid, $api_key);
    }

    public static function getMetadataFor($jid, $source_or_output, $options=array()) {
        $api_key = null;
        if(isset($options['api_key'])) {
            $api_key = $options['api_key'];
        }

        return Coconut::get('/v1/metadata/jobs/' . $jid . '/' . $source_or_output, $api_key);
    }
}
