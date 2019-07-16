<?php

namespace Hub\HubAPI;

use Hub\HubAPI\Service\Exception\HubIdApiExeption;
use Hub\HubAPI\Service\Service;
use Valitron\Validator;
use Hub\HubAPI\Auth\RedirectingLoginHelper;

class HubClient extends Service
{
    const COOKIE_TOKEN_NAME = 'hubid-api-client';

    private $response;
    private $request;
    private static $token;

    /**
     * @return RedirectingLoginHelper
     * @throws \Exception
     */
    public function getRedirectingLoginHelper()
    {
        return new RedirectingLoginHelper($this->config);
    }

    /**
     * Create a password hash
     * For verification, you need to use - passwordVerify(password, hash).
     *
     * @param string password
     * @return
     * @throws \Exception
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
     * Verifies the password created with help - passwordHash(password).
     *
     * @param string password
     * @param string hash
     * @return
     * @throws \Exception
     */
    public function passwordVerify($password, $hash)
    {
        return $this->request('post', '/password/verify', ['password' => $password, 'hash' => $hash])->getContent();
    }

    /**
     * Refresh token.
     *
     * @param string token - required
     * @return HubClient
     * @throws \Exception
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
     * Authorize on the site, in response we receive a token and user ID.
     *
     * @param array  dataUser
     * @param string dataUser['email'] - required
     * @param string dataUser['password'] - required
     * @return array|string
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

            if (false == $response['status']) {
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

    /**
     * @param null $field
     * @return mixed
     * @throws \Exception
     */
    public function getContent($field = null)
    {
        $objectResponse = $this->response;
        if (isset($objectResponse['error']) && 'token_expired' === $objectResponse['error']) {
            $options = [];
            if (isset($this->request[2])) {
                $options = $this->request[2];
                $options = '['.implode('","', $options).']';
                unset($this->request[2]);
            }

            return eval('return $this->refreshToken($this->getToken())->request("'.implode('","', $this->request).', '.$options.'")->getContent('.$field.');');
        }

        if (!is_null($field) && isset($objectResponse[$field])) {
            return $objectResponse[$field];
        }
        if (!empty($objectResponse['error'])) {
            throw new \Exception($objectResponse['error']);
        }

        $objectResponse['status'] = 'success' === $objectResponse['status'] ? true : false;

        return $objectResponse;
    }

    public function request($method, $uri, $parameters = [])
    {
        $this->request = func_get_args();

        try {
            $this->config['token'] = $this->getToken();
            $this->response = parent::rawRequest($uri, $parameters, $method);
        } catch (HubIdApiExeption $ex) {
            $this->response = $ex->getMessage();
        }

        return $this;
    }

    /**
     * Apply the token to the request.
     *
     * @param string token - required
     * @return HubClient
     */
    private function setToken($token)
    {
        self::$token = $token;
        setcookie(self::COOKIE_TOKEN_NAME, self::$token, time() + 3600 * 24 * 365, '/');

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
