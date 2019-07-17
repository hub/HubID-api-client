<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since 16-09-2018
 */

namespace Hub\HubAPI\Service;

class UserService extends Service
{
    public function getUserById($id)
    {
        if ($id === 'me') {
            return $this->getSelf();
        }

        return $this->createResponse(
            $this->get("/user/{$id}")
        );
    }

    public function getSelf()
    {
        return $this->createResponse(
            $this->get("/user")
        );
    }
}
