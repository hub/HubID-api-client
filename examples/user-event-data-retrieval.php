<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@tsk-webdevelopment.com>
 * @since  : 16-09-2018
 */

include __DIR__ . '/../vendor/autoload.php';

use HubID\HubAPI;
use HubID\Service\EventService;
use HubID\Service\UserService;

$redirectUrl = 'http://localhost/APIHubID/examples/user-event-data-retrieval.php';
$config = array(
    'private_key' => 'private_xxxxxxxx',
    'public_key' => 'public_xxxxxxxx',
    'client_id' => 0,
);

$hubClient = new HubAPI($config);

$redirectLoginHelper = $hubClient->getRedirectLoginHelper();

if (empty($_GET['access_token'])) {
    $redirectLoginHelper->getAccessToken($redirectUrl);
} else {
    $accessToken = $_GET['access_token'];
    $refreshedToken = $redirectLoginHelper->getRefreshToken($accessToken);
    echo <<<HTML
<pre>
    <br/><b>Access Token</b>: '{$accessToken}'
    <br/><b>Refresh Token</b>: '{$refreshedToken}'
</pre>
HTML;

    $config['token'] = $accessToken;

    // example event data retrival
    $service = new EventService($config);
    $event = $service->getEventById(141);
    var_dump($event);

    // example user data retrival
    $service = new UserService($config);
    $user = $service->getUserById(18495);
    var_dump($user);
}
