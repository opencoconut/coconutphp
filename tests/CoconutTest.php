<?php

use PHPUnit\Framework\TestCase;

class CoconutTest extends TestCase {

  /*
    To run these tests, you need to set your API key with the
    environment variable `COCONUT_API_KEY`
  */

  public function testSubmitJob() {
    $config = Coconut\Coconut::config(array(
      'source' => 'https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4',
      'webhook' => 'http://mysite.com/webhook',
      'outputs' => array('mp4' => 's3://a:s@bucket/video.mp4')
    ));

    $job = Coconut\Coconut::submit($config);
    $this->assertEquals('processing', $job->{'status'});
    $this->assertTrue($job->{'id'} > 0);
  }

  public function testSubmitBadConfig() {
    $config = Coconut\Coconut::config(array(
      'source' => 'https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4'
    ));

    $job = Coconut\Coconut::submit($config);
    $this->assertEquals('error', $job->{'status'});
    $this->assertEquals('config_not_valid', $job->{'error_code'});
  }

  public function testSubmitConfigWithAPIKey() {
    $config = Coconut\Coconut::config(array(
      'source' => 'https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4'
    ));

    $job = Coconut\Coconut::submit($config, 'k-4d204a7fd1fc67fc00e87d3c326d9b75');
    $this->assertEquals('error', $job->{'status'});
    $this->assertEquals('authentication_failed', $job->{'error_code'});
  }

  public function testGenerateFullConfigWithNoFile() {
    $config = Coconut\Coconut::config(array(
      'vars' => array(
        'vid' => 1234,
        'user' => 5098,
        's3' => 's3://a:s@bucket'
      ),
      'source' => 'https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4',
      'webhook' => 'http://mysite.com/webhook?vid=$vid&user=$user',
      'outputs' => array(
        'mp4' => '$s3/vid.mp4',
        'jpg_200x' => '$s3/thumb.jpg',
        'webm' => '$s3/vid.webm'
      )
    ));

    $generated = join("\n", array(
      'var s3 = s3://a:s@bucket',
      'var user = 5098',
      'var vid = 1234',
      '',
      'set source = https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4',
      'set webhook = http://mysite.com/webhook?vid=$vid&user=$user',
      '',
      '-> jpg_200x = $s3/thumb.jpg',
      '-> mp4 = $s3/vid.mp4',
      '-> webm = $s3/vid.webm'
    ));

    $this->assertEquals($generated, $config);
  }

  public function testGenerateConfigWithFile() {
    $file = fopen('coconut.conf', 'w');
    fwrite($file, 'var s3 = s3://a:s@bucket/video' . "\n" . 'set webhook = http://mysite.com/webhook?vid=$vid&user=$user' . "\n" . '-> mp4 = $s3/$vid.mp4');
    fclose($file);

    $config = Coconut\Coconut::config(array(
      'conf' => 'coconut.conf',
      'source' => 'https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4',
      'vars' => array('vid' => 1234, 'user' => 5098)
    ));

    $generated = join("\n", array(
      'var s3 = s3://a:s@bucket/video',
      'var user = 5098',
      'var vid = 1234',
      '',
      'set source = https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4',
      'set webhook = http://mysite.com/webhook?vid=$vid&user=$user',
      '',
      '-> mp4 = $s3/$vid.mp4'
    ));

    $this->assertEquals($generated, $config);

    unlink('coconut.conf');
  }

  public function testSubmitFile() {
    $file = fopen('coconut.conf', 'w');
    fwrite($file, 'var s3 = s3://a:s@bucket/video' . "\n" . 'set webhook = http://mysite.com/webhook?vid=$vid&user=$user' . "\n" . '-> mp4 = $s3/$vid.mp4');
    fclose($file);

    $job = Coconut\Job::create(array(
      'conf' => 'coconut.conf',
      'source' => 'https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4',
      'vars' => array('vid' => 1234, 'user' => 5098)
    ));

    $this->assertEquals('processing', $job->{'status'});
    $this->assertTrue($job->{'id'} > 0);

    unlink('coconut.conf');
  }

  public function testSetApiKeyInJobOptions() {
    $job = Coconut\Job::create(array(
      'api_key' => 'k-4d204a7fd1fc67fc00e87d3c326d9b75',
      'source' => 'https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4'
    ));

    $this->assertEquals('error', $job->{'status'});
    $this->assertEquals('authentication_failed', $job->{'error_code'});
  }

  public function testGetJobInfo() {
    $config = Coconut\Coconut::config(array(
      'source' => 'https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4',
      'webhook' => 'http://mysite.com/webhook',
      'outputs' => array('mp4' => 's3://a:s@bucket/video.mp4')
    ));

    $job = Coconut\Coconut::submit($config);
    $info = Coconut\Job::get($job->{"id"});

    $this->assertEquals($info->{"id"}, $job->{"id"});
  }

  public function testGetNotFoundJobReturnsNull() {
    $info = Coconut\Job::get(1000);
    $this->assertNull($info);
  }

  public function testGetAllMetadata() {
    $config = Coconut\Coconut::config(array(
      'source' => 'https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4',
      'webhook' => 'http://mysite.com/webhook',
      'outputs' => array('mp4' => 's3://a:s@bucket/video.mp4')
    ));

    $job = Coconut\Coconut::submit($config);
    sleep(4);

    $metadata = Coconut\Job::getAllMetadata($job->{"id"});
    $this->assertNotNull($metadata);
  }

  public function testGetMetadataForSource() {
    $config = Coconut\Coconut::config(array(
      'source' => 'https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4',
      'webhook' => 'http://mysite.com/webhook',
      'outputs' => array('mp4' => 's3://a:s@bucket/video.mp4')
    ));

    $job = Coconut\Coconut::submit($config);
    sleep(4);

    $metadata = Coconut\Job::getMetadataFor($job->{"id"}, 'source');
    $this->assertNotNull($metadata);
  }

  public function testSetAPIVersion() {
    $config = Coconut\Coconut::config(array(
      'source' => 'https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4',
      'webhook' => 'http://mysite.com/webhook?vid=$vid&user=$user',
      'api_version' => 'beta',
      'outputs' => array(
        'mp4' => '$s3/vid.mp4',
      )
    ));

    $generated = join("\n", array(
      '',
      'set api_version = beta',
      'set source = https://s3-eu-west-1.amazonaws.com/files.coconut.co/test.mp4',
      'set webhook = http://mysite.com/webhook?vid=$vid&user=$user',
      '',
      '-> mp4 = $s3/vid.mp4'
    ));

    $this->assertEquals($generated, $config);
  }
}