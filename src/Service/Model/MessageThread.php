<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service\Model;

/**
 * Class MessageThread
 * @package Hub\HubAPI\Service\Model
 */
final class MessageThread
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $content;

    /**
     * @var User
     */
    private $sender;

    /**
     * @var User[]
     */
    private $recipients;

    /**
     * @var User[]
     */
    private $participants;

    /**
     * List of tags to be used to categorise the message thread.
     *
     * @var array
     */
    private $tags;

    /**
     * @param string      $content      message thread content.
     * @param User[]      $recipientIds list of recipient/participant Hub Culture users.
     * @param string|null $subject      [optional] subject of this new message thread
     * @param array|null  $tags         [optional] list of tags to be used to categorise the message thread.
     *                                  If a given tag is not found, it will create a new one.
     *
     * @see MessageThreadFactory for creating an instance of MessageThread
     */
    public function __construct($content, array $recipientIds, $subject = null, array $tags = array())
    {
        $this->content = $content;
        $this->recipients = $recipientIds;
        $this->subject = $subject;
        $this->tags = $tags;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return User|null
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param User $sender
     */
    public function setSender(User $sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return User[]
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @param User $recipient a recipient user.
     */
    public function addRecipient(User $recipient)
    {
        $this->recipients[] = $recipient;
    }

    /**
     * @return User[]
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param User $participant
     */
    public function addParticipant(User $participant)
    {
        $this->participants[] = $participant;

        // let's fill the recipients list excluding the sender.
        $this->recipients = array();
        foreach ($this->participants as $participant) {
            if ($participant->getId() == $this->getSender()->getId()) {
                continue;
            }
            $this->recipients[] = $participant;
        }
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags a term used to categorise this message thread.
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $recipientIds = array();
        foreach ($this->getRecipients() as $recipient) {
            $recipientIds[] = $recipient->getId();
        }

        return array(
            'content' => $this->getContent(),
            'recipients' => $recipientIds,
            'subject' => $this->getSubject(),
            'tags' => $this->getTags(),
        );
    }
}
