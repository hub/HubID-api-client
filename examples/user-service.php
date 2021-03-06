<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

include __DIR__ . '/config.php';

use Hub\HubAPI\Service\FriendService;
use Hub\HubAPI\Service\UserService;

$redirectUrl = 'http://localhost:8085/user-service.php';

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

    // example user data retrieval
    $service = new UserService($config);
    $user = $service->getUserById(18495);
    var_dump($user);
    $user = $service->getSelf();
    var_dump($user);
//    $response = $service->registerNewSubUser('test sub account via API sdk', SubProfileType::bot(), 'object__property');
//    var_dump($response);

    // example friend list retrieval for your authenticated user
    $service = new FriendService($config);
    $friends = $service->getFriends();
    var_dump($friends);

//    $service = new UserService($config);
//    $user = $service->uploadLogo(__DIR__ . '/test.jpg');
}
