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
use Hub\HubAPI\Service\Event\Event;

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

    // example event creation and retrieval
    $service = new EventService($config);
    $events = $service->getEvents(10);
    var_dump($events);
    $event = $service->create(new Event(
        'example event',
        'example event description',
        776,
        time(),
        time() + 60 * 60 * 24,
        '666'
    ));
    $event = $service->getEventById($event['id']);
    var_dump($event);
    $attachment = $service->addAttachment($event['id'], __DIR__ . '/../Desert.jpg');
    $service->removeAttachment($event['id'], $attachment['id']);
    $service->deleteById($event['id']);

    // example user data retrieval
    $service = new UserService($config);
    $user = $service->getUserById(18495);
    var_dump($user);

    // example friend list retrieval for your authenticated user
    $service = new FriendService($config);
    $friends = $service->getFriends();
    var_dump($friends);
}
