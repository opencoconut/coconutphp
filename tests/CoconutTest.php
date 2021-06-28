<?php

use PHPUnit\Framework\TestCase;

class CoconutTest extends TestCase {

  const INPUT_URL = "https://s3-eu-west-1.amazonaws.com/files.coconut.co/bbb_800k.mp4";

  public function testCoconutClient() {
    $api_key = getenv("COCONUT_API_KEY");

    $coconut = new Coconut\Client($api_key);
    $this->assertEquals($api_key, $coconut->api_key);
    $this->assertEquals("https://api.coconut.co/v2", $coconut->getEndpoint());
  }

  public function testCoconutClientWithRegion() {
    $coconut = new Coconut\Client(getenv("COCONUT_API_KEY"), ["region" => "us-west-2"]);
    $this->assertEquals("https://api-us-west-2.coconut.co/v2", $coconut->getEndpoint());
  }

  public function testCoconutClientWithEndpoint() {
    $coconut = new Coconut\Client(getenv("COCONUT_API_KEY"), ["endpoint" => "http://localhost:3001/v2"]);
    $this->assertEquals("http://localhost:3001/v2", $coconut->getEndpoint());
  }

  public function testJobCreation() {
    $coconut = new Coconut\Client(getenv("COCONUT_API_KEY"));

    $coconut->storage = [
      "service" => "s3",
      "bucket" => getenv("AWS_BUCKET"),
      "region" => getenv("AWS_REGION"),
      "path" => "/coconutphp/tests/",
      "credentials" => [
        "access_key_id" => getenv("AWS_ACCESS_KEY_ID"),
        "secret_access_key" => getenv("AWS_SECRET_ACCESS_KEY")
      ]
    ];

    $coconut->notification = [
      "type" => "http",
      "url" => getenv("COCONUT_WEBHOOK_URL")
    ];

    $job = $coconut->job->create([
      "input" => ["url" => CoconutTest::INPUT_URL],
      "outputs" => [
        "mp4" => [
          "path" => "video.mp4"
        ]
      ]
    ]);

    $this->assertEquals("job.starting", $job->{"status"});
  }

  public function testJobError() {
    $coconut = new Coconut\Client(getenv("COCONUT_API_KEY"));

    try {
      $job = $coconut->job->create([
        "outputs" => [
          "mp4" => [
            "path" => "video.mp4"
          ]
        ]
      ]);
    } catch(Exception $e) {
      $this->assertEquals("The job 'input' is not a valid object. (input_not_valid)", $e->getMessage());
    }
  }
}