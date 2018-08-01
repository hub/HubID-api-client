# Hub Culture API ![hubculture logo](https://hubculture.com/images/logo-hub-clear.png)

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
    $token = $HubID->auth(['email' => 'test@hubculture.com', 'password' => 'mypass']);
  </dd>
  <dt>Create new user</dt>
  <dd>

    $response = $HubID->request('post', '/user', [
      'first' => 'First name',
      'last' => 'Last name',
      'email' => 'test@hubculture.com',
      'password' => 'yourpassword',
      'mobile' => 'XXXXXXXXXXXX',
    ]);
    $user = $response->getContent();
    $status = $user['status']; // whether registration is successful (true/false)
    $token = $user['data']['token']; // token
    $user_id = $user['data']['user_id']; // ID

  </dd>
  <dt>Authorization</dt>
  <dd>

    $response = $HubID->request('post', '/auth', [
      'email' => 'test@hubculture.com',
      'password' => 'yourpassword',
    ])->getContent();
    $token = $response['data']['token'];

  </dd>
  <dt>Get a balance</dt>

  <dd>

    // step 1 (perform if you have not previously authorized)
    $HubID->auth(['email' => 'test@hubculture.com', 'password' => 'yourpassword']);
    // step 2
    $response = $HubID->request('get', '/balance')->getContent();
    var_dump($response);

  </dd>

</dl>

After authorization, the token data will be stored in the cookie, so you only need to be authorized once. The lifetime of the token will also be automatically renewed.

<dl>
  <dt>Refresh token</dt>
  <dd>

    $oldToken = $HubID->getToken();
    $newToken = $HubID->refreshToken($oldToken);
    var_dump($newToken);
  </dd>
  <dt>To log out of the authorization, use</dt>
  <dd>

    $HubID->logout();
  </dd>
</dl>

**More information** [api.hubculture.com](https://api.hubculture.com/)
