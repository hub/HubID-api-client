<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2020 by HubCulture Ltd.
 */

include __DIR__ . '/config.php';

use Hub\HubAPI\Service\Model\Pavilion;
use Hub\HubAPI\Service\PavilionService;

$redirectUrl = 'http://localhost:8085/pavilion-service.php';

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

    $service = new PavilionService($config);

    $pavilion = new Pavilion();
    $pavilion->setName('name-' . time())
        ->setDescription('description-' . time())
        ->setAddress('address-' . time())
        ->setPavilionRelativeUrl('url-' . time())
        ->setTimezone('Europe/London')
        ->setLatitude(51.507351)
        ->setLongitude(-0.127758)
        ->setTerritory('test-territory')
        ->setIsVisible(false)
        ->setIsPrivate(true);
    $result = $service->createPavilion($pavilion);
    $newPavilionId = $result['pavilion']['id'];
    var_dump($result);

    $pavilion = new Pavilion();
    $pavilion->setName('name [edited] at : ' . time())
        ->setDescription('description [edited] at : ' . time())
        ->setAddress('address [edited] at : ' . time())
        ->setIsVisible(true)
        ->setIsPrivate(false);
    $result = $service->editPavilion($newPavilionId, $pavilion);
    var_dump($result);

    $result = $service->getPavilionById($newPavilionId);
    var_dump($result);

    $result = $service->deletePavilions($newPavilionId);
    var_dump($result);

    $result = $service->getPavilionById($newPavilionId);
    var_dump($result);
}
