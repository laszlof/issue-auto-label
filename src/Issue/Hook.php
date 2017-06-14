<?php

namespace Issue;

class Hook {

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

  public function __construct() {
    $this->_api_token = getenv('API_TOKEN');
    $this->_secret = getenv('SECRET');
    $this->_raw_data = file_get_contents('php://input');
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
    return var_export($this->_getData(), true);
  }
}
