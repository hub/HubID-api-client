<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Auth;

use Hub\HubAPI\Service\Exception\HubIdApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Hub\HubAPI\Service\Service;
use Valitron\Validator;

class RedirectingLoginHelper extends Service
{
    /**
     * Use this to redirect the user to the Hub Culture login page.
     *
     * @param string $callbackUrl the url that you need us to redirect the user to after a login attempt.
     */
    public function redirectToLoginUrl($callbackUrl)
    {
        header("Location: {$this->getLoginUrl($callbackUrl)}");
    }

    /**
     * Use this to get the constructed redirection url for our login page.
     * @see RedirectingLoginHelper::redirectToLoginUrl()
     *
     * @param string $callbackUrl the url that you need us to redirect the user to after a login attempt.
     *
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
     * @see RedirectingLoginHelper::redirectToLoginUrl()
     *
     * @param string $accessToken the access token you have already.
     *
     * @return string|null
     */
    public function getRefreshToken($accessToken)
    {
        $this->setAccessToken($accessToken);
        $response = $this->put('/token');
        if (!empty($response['error']) && $response['error'] === 'token_invalid') {
            throw new HubIdApiException('Invalid access token provided');
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
     *
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
            throw new HubIdApiException($ex->getMessage());
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
