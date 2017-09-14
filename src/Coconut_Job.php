<?php

namespace Coconut;

class Coconut_Job {
    public static function create($options=array()) {
        $api_key = null;
        if(isset($options['api_key'])) {
            $api_key = $options['api_key'];
        }

        return Coconut::submit(Coconut::config($options), $api_key);
    }
}