<?php

namespace Issue;

use \Symfony\Component\HttpFoundation\Request;
use \Silex\Application;
use \Github\Client as Client;

class Hook {

  /**
   * Our Silex application
   * @var \Silex\Application
   */
  private $_app;

  /**
   * Our Request object
   * @var \Symfony\Component\HttpFoundation\Request
   */
  private $_request;

  /**
   * Our github client
   * @var \Github\Client
   */
  private $_github;

  /**
   * Github Webhook Secret
   * @var string
   */
  private $_secret = '';

  /**
   * Github label to apply to issue
   * @var string
   */
  private $_label = '';

  /**
   * Raw data from hook request
   * @var string
   */
  private $_raw_data = '';

  /**
   * Regex to match issue links
   * @var string
   */
  const ISSUE_REGEX = '/(?:close|closes|closed|fix|fixes|fixed|resolve|resolves|resolved)\shttps:\/\/github.com\/(\w+)\/(\w+)\/issues\/([0-9]+)/i';

  /**
   * Construct this object
   *
   * @param \Silex\Application $app
   * @param \Symfony\Component\HttpFoundation\Request $request
   */
  public function __construct(Application $app, Request $request) {
    $this->_app = $app;
    $this->_request = $request;
    $token = getenv('API_TOKEN');
    $this->_github = new Client();
    $this->_github->authenticate($token, Client::AUTH_HTTP_TOKEN);
    $this->_secret = getenv('SECRET');
    $this->_label = getenv('GITHUB_LABEL');
    $this->_raw_data = $request->getContent();
  }

  /**
   * Check if the webhook is a valid request
   *
   * @return boolean
   */
  public function isValid() : bool {
    $event = $this->_request->headers->get('X-Github-Event');
    $signature = $this->_request->headers->get('X-Hub-Signature');
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
    if ($this->_getData()->action === 'opened') {
      $body = $this->_getData()->pull_request->body;
      preg_match_all(self::ISSUE_REGEX, $body, $matches, PREG_SET_ORDER);
      if ($matches) {
        foreach ($matches as $m) {
          $user = $m[1];
          $repo = $m[2];
          $issue_id = $m[3];
          $r = $this->_github->api('issue')->labels()
            ->add($user, $repo, $issue_id, $this->_label);
        }
      }
    }
  }
}
