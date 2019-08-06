<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service;

use Hub\HubAPI\Service\Model\MessageThread;
use Hub\HubAPI\Service\Model\MessageThreadFactory;
use Hub\HubAPI\Service\Model\UserFactory;

class MessageService extends Service
{
    /**
     * Use this to create a new message thread with other users.
     * This is equivalent to sending a new message.
     *
     * @param MessageThread $messageThread
     *
     * @return MessageThread
     */
    public function createThread(MessageThread $messageThread)
    {
        $response = $this->createResponse($this->postFormData("/messages/thread", $messageThread->toArray()));
        if (empty($response['thread'])) {
            return $messageThread;
        }

        return MessageThreadFactory::fromArray($response['thread']);
    }

    /**
     * Use this to retrieve an existing message thread by its id.
     *
     * @param int $messageThreadId Existing message thread id.
     *
     * @return MessageThread
     */
    public function getThread($messageThreadId)
    {
        $thread = $this->createResponse($this->get("/v2/messages/thread/{$messageThreadId}"));
        if (empty($thread['items'][0])) {
            return null;
        }

        $item = $thread['items'][0];
        $messageThread = new MessageThread(
            $item['threadsubject'],
            $item['content'],
            array(),
            $item['tags']
        );

        $messageThread->setId($item['id']);
        $messageThread->setSender(UserFactory::fromArray($item['sender']));

        return $messageThread;
    }

    /**
     * Use this to delete an existing message thread by its id.
     *
     * @param int $messageThreadId Existing message thread id.
     */
    public function deleteThread($messageThreadId)
    {
        $this->createResponse($this->delete("/v2/messages/thread/{$messageThreadId}"));
    }

    /**
     * Returns a list of message threads in your inbox.
     *
     * @return MessageThread[]
     */
    public function inboxThreads()
    {
        return $this->extractMessageThreads(
            $this->createResponse($this->get("/v2/messages/thread/inbox", array('type' => 'messages')))
        );
    }

    /**
     * Returns a list of unread message threads in your inbox.
     *
     * @return MessageThread[]
     * @see MessageService::inboxThreads()
     */
    public function inboxUnreadThreads()
    {
        return $this->extractMessageThreads(
            $this->createResponse($this->get("/v2/messages/thread/inbox/unread", array('type' => 'messages')))
        );
    }

    /**
     * Returns a list of sent message threads by you.
     *
     * @return MessageThread[]
     */
    public function sentThreads()
    {
        return $this->extractMessageThreads(
            $this->createResponse($this->get("/v2/messages/thread/sent", array('type' => 'messages')))
        );
    }

    /**
     * Use this to tag a message thread by its id.
     *
     * @param int      $messageThreadId Existing message thread id.
     * @param string[] $tags            comma separated list of tags.
     */
    public function tagThread($messageThreadId, array $tags)
    {
        $this->createResponse($this->put("/messages/tag/thread/{$messageThreadId}", array(
            'tags' => implode(',', $tags),
        )));
    }

    /**
     * Use this to reply to a message thread.
     *
     * @param int    $messageThreadId Existing message thread id.
     * @param string $replyMessage    Your reply message to the given thread.
     */
    public function replyToThread($messageThreadId, $replyMessage)
    {
        $this->createResponse($this->put("/messages/thread/{$messageThreadId}", array(
            'content' => $replyMessage,
        )));
    }

    /**
     * This extracts the messages threads from a given raw api response array.
     *
     * @param array $apiResponse The raw API response.
     *
     * @return array
     */
    private function extractMessageThreads(array $apiResponse)
    {
        if (empty($apiResponse['items'])) {
            return array();
        }

        $messageThreads = array();
        foreach ($apiResponse['items'] as $item) {
            $messageThreads[] = MessageThreadFactory::fromArray($item);
        }

        return $messageThreads;
    }
}
