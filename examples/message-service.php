<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

use Hub\HubAPI\Service\MessageService;
use Hub\HubAPI\Service\Model\MessageThread;
use Hub\HubAPI\Service\Model\User;

include __DIR__ . '/config.php';

$redirectUrl = 'http://localhost:8085/message-service.php';

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
    $messageThread = $service->createThread(
        new MessageThread(
            'subject',
            'content',
            array(new User(21025), new User(23874))
        )
    );

    var_dump($messageThread);
    $messageThread = $service->getThread($messageThread->getId());
    var_dump($messageThread);
    var_dump($messageThread->getId());
    $service->tagThread($messageThread->getId(), array('apitest', 'hubidsdk'));
    $threads = $service->sentThreads();
    $threads = $service->inboxUnreadThreads();
    $threads = $service->inboxThreads();
    var_dump($threads);
    $messageThread = $service->replyToThread($messageThread->getId(), "a reply via the api - " . time());
}
