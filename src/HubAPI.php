<?php namespace HubID;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Valitron\Validator;

class HubAPI
{
  private $private_key;
  private $public_key;
  private $hubUrl;

  public $client;
  public $response;
  public $request;
  public static $token;


  const COOKIE_TOKEN_NAME = 'hubid-api-client';

  public function __construct($configurations)
  {
    $v = new Validator($configurations);
    $v->rule('required', ['private_key', 'public_key', 'hubUrl'])->message('{field} - is required');
    if (!$v->validate()) {
      throw new \Exception('fields: private_key, public_key and hubUrl is required');
    }
    $this->private_key = $configurations['private_key'];
    $this->public_key = $configurations['public_key'];
    $this->hubUrl = $configurations['hubUrl'];

    $this->client = new Client;
  }

  /**
   * Create a password hash
   * For verification, you need to use - passwordVerify(password, hash)
   * @param string password
   */
  public function passwordHash($password)
  {
    $pass = $this->request('post', '/password/hash', ['password' => $password])->getContent();
    if (!$pass['status'] || empty($pass['data'])) {
      throw new \Exception('Failed to generate password');
    }
    return $pass['data'];
  }

  /**
   * Password verification
   * Verifies the password created with help - passwordHash(password)
   * @param string password
   * @param string hash
   */
  public function passwordVerify($password, $hash)
  {
    $response = $this->request('post', '/password/verify', ['password' => $password, 'hash' => $hash])->getContent();

    return $response;
  }

  /**
   * Apply the token to the request
   * @param string token - required
   */
  private function setToken($token)
  {
    self::$token = $token;
    setcookie(self::COOKIE_TOKEN_NAME, self::$token, time() + 3600 * 24 * 365, '/');
    return $this;
  }

  /**
   * Refresh token
   * @param string token - required
   */
  public function refreshToken($token)
  {
    $newToken = $this->setToken($token)->request('put', '/token')->getContent();
    if (!empty($newToken['data']['token'])) {
      return $this->setToken($newToken['data']['token']);
    }
    return $newToken;
  }

  /**
   * Authorize on the site, in response we receive a token and user ID
   * @param array  dataUser
   * @param string dataUser['email'] - required
   * @param string dataUser['password'] - required
   */
  public function auth($dataUser)
  {
    $v = new Validator($dataUser);
    $v->rule('required', ['email', 'password'])->message('{field} - is required');
    if (!$v->validate()) {
      return $this->fail($v->errors());
    }

    $authorize = $this->request('post', '/auth', $dataUser);
    try {
      $response = $authorize->getContent('data');

      if (isset($response['errors']['error'])) {
        return $this->fail($response['errors']['error']);
      }
      if (!$response['token'] && $response['error']) {
        return $this->fail($response['error']);
      }
      $this->setToken($response['token']);
      return $this->success($response);
    } catch (\Exception $e) {
      return null;
    }
    return null;
  }

  public function getToken()
  {
    if (!empty(self::$token)) {
      return self::$token;
    }
    if (!empty($_COOKIE[self::COOKIE_TOKEN_NAME])) {
      return $_COOKIE[self::COOKIE_TOKEN_NAME];
    }
    return null;
  }

  public function logout()
  {
    self::$token = null;
    setcookie(self::COOKIE_TOKEN_NAME, null, null, '/');
  }

  public function getContent($field = null)
  {
    $objectresponse = json_decode($this->response->getBody()->getContents(), true);
    if ('token_expired' === $objectresponse['error']) {
      return eval('return $this->refreshToken($this->getToken())->request("' . implode('","', $this->request) . '")->getContent();');
    }

    if (!is_null($field) && isset($objectresponse[$field])) {
      return $objectresponse[$field];
    }
    $objectresponse['status'] = 'success' === $objectresponse['status'] ? true : false;

    return $objectresponse;
  }

  public function request($method, $uri, $parameters = [])
  {
    $this->request = func_get_args();
    try {
      $this->response = $this->client->$method($this->hubUrl . $uri, [
        'headers' => [
          'Public-Key' => $this->public_key,
          'Private-Key' => $this->private_key,
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . $this->getToken(),
        ],
        'body' => json_encode($parameters)
      ]);
    } catch (ClientException $e) {
      $this->response = $e->getResponse();
    }
    return $this;
  }

  private function success($data)
  {
    return [
      'status' => true,
      'error' => null,
      'data' => $data,
    ];
  }

  private function fail($error)
  {
    return [
      'status' => false,
      'error' => $error,
      'data' => null,
    ];
  }

}
