<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

include __DIR__ . '/config.php';

use Hub\HubAPI\Service\UltraExchangeService;

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
    $service = new UltraExchangeService($config);
    $result = $service->getRates();
    var_dump($result);

    $result = $service->convert('VEN','USD');
    var_dump($result);

    $result = $service->getAssets();
    var_dump($result);

    // retrieving one ultra asset by id
    $result = $service->getAssetById(4);
    var_dump($result);

    $result = $service->getUserWallet(4);
    var_dump($result);

    $result = $service->getUserWalletByPublicKey('338cc261f4070df90fc0a5b6fed1af6a');
    var_dump($result);

    $result = $service->getWalletTransactions();
    var_dump($result);

    $result = $service->getWalletTransactionsByAssetId(4);
    var_dump($result);

    $result = $service->getUserWallets();
    var_dump($result);

    $result = $service->getCurrencyChart();
    var_dump($result);

//    $result = $service->purchase(4, 1);
//    var_dump($result);

//    $result = $service->sell(4, 1);
//    var_dump($result);
}
