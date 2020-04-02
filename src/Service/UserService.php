<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  16-09-2018
 */

namespace Hub\HubAPI\Service;

use Hub\HubAPI\Service\Model\File;

class UserService extends TokenRefreshingService
{
    const BASE = '/user';

    /**
     * Use this to provision a new user in the Hub Culture platform.
     *
     * @param string $firstName   New user's first name
     * @param string $lastName    New user's last name
     * @param string $email       New user's email address. This will be the login username too.
     * @param string $password    New user's new login password
     * @param string $phoneNumber New user's phone number
     *
     * @return array
     */
    public function registerNewUser($firstName, $lastName, $email, $password, $phoneNumber)
    {
        return $this->createResponse(
            $this->postFormData(
                self::BASE,
                [
                    'first' => $firstName,
                    'last' => $lastName,
                    'email' => $email,
                    'password' => $password,
                    'mobile' => $phoneNumber,
                ]
            )
        );
    }

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
            $this->get(self::BASE . "/{$id}")
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
            $this->uploadFile(self::BASE . "/uploadLogo", new File('logo', $absoluteFilePath))
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
            $this->get(self::BASE)
        );
    }
}
