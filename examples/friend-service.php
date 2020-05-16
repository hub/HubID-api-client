<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2020 by HubCulture Ltd.
 */

use Hub\HubAPI\Service\Exception\HubIdApiException;
use Hub\HubAPI\Service\FriendService;

include __DIR__ . '/config.php';

$redirectUrl = 'http://localhost:8085/friend-service.php';
if (empty($_GET['access_token'])) {
    $redirectLoginHelper->redirectToLoginUrl($redirectUrl);
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
    $service = new FriendService($config);

    $potentialFriendId = 21025;

    try {
        $response = $service->addFriend('A friend request via the API SDK', $potentialFriendId);
        var_dump($response);
    } catch (HubIdApiException $ex) {
        $service->removeFriend($potentialFriendId);
        $response = $service->addFriend('A friend request via the API SDK', $potentialFriendId);
    }
    var_dump($service->getPendingFriends());
    var_dump($service->removeFriend($potentialFriendId));
    var_dump($service->getPendingFriends());
}
