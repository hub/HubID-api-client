<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  : 16-09-2018
 */

namespace Hub\HubAPI\Service;

use Exception;
use Hub\HubAPI\Service\Exception\HubIdApiExeption;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Valitron\Validator;

class Service
{
    const API_BASE_PATH = 'https://id.hubculture.com';

    /**
     * @var array runtime configuration containing the credentials etc.
     */
    protected $config;

    /**
     * @var Client curl client.
     */
    protected $client;

    /**
     * Service constructor.
     * @param array $config runtime configuration containing the credentials etc.
     * @throws Exception when required config keys are not found.
     */
    public function __construct(array $config)
    {
        $validator = new Validator($config);
        $validator
            ->rule('required', ['private_key', 'public_key'])
            ->message('{field} - is required');
        if (!$validator->validate()) {
            throw new \Exception('fields: private_key, public_key are required');
        }

        $this->config = array_merge(
            array(
                'base_path' => self::API_BASE_PATH,
                'verify' => true,

                // this will write any request to a log file
                'debug' => false,

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

    protected function get($api, array $params = array())
    {
        return $this->requestWithForm($api, 'get', $params);
    }

    protected function postJson($api, array $params = array())
    {
        return $this->requestWithJson($api, 'post', $params);
    }

    protected function postFormData($api, array $params = array())
    {
        return $this->requestWithForm($api, 'post', $params);
    }

    protected function uploadFile($api, array $files = array())
    {
        return $this->rawRequest(
            $api,
            array('headers' => $this->getHeaders(), 'multipart' => array($files)),
            'post'
        );
    }

    protected function delete($api, array $params = array())
    {
        return $this->requestWithForm($api, 'delete', $params);
    }

    protected function requestWithJson($api, $method = 'get', array $params = array())
    {
        return $this->rawRequest(
            $api,
            array('headers' => $this->getHeaders(), 'body' => json_encode($params)),
            $method
        );
    }

    protected function requestWithForm($api, $method = 'get', array $params = array())
    {
        return $this->rawRequest(
            $api,
            array('headers' => $this->getHeaders(), 'form_params' => $params),
            $method
        );
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

    /**
     * @param string $api api endpoint
     * @param array $payload request data. ex: form submission data.
     * @param string $method HTTP method to use.
     * @return array
     */
    protected function rawRequest($api, array $payload, $method = 'get')
    {
        $method = strtolower($method);
        $errorResponse = null;

        try {
            $this->debug($api, $method, $payload);
            /** @var ResponseInterface $response */
            $response = $this->client->$method(
                sprintf('%s%s', $this->config['base_path'], $api),
                $payload
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $ex) {
            $errorResponse = $ex->getResponse()->getBody()->getContents();
        } catch (Exception $ex) {
            $errorResponse = $ex->getMessage();
        }

        if (!is_null($errorResponse)) {
            $errorResponseDecoded = json_decode($errorResponse, true);
            if (!empty($errorResponseDecoded['errors'])) {
                if (is_array($errorResponseDecoded['errors'])) {
                    throw new HubIdApiExeption(print_r($errorResponseDecoded['errors'], true));
                } else {
                    throw new HubIdApiExeption($errorResponseDecoded['errors']);
                }
            }

            throw new HubIdApiExeption($errorResponse);
        }

        return [];
    }

    private function getHeaders()
    {
        $headers = array(
            'Public-Key' => $this->config['public_key'],
            'Private-Key' => $this->config['private_key'],
        );

        // inject authorization token if available
        if (!empty($this->config['token'])) {
            $headers['Authorization'] = sprintf('Bearer %s', $this->config['token']);
        }

        return $headers;
    }

    private function debug($api, $method, array $payload)
    {
        if (!$this->config['debug']) {
            return;
        }

        $string = "curl --insecure -X%s '%s' %s %s";
        $dataString = array();
        $headerString = array();

        // headers
        if (!empty($payload['headers']) && is_array($payload['headers'])) {
            foreach ($payload['headers'] as $header => $value) {
                $headerString[] = "-H '{$header}:{$value}'";
            }
        }
        $headerString = implode(' ', $headerString);

        if (!empty($payload['body'])) {
            // if json
            $dataString[] = $payload['body'];
            $dataString = "--data " . (implode(' ', $dataString));
        } else if (!empty($payload['form_params']) && is_array($payload['form_params'])) {
            // if form data
            foreach ($payload['form_params'] as $formParam => $value) {
                $dataString[] = sprintf("-F '%s=%s'", $formParam, $value);
            }

            $dataString = (implode(' ', $dataString));
        } else {
            $dataString = '';
        }

        $string = sprintf($string, strtoupper($method), $this->config['base_path'] . $api, $headerString, $dataString);
        file_put_contents('/tmp/hubid-api-client.log', $string . PHP_EOL, FILE_APPEND);
    }
}
