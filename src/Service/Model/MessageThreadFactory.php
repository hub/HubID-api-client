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
        $subject = !empty($data['subject']) ? $data['subject'] : $data['threadsubject'];
        $messageThread = new MessageThread($data['content'], array(), $subject);
        $messageThread->setId($data['thread']);

        if (!empty($data['creator']) && is_array($data['creator'])) {
            $messageThread->setSender(UserFactory::fromArray($data['creator']));
        }
        if (!empty($data['sender']) && is_array($data['sender'])) {
            $messageThread->setSender(UserFactory::fromArray($data['sender']));
        }

        if (!empty($data['participants']) && is_array($data['participants'])) {
            foreach ($data['participants'] as $participant) {
                if (is_array($participant)) {
                    $messageThread->addParticipant(UserFactory::fromArray($participant));
                }
            }
        }

        if (!empty($data['recipients']) && is_array($data['recipients'])) {
            foreach ($data['recipients'] as $recipients) {
                if (is_array($recipients)) {
                    $messageThread->addRecipient(UserFactory::fromArray($recipients));
                }
            }
        }

        if (!empty($data['tags'])) {
            $messageThread->setTags($data['tags']);
        }

        return $messageThread;
    }
}
