<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service\Model;

/**
 * This factory class can create new User objects from a given raw array of data.
 *
 * @package Hub\HubAPI\Service\Model
 */
final class UserFactory
{
    /**
     * @param array $data array of raw data coming from the API.
     *
     * @return User
     */
    public static function fromArray(array $data)
    {
        $user = new User(isset($data['id']) ? $data['id'] : 0);
        if (!empty($data['first'])) {
            $user->setFirstName($data['first']);
        }
        if (!empty($data['last'])) {
            $user->setLastName($data['last']);
        }
        if (!empty($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (!empty($data['location'])) {
            $user->setLocation($data['location']);
        }
        if (!empty($data['picture']['large'])) {
            $user->setPicture($data['picture']['large']);
        }

        return $user;
    }
}
