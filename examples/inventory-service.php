<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2020 by HubCulture Ltd.
 */

include __DIR__ . '/config.php';

use Hub\HubAPI\Service\InventoryService;

$redirectUrl = 'http://localhost:8085/inventory-service.php';

if (empty($_GET['access_token'])) {
    $redirectLoginHelper->redirectToLoginUrl($redirectUrl);
} else {
    $accessToken = $_GET['access_token'];
    echo <<<HTML
<pre>
    <br/><b>Access Token</b>: '{$accessToken}'
</pre>
HTML;

    $config['token'] = $accessToken;

    $service = new InventoryService($config);

    $result = $service->getItems();
    var_dump($result);

    $result = $service->submitItem(
        'test item via api ' . time(),
        10,
        5,
        [1],
        'This is a test item submitted via the hubid api'
    );
    $newItemId = $result['id'];
    var_dump($result);

    $result = $service->editItem(
        $newItemId,
        'test item via api - edited - ' . time(),
        20,
        10.53,
        [2],
        'This is a test item submitted via the hubid api [edited]'
    );
    var_dump($result);

    $result = $service->getItem($newItemId);
    var_dump($result);

//    $result = $service->getItemImages($newItemId);
//    var_dump($result);
//
//    $result = $service->uploadItemImage($newItemId, __DIR__ . '/test.jpg');
//    var_dump($result);
//
//    $result = $service->getItemImages($newItemId);
//    var_dump($result);
//
//    $result = $service->getItem($newItemId);
//    var_dump($result);

    $result = $service->removeItem($newItemId);
    var_dump($result);
}
