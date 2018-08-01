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
    $token = $HubID->getToken(['email' => 'test@hubculture.com', 'password' => 'mypass']);
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

    $token = ''; // token
    $response = $HubID->setToken($token)->request('get', '/balance')->getContent();
    var_dump($response);

  </dd>

</dl>

**More information** [api.hubculture.com](https://api.hubculture.com/)