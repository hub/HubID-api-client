<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@tsk-webdevelopment.com>
 * @since  : 16-09-2018
 */

include __DIR__ . '/../vendor/autoload.php';

use Hub\HubAPI\HubClient;
use Hub\HubAPI\Service\EventService;
use Hub\HubAPI\Service\FriendService;
use Hub\HubAPI\Service\UserService;

$redirectUrl = 'http://localhost/APIHubID/examples/user-event-data-retrieval.php';
$config = array(
    'private_key' => 'private_xxxxxxxx',
    'public_key' => 'public_xxxxxxxx',
    'client_id' => 0,
);

$hubClient = new HubClient($config);

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

    // example event data retrieval
    $service = new EventService($config);
    $event = $service->getEventById(141);
    var_dump($event);

    // example user data retrieval
    $service = new UserService($config);
    $user = $service->getUserById(18495);
    var_dump($user);

    // example friend list retrieval for your authenticated user
    $service = new FriendService($config);
    $friends = $service->getFriends();
    var_dump($friends);
}
