<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  15-06-2019
 */

namespace Hub\HubAPI\Service;

class FriendService extends Service
{
    /**
     * Returns the friends associated to the current authenticated user.
     * @return array
     */
    public function getFriends()
    {
        return $this->createResponse(
            $this->get("/friends")
        );
    }
}
