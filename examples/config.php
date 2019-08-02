<?php

use Hub\HubAPI\HubClient;

include __DIR__ . '/../vendor/autoload.php';

$config = array(
    'base_path' => 'https://id.hubculture.com:466',
    'verify' => false,
    'private_key' => 'private_5d265de1d9204f6235830ce2',
    'public_key' => 'public_153222247f4cbe2511208120a',
    'client_id' => 10014,
    'debug' => true,
);

$hubClient = new HubClient($config);

$redirectLoginHelper = $hubClient->getRedirectingLoginHelper();
