<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2019 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service\Message;

class MessageThread
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $content;

    /**
     * @var array
     */
    private $recipients;

    /**
     * List of tags to be used to categorise the message thread.
     *
     * @var array
     */
    private $tags;

    /**
     * @param string $subject      subject of this new message thread.
     * @param string $content      thread message content.
     * @param array  $recipientIds [optional] list of recipient/participant ids.
     * @param array  $tags         [optional] list of tags to be used to categorise the message thread. If a given tag
     *                             is not found, it will create a new one.
     */
    public function __construct($subject, $content, array $recipientIds = array(), array $tags = array())
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->recipients = $recipientIds;
        $this->tags = $tags;
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
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @param array $recipientId a recipient/participant ids.
     */
    public function addRecipient($recipientId)
    {
        $this->recipients[] = $recipientId;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tag a term used to categorise this message thread.
     */
    public function addTag($tag)
    {
        $this->tags[] = $tag;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'subject' => $this->getSubject(),
            'content' => $this->getContent(),
            'recipients' => $this->getRecipients(),
            'tags' => $this->getTags(),
        );
    }
}
