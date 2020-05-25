<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  15-06-2019
 */

namespace Hub\HubAPI\Service;

class FriendService extends TokenRefreshingService
{
    const BASE = '/friends';
    const DEFAULT_PAGINATION_LIMIT = 10;

    /**
     * This returns all the friends of the current authenticated user.
     *
     * @param int $offset [optional] offset for pagination
     * @param int $limit  [optional] limit for pagination
     *
     * @return array
     * @see UserService::getFriends()
     */
    public function getFriends($offset = 0, $limit = self::DEFAULT_PAGINATION_LIMIT)
    {
        $offset = intval($offset) === 0 ? 0 : intval($offset);
        $limit = intval($limit) === 0 ? self::DEFAULT_PAGINATION_LIMIT : intval($limit);

        return $this->createResponse($this->get(self::BASE . "/?offset={$offset}&limit={$limit}"));
    }

    /**
     * Returns the pending friends associated to the current authenticated user (you).
     * These are the list of users for whom you have sent a friend request to.
     * However they haven't yet added you as a friend yet
     *
     * @return array
     */
    public function getPendingFriends()
    {
        return $this->createResponse($this->get(self::BASE . "/pending"));
    }

    /**
     * Returns the users who have sent the current authenticated user (you) friend requests.
     * You are yet to approve them as your friends.
     *
     * @return array
     */
    public function getFriendRequests()
    {
        return $this->createResponse($this->get(self::BASE . "/requests"));
    }

    /**
     * Returns the full user information of a friend. This returns 'No such friend' if the given user id is not a
     * friend yet.
     *
     * @param int $friendUserId A valid user id of a friend user.
     *
     * @return array
     * @see UserService::getUserById() For retriving any user.
     */
    public function getFriendInfo($friendUserId)
    {
        return $this->createResponse($this->get(self::BASE . "/{$friendUserId}"));
    }

    /**
     * Use this to approve a friend request that the current authenticated user (you) has received.
     *
     * @param int $friendUserId A valid user id of a pending user / friend.
     *
     * @return array
     */
    public function approveFriendRequest($friendUserId)
    {
        return $this->createResponse($this->put("/friend/request/{$friendUserId}"));
    }

    /**
     * Use this to decline a friend request that the current authenticated user (you) has received.
     *
     * @param int $friendUserId A valid user id of a pending user / friend.
     *
     * @return array
     */
    public function declineFriendRequest($friendUserId)
    {
        return $this->createResponse($this->delete("/friend/request/{$friendUserId}"));
    }

    /**
     * Use this to add a new user as a friend.
     *
     * The following errors will be thrown:
     *      'You are already friend with that user'
     *      'A friend request already exists for that user'
     *
     * @param string      $message              A personal note to the receiver of the request.
     *                                          Message length must be between 4 to 500 characters long.
     * @param null|int    $potentialFriendId    A valid user id
     * @param null|string $potentialFriendEmail A valid email address of an existing user in the platform
     *
     * @return array
     */
    public function addFriend($message, $potentialFriendId = null, $potentialFriendEmail = null)
    {
        $payload = array('message' => $message);
        if (!is_null($potentialFriendId) && intval($potentialFriendId) > 0) {
            $payload['id'] = $potentialFriendId;
        }
        if (!is_null($potentialFriendEmail)) {
            $payload['email'] = $potentialFriendEmail;
        }

        return $this->createResponse($this->postFormData(self::BASE, $payload));
    }

    /**
     * Use this to un-friend a existing friend.
     *
     * @param int $friendUserId A valid user id of a friend user.
     *
     * @return array
     */
    public function removeFriend($friendUserId)
    {
        return $this->createResponse($this->delete("/friend/{$friendUserId}"));
    }

    /**
     * Use this to search for friends in the current authenticated user's (your) friend list.
     *
     * @param string $searchKeyword The search term to search for friends in your friend list.
     *                              This can be part of a name or an email address.
     * @param int    $offset        [optional] offset for pagination
     * @param int    $limit         [optional] limit for pagination
     *
     * @return array
     */
    public function searchFriends($searchKeyword, $offset = 0, $limit = self::DEFAULT_PAGINATION_LIMIT)
    {
        if (empty($searchKeyword)) {
            return [];
        }

        $offset = intval($offset) === 0 ? 0 : intval($offset);
        $limit = intval($limit) === 0 ? self::DEFAULT_PAGINATION_LIMIT : intval($limit);
        $searchKeyword = urlencode($searchKeyword);

        return $this->createResponse(
            $this->get("/v2/friends/search?search={$searchKeyword}&offset={$offset}&limit={$limit}")
        );
    }
}
