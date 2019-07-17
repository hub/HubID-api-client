<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since 15-06-2019
 */

namespace Hub\HubAPI\Service;

class FriendService extends Service
{
    public function getFriends()
    {
        return $this->createResponse(
            $this->get("/friends")
        );
    }
}
