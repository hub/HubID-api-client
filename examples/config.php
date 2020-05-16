<?php

use Hub\HubAPI\HubClient;

include __DIR__ . '/../vendor/autoload.php';

$config = array(
    'base_path' => 'https://id.hubculture.com:466',
    'verify' => false,
    'private_key' => getenv('HUBID_PRIVATE_KEY'), // __YOUR_KEY__
    'public_key' => getenv('HUBID_PUBLIC_KEY'), // __YOUR_KEY__
    'client_id' => 10014,
    'debug' => true,
);

$hubClient = new HubClient($config);

$redirectLoginHelper = $hubClient->getRedirectingLoginHelper();
