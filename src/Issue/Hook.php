<?php

namespace Issue;

use Symfony\Component\HttpFoundation\Request;

class Hook {

  /**
   * Our Silex application
   * @var \Silex\application
   */
  private $_app;

  /**
   * Our Request object
   * @var Symfony\Component\HttpFoundation\Request
   */
  private $_request;

  /**
   * Github API Token
   * @var string
   */
  private $_api_token = '';

  /**
   * Github Webhook Secret
   * @var string
   */
  private $_secret = '';

  /**
   * Raw data from hook request
   * @var string
   */
  private $_raw_data = '';

  public function __construct(\Silex\Application $app, Request $request) {
    $this->_app = $app;
    $this->_request = $request;
    $this->_api_token = getenv('API_TOKEN');
    $this->_secret = getenv('SECRET');
    $this->_raw_data = $request->getContent();
  }

  /**
   * Check if the webhook is a valid request
   *
   * @return boolean
   */
  public function isValid() : bool {
    $event = $_SERVER['X-Github-Event'];
    $signature = $_SERVER['X-Hub-Signature'];
    list($algo, $sig) = explode('=', $signature);
    $hash = hash_hmac($algo, $this->_raw_data, $this->_secret);

    return $hash === $sig;
  }

  /**
   * Get our data
   *
   * @return \stdClass
   */
  private function _getData() : \stdClass {
    return json_decode($this->_raw_data);
  }

  /**
   * Process our webhook data
   *
   * @return void
   */
  public function process() {
    $this->_app['monolog']->addDebug(var_export($this->_getData(), true));
  }
}
