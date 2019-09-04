<?php

namespace Coconut;

class Job {
    public static function create($options=array()) {
        $api_key = self::getApiKey($options);

        return Coconut::submit(Coconut::config($options), $api_key);
    }

    public static function get($jid, $options=array()) {
        $api_key = self::getApiKey($options);

        return Coconut::get('/v1/jobs/' . $jid, $api_key);
    }

    public static function getAllMetadata($jid, $options=array()) {
        $api_key = self::getApiKey($options);

        return Coconut::get('/v1/metadata/jobs/' . $jid, $api_key);
    }

    public static function getMetadataFor($jid, $source_or_output, $options=array()) {
        $api_key = self::getApiKey($options);

        return Coconut::get('/v1/metadata/jobs/' . $jid . '/' . $source_or_output, $api_key);
    }


    private static function getApiKey($options=array()) {
      $api_key = null;
      if(isset($options['api_key'])) {
          $api_key = $options['api_key'];
      }
      return $api_key;
    }
}
