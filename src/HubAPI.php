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
  public static $token = null;

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

  public function test()
  {
    return 'Hello';
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
   * Authorize on the site, in response we receive a token and user ID
   * @param array  dataUser
   * @param string dataUser['email'] - required
   * @param string dataUser['password'] - required
   */
  public function getToken($dataUser)
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
      self::$token = $response['token'];
      return $this->success($response);
    } catch (\Exception $e) {
      return null;
    }
    return null;
  }

  public function getContent($field = null)
  {
    $objectresponse = json_decode($this->response->getBody()->getContents(), true);
    if (!is_null($field) && isset($objectresponse[$field])) {
      return $objectresponse[$field];
    }
    $objectresponse['status'] = 'success' === $objectresponse['status'] ? true : false;
    return $objectresponse;
  }

  public function request($method, $uri, $parameters = [])
  {
    try {
      $this->response = $this->client->$method($this->hubUrl . $uri, [
        'headers' => [
          'Public-Key' => $this->public_key,
          'Private-Key' => $this->private_key,
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . self::$token,
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
