<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

use Hub\HubAPI\Service\EventService;
use Hub\HubAPI\Service\Model\Event;

include __DIR__ . '/config.php';

$redirectUrl = 'http://localhost:8085/event-service.php';
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
    // Creation
//    $event = $service->create(new Event(
//        'example event',
//        'example event description',
//        776,
//        time(),
//        time() + 60 * 60 * 24,
//        '666'
//    ));
    // Retrieval
//    $event = $service->getEventById($event['id']);
//    var_dump($event);
    $events = $service->getEvents(5);
    var_dump($events);
    // Uploading an attachment
//    $attachment = $service->addAttachment($event['id'], __DIR__ . '/test.jpg');
//    $service->removeAttachment($event['id'], $attachment['id']);
//    // Deleting an event
//    $service->deleteById($event['id']);
}
