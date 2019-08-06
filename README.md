# Hub Culture API ![hubculture logo](https://hubculture.com/images/logo-hub-clear.png)

**Wiki** https://github.com/hub/APIHubID/wiki

**Swagger** [api.hubculture.com](https://api.hubculture.com/)

# Usage

Include the library with composer.

```
composer require hub/hubid-api-client
```

## Authentication

```php
include '/vendor/autoload.php';

use Hub\HubAPI\HubClient;

$redirectUrl = 'http://localhost/callback.php';
$config = array(
    // @see https://hubculture.com/developer/home
    'private_key' => '<your private key>',
    'public_key' => '<your public key>',
    'client_id' => 12345,
);

$hubClient = new HubClient($config);

$redirectLoginHelper = $hubClient->getRedirectLoginHelper();
$redirectLoginHelper->getAccessToken($redirectUrl);
```

## User Service
Retrieving a user by id

```php
include '/vendor/autoload.php';

use Hub\HubAPI\Service\UserService;

$config = array(
    'private_key' => '<your private key>',
    'public_key' => '<your public key>',
    'token' => '<access_token you got from the auth endpoint>',
);

$service = new UserService($config);
$user = $service->getUserById(18495);
var_dump($user);
```

# Examples

Please run the following command to run a PHP server serving examples.

```bash
make demo
```

Browse to [http://localhost:8085/message-service.php](http://localhost:8085/message-service.php).

You may look at examples under examples directory.
