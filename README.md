# Hub Culture API

<dl>
  <dt>Connection</dt>
  <dd>

    composer require hub/hubid-api-client
  </dd>
  <dt>Using</dt>
  <dd>

    use HubID\HubAPI;
    
    $config = [
      'private_key' => '',
      'public_key' => '',
      'hubUrl' => 'https://id.hubculture.com',
    ];

    $HubID = new HubAPI($config);
    
    // hash password
    $passHash = $HubID->passwordHash('mypass');

    // authorize
    $token = $HubID->getToken(['email' => 'test@hubculture.com', 'password' => 'mypass'];
  </dd>
</dl>
