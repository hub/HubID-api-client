<?php namespace HubID;

use HubID\Auth\RedirectLoginHelper;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Valitron\Validator;

class HubAPI
{
    const API_BASE_PATH = 'https://id.hubculture.com';

    private $config;

  public $client;
  public $response;
  public $request;
  public static $token;


  const COOKIE_TOKEN_NAME = 'hubid-api-client';

    public function __construct(array $config)
    {
        $validator = new Validator($config);
        $validator
            ->rule('required', ['private_key', 'public_key'])
            ->message('{field} - is required');
        if (!$validator->validate()) {
            throw new \Exception('fields: private_key & public_key are required');
        }

        $this->config = array_merge(
            array(
                'base_path' => self::API_BASE_PATH,
                'verify' => true,
                // https://hubculture.com/developer/home
                'client_id' => 0,
                'private_key' => '',
                'public_key' => '',
            ),
            $config
        );

        if ($this->config['verify'] === false) {
            $this->client = new Client(array('verify' => false));
        } else {
            $this->client = new Client();
        }
    }

    public function getRedirectLoginHelper()
    {
        return new RedirectLoginHelper($this->config);
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

      if (!empty($response['token'])) {
        $this->setToken($response['token']);
        return $this->success($response);
      }

      if (isset($response['errors']['error'])) {
        return $this->fail($response['errors']['error']);
      }
      if (!isset($response['token']) && !empty($response['error'])) {
        return $this->fail($response['error']);
      }

      if ($response['status'] == false) {
        return $this->fail($response['errors']);
      }
    } catch (\Exception $e) {
      return 'Auth Error!';
    }
    return 'Auth Error!';
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

    if (isset($objectresponse['error']) && 'token_expired' === $objectresponse['error']) {
      return eval('return $this->refreshToken($this->getToken())->request("' . implode('","', $this->request) . '")->getContent(' . $field . ');');
    }

    if (!is_null($field) && isset($objectresponse[$field])) {
      return $objectresponse[$field];
    }
    if (!empty($objectresponse['error'])) {
      // if ($objectresponse['error'] == 'token_invalid') {
      //   $this->logout();
      // }
      throw new \Exception($objectresponse['error']);
    }
    
    $objectresponse['status'] = 'success' === $objectresponse['status'] ? true : false;

    return $objectresponse;
  }

  public function request($method, $uri, $parameters = [])
  {
    $this->request = func_get_args();
    try {
      $this->response = $this->client->$method($this->config['base_path'] . $uri, [
        'headers' => [
          'Public-Key' => $this->config['public_key'],
          'Private-Key' => $this->config['private_key'],
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
