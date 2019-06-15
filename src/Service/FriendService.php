<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@tsk-webdevelopment.com>
 * @since  : 16-09-2018
 */

namespace Hub\HubAPI\Service;

class FriendService extends Service
{
    public function getFriends()
    {
        return $this->createResponse(
            $this->request("/friends")
        );
    }
}
