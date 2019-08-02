<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service\Model;

final class MessageThreadFactory
{
    /**
     * @param array $data
     *
     * @return MessageThread
     */
    public static function fromArray(array $data)
    {
        $messageThread = new MessageThread($data['subject']);
        $messageThread->setId($data['thread']);
        $messageThread->setSender(UserFactory::fromArray($data['creator']));

        if (is_array($data['participants'])) {
            foreach ($data['participants'] as $participant) {
                $messageThread->addParticipant(UserFactory::fromArray($participant));
            }
        }

        if (!empty($data['tags'])) {
            $messageThread->setTags($data['tags']);
        }

        return $messageThread;
    }
}
