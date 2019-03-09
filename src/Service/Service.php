<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@tsk-webdevelopment.com>
 * @since  : 16-09-2018
 */

namespace HubID\Service;

use Exception;
use HubID\Service\Exception\HubIdApiExeption;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Valitron\Validator;

class Service
{
    const API_BASE_PATH = 'https://id.hubculture.com';
    protected $config;
    protected $client;

    public function __construct(array $config)
    {
        $validator = new Validator($config);
        $validator
            ->rule('required', ['private_key', 'public_key', 'token'])
            ->message('{field} - is required');
        if (!$validator->validate()) {
            throw new \Exception('fields: private_key, public_key & token are required');
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

    protected function get($api, $params = array())
    {
        return $this->request($api, 'get', $params);
    }

    protected function post($api, $params = array())
    {
        return $this->request($api, 'post', $params);
    }

    protected function request($api, $method = 'get', $params = array())
    {
        $method = strtolower($method);

        try {
            $response = $this->client->$method(
                sprintf('%s%s', $this->config['base_path'], $api),
                array(
                    'headers' => $this->getHeaders(),
                    'body' => json_encode($params),
                )
            );
        } catch (ClientException $ex) {
            throw new HubIdApiExeption($ex->getMessage());
        } catch (Exception $ex) {
            throw new HubIdApiExeption($ex->getMessage());
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function createResponse($response)
    {
        if (!empty($response['error'])) {
            throw new HubIdApiExeption($response['error']);
        }

        $default = array(
            'total' => 0,
            'offset' => 0,
            'limit' => 0,
            'items' => array(),
        );
        if (empty($response['data'])) {
            return $default;
        }

        return $response['data'];
    }

    private function getHeaders()
    {
        $headers = array(
            'Content-Type' => 'application/json',
            'Public-Key' => $this->config['public_key'],
            'Private-Key' => $this->config['private_key'],
        );

        // inject authorization token if available
        if (!empty($this->config['token'])) {
            $headers['Authorization'] = sprintf('Bearer %s', $this->config['token']);
        }

        return $headers;
    }
}
