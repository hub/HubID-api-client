<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

include __DIR__ . '/config.php';

use Hub\HubAPI\Service\FriendService;
use Hub\HubAPI\Service\UltraService;
use Hub\HubAPI\Service\UserService;

$redirectUrl = 'http://localhost:8085/ultra-service.php';

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

    // retrieving all ultra assets
    $service = new UltraService($config);
//    $assets = $service->getAllAssets();
//    var_dump($assets);exit;

    // retrieving one ultra asset by id
    $asset = $service->getAssetById(4);
    var_dump($asset);

    $result = $service->purchase(4, 1);
    var_dump($result);
}
