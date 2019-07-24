<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  : 16-09-2018
 */

include __DIR__ . '/../vendor/autoload.php';

use Hub\HubAPI\HubClient;
use Hub\HubAPI\Service\FriendService;
use Hub\HubAPI\Service\Message\MessageService;
use Hub\HubAPI\Service\Message\MessageThread;
use Hub\HubAPI\Service\UserService;
use Hub\HubAPI\Service\Event\Event;

$redirectUrl = 'http://localhost:8085/user-event-data-retrieval.php';
$config = array(
    'base_path' => 'https://id.hubculture.com:466',
    'verify' => false,
    'private_key' => 'private_5d265de1d9204f6235830ce2',
    'public_key' => 'public_153222247f4cbe2511208120a',
    'client_id' => 10014,
);

$hubClient = new HubClient($config);

$redirectLoginHelper = $hubClient->getRedirectingLoginHelper();

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
    $service = new MessageService($config);
    $events = $service->createThread(new MessageThread('subject', 'content', array(21025, 23874)));
//    var_dump($events);
    $events = $service->sentThreads();
    var_dump($events);
}
