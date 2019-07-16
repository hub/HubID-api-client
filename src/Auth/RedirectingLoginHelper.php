<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  : 16-09-2018
 */

namespace Hub\HubAPI\Auth;

use Hub\HubAPI\Service\Exception\HubIdApiExeption;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Valitron\Validator;

class RedirectingLoginHelper
{
    const API_BASE_PATH = 'https://id.hubculture.com';
    private $config;
    private $client;

    /**
     * RedirectLoginHelper constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        $validator = new Validator($config);
        $validator
            ->rule('required', ['private_key', 'public_key', 'client_id'])
            ->message('{field} - is required');
        if (!$validator->validate()) {
            throw new \Exception('fields: private_key, public_key & client_id are required');
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
            $this->client = new Client(array('verify' => true));
        }
    }

    /**
     * Use this to redirect the user to the Hub Culture login page.
     *
     * @param string $callbackUrl the url that you need us to redirect the user to after a login attempt.
     */
    public function getAccessToken($callbackUrl)
    {
        header("Location: {$this->getLoginUrl($callbackUrl)}");
    }

    /**
     * Use this to get the constructed redirection url for our login page.
     * @see RedirectingLoginHelper::getAccessToken()
     *
     * @param string $callbackUrl the url that you need us to redirect the user to after a login attempt.
     * @return string the URL to Hub Culture login page.
     */
    public function getLoginUrl($callbackUrl)
    {
        return sprintf("%s/oauth/authorization?client_id=%s&redirect_uri=%s&response_type=token",
            $this->config['base_path'],
            $this->config['client_id'],
            $callbackUrl
        );
    }

    /**
     * Use this to get a refresh token.
     * @see RedirectingLoginHelper::getAccessToken()
     *
     * @param string $accessToken the access token you have already.
     * @return string|null
     */
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
        if (empty($response['data']['token'])) {
            return null;
        }

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
