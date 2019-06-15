<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@tsk-webdevelopment.com>
 * @since  : 16-09-2018
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
            $this->request("/user/{$id}")
        );
    }

    public function getSelf()
    {
        return $this->createResponse(
            $this->request("/user")
        );
    }
}
