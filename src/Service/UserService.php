<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@tsk-webdevelopment.com>
 * @since  : 16-09-2018
 */

namespace HubID\Service;

class UserService extends Service
{
    public function getUserById($id)
    {
        return $this->createResponse(
            $this->request("/user/{$id}")
        );
    }
}
