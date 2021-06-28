# Coconut PHP Library

The Coconut PHP library provides access to the Coconut API for encoding videos, packaging media files into HLS and MPEG-Dash, generating thumbnails and GIF animation.

This library is only compatible with the Coconut API v2.

## Documentation

See the [full documentation](https://docs.coconut.co).

## Installation

To install the Coconut PHP library, you need [composer](http://getcomposer.org) first:

```console
curl -sS https://getcomposer.org/installer | php
```

Edit `composer.json`:

```javascript
{
    "require": {
        "opencoconut/coconut": "3.*"
    }
}
```

Install the depencies by executing `composer`:

```console
php composer.phar install
```

## Usage

The library needs you to set your API key which can be found in your [dashboard](https://app.coconut.co/api). Webhook URL and storage settings are optional but are very convenient because you set them only once.

```php
<?php

require_once('vendor/autoload.php');

$coconut = new Coconut\Client('k-api-key');

$coconut.notification = [
  'type' => 'http',
  'url' => 'https://yoursite/api/coconut/webhook'
];

$coconut.storage = [
  'service' => 's3',
  'bucket' => 'my-bucket',
  'region' => 'us-east-1',
  'credentials' => [
    'access_key_id' => 'access-key',
    'secret_access_key' => 'secret-key'
  ]
];

?>
```

## Creating a job

```php
<?php

try {
  $job = $coconut->job->create([
    'input' => [ 'url' => 'https://mysite/path/file.mp4' ],
    'outputs' => [
      'jpg:300x' => [ 'path' => '/image.jpg' ],
      'mp4:1080p' => [ 'path' => '/1080p.mp4' ],
      'httpstream' => [
        'hls' => [ 'path' => 'hls/' ]
      ]
    ]
  ]);

  print_r($job);

} cacth(Exception $e) {
  echo $e->getMessage();
}

?>
```

## Getting information about a job

```php
$job = $coconut->job->retrieve('OolQXaiU86NFki');
```

## Retrieving metadata

```php
$metadata = $coconut->metadata->retrieve('OolQXaiU86NFki');
```

*Released under the [MIT license](http://www.opensource.org/licenses/mit-license.php).*