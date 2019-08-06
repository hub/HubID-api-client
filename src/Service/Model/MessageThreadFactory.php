<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service\Model;

/**
 * This factory class can create new MessageThread objects from a given raw array of data.
 *
 * @package Hub\HubAPI\Service\Model
 */
final class MessageThreadFactory
{
    /**
     * @param array $data array of raw data coming from the API.
     *
     * @return MessageThread
     */
    public static function fromArray(array $data)
    {
        $messageThread = new MessageThread($data['subject']);
        $messageThread->setId($data['thread']);

        if (!empty($data['creator']) && is_array($data['creator'])) {
            $messageThread->setSender(UserFactory::fromArray($data['creator']));
        }

        if (!empty($data['participants']) && is_array($data['participants'])) {
            foreach ($data['participants'] as $participant) {
                if (!is_array($participant)) {
                    continue;
                }

                $messageThread->addParticipant(UserFactory::fromArray($participant));
            }
        }

        if (!empty($data['tags'])) {
            $messageThread->setTags($data['tags']);
        }

        return $messageThread;
    }
}
