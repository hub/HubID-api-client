<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service\Message;

use Hub\HubAPI\Service\Service;

class MessageService extends Service
{
    /**
     * Use this to create a new message thread with other users.
     * This is equivalent to sending a new message.
     *
     * @param MessageThread $messageThread
     *
     * @return array
     */
    public function createThread(MessageThread $messageThread)
    {
        return $this->createResponse($this->postFormData("/messages/thread", $messageThread->toArray()));
    }

    /**
     * Returns a list of sent message threads.
     * @return array
     */
    public function sentThreads()
    {
        return $this->createResponse($this->get("/v2/messages/thread/sent", array('type' => 'messages')));
    }
}
