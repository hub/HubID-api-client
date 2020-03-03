<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  16-09-2018
 */

namespace Hub\HubAPI\Service;

use Hub\HubAPI\Service\Model\File;

class UserService extends Service
{
    /**
     * Use this to retrieve a user by their id.
     *
     * @param int $id User identifier.
     *
     * @return array
     */
    public function getUserById($id)
    {
        if ($id === 'me') {
            return $this->getSelf();
        }

        return $this->createResponse(
            $this->get("/user/{$id}")
        );
    }

    /**
     * Use this to upload an image to the authenticated user.
     *
     * @param string $absoluteFilePath Absolute file path to an image file. ex: /tmp/test-image.jpg
     *
     * @return array
     */
    public function uploadLogo($absoluteFilePath)
    {
        return $this->createResponse(
            $this->uploadFile("/user/uploadLogo", new File('logo', $absoluteFilePath))
        );
    }

    /**
     * Use this to get the current authenticated user.
     *
     * @return array
     */
    public function getSelf()
    {
        return $this->createResponse(
            $this->get("/user")
        );
    }
}
