# PHP client library for encoding videos with Coconut

## Install

To install the Coconut PHP library, you need [composer](http://getcomposer.org) first:

```console
curl -sS https://getcomposer.org/installer | php
```

Edit `composer.json`:

```javascript
{
    "require": {
        "opencoconut/coconut": "2.*"
    }
}
```

Install the depencies by executing `composer`:

```console
php composer.phar install
```

## Submitting the job

Use the [API Request Builder](https://app.coconut.co/job/new) to generate a config file that match your specific workflow.

Example of `coconut.conf`:

```ini
var s3 = s3://accesskey:secretkey@mybucket

set webhook = http://mysite.com/webhook/coconut?videoID=$vid

-> mp4  = $s3/videos/video_$vid.mp4
-> webm = $s3/videos/video_$vid.webm
-> jpg:300x = $s3/previews/thumbs_#num#.jpg, number=3
```

Here is the PHP code to submit the config file:

```php
<?php

$job = Coconut_Job::create(array(
  'api_key' => 'k-api-key',
  'conf' => 'coconut.conf',
  'source' => 'http://yoursite.com/media/video.mp4',
  'vars' => array('vid' => 1234)
));

if($job->{'status'} == 'processing') {
  echo $job->{'id'};
} else {
  echo $job->{'error_code'};
  echo $job->{'error_message'};
}

?>
```

You can also create a job without a config file. To do that you will need to give every settings in the method parameters. Here is the exact same job but without a config file:

```php
<?php

$vid = 1234;
$s3 = 's3://accesskey:secretkey@mybucket';

$job = Coconut_Job::create(array(
  'api_key' => 'k-api-key',
  'source' => 'http://yoursite.com/media/video.mp4',
  'webhook' => 'http://mysite.com/webhook/coconut?videoId=' . $vid,
  'outputs' => array(
    'mp4' => $s3 . '/videos/video_' . $vid . '.mp4',
    'webm' => $s3 . '/videos/video_' . $vid . '.webm',
    'jpg:300x' => $s3 . '/previews/thumbs_#num#.jpg, number=3'
  )
));

?>
```

Other example usage:

```php
<?php
// Getting info about a job
$job = Coconut_Job::get(18370773);

// Retrieving metadata
Coconut_Job::getAllMetadata(18370773);

// Retrieving the source file metadata only
Coconut_Job::getMetadataFor(18370773, 'source');
?>
```

Note that you can use the environment variable `COCONUT_API_KEY` to set your API key.

## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Added some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request


*Released under the [MIT license](http://www.opensource.org/licenses/mit-license.php).*

---

* Coconut website: http://coconut.co
* API documentation: http://coconut.co/docs
* Contact: [support@coconut.co](mailto:support@coconut.co)
* Twitter: [@OpenCoconut](http://twitter.com/opencoconut)
