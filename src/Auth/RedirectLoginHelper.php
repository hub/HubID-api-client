<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@tsk-webdevelopment.com>
 * @since  : 16-09-2018
 */

namespace HubID\Auth;

use HubID\Service\Exception\HubIdApiExeption;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class RedirectLoginHelper
{
    private $config;
    private $client;

    public function __construct(array $config)
    {
        $validator = new Validator($config);
        $validator
            ->rule('required', ['private_key', 'public_key', 'client_id'])
            ->message('{field} - is required');
        if (!$validator->validate()) {
            throw new \Exception('fields: private_key, public_key & client_id are required');
        }

        $this->config = $config;
        if (isset($this->config['verify']) && $this->config['verify'] === false) {
            $this->client = new Client(array('verify' => false));
        } else {
            $this->client = new Client();
        }
    }

    public function getAccessToken($redirectUrl)
    {
        header("Location: {$this->getLoginUrl($redirectUrl)}");
    }

    public function getLoginUrl($redirectUrl)
    {
        return sprintf("%s/oauth/authorization?client_id=%s&redirect_uri=%s&response_type=token",
            $this->config['base_path'],
            $this->config['client_id'],
            $redirectUrl
        );
    }

    public function getRefreshToken($accessToken)
    {
        $response = $this->request(sprintf("/token?grant_type=refresh_token&token=%s", $accessToken));
        if (!empty($response['error']) && $response['error'] === 'token_invalid') {
            throw new HubIdApiExeption('Invalid access token provided');
        }

        if (empty($response['data']['token'])) {
            return null;
        }

        return $response['data']['token'];
    }

    /**
     * returns an access token using the user credentials
     *
     * @param string $username username used to login to the website
     * @param string $password password used to login to the website [plain text]
     * @return string
     */
    public function getAccessTokenByUserCredentials($username, $password)
    {
        $credentials = array(
            'email' => $username,
            'password' => $password,
        );
        $response = $this->request('/auth', 'post', $credentials);
        return $response['data']['token'];
    }

    private function request($api, $method = 'get', $params = array())
    {
        try {
            $response = $this->client->$method(
                sprintf('%s%s', $this->config['base_path'], $api),
                array(
                    'headers' => array(
                        'Public-Key' => $this->config['public_key'],
                        'Private-Key' => $this->config['private_key'],
                        'Content-Type' => 'application/json',
                    ),
                    'body' => json_encode($params),
                )
            );
        } catch (ClientException $ex) {
            throw new HubIdApiExeption($ex->getMessage());
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
