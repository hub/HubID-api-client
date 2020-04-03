<?php
/**
 * @author  Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since   16-09-2018
 */

namespace Hub\HubAPI\Service;

use Hub\HubAPI\Service\Model\Event;
use Hub\HubAPI\Service\Model\File;

class EventService extends TokenRefreshingService
{
    /**
     * This return any incoming events
     *
     * @param int $limit        [optional] data limit
     * @param int $groupId      [optional] valid hub/group id can be passed to filter events.
     * @param int $startTimeUts [optional] pass a unix timestamp to retrieve events starting after this date.
     *
     * @return array
     */
    public function getEvents($limit = 10, $groupId = null, $startTimeUts = null)
    {
        $url = "/events/list/group?limit={$limit}";
        if (!is_null($groupId)) {
            $url .= "&groupId={$groupId}";
        }
        if (!is_null($startTimeUts)) {
            $url .= "&startTime={$startTimeUts}";
        }

        return $this->createResponse($this->get($url));
    }

    /**
     * @param Event $event the new event value object.
     *
     * @see Event
     *
     * @return array
     */
    public function create(Event $event)
    {
        $payload = [
            'group' => $event->groupId(),
            'title' => $event->name(),
            'description' => $event->description(),
            'address' => $event->address(),
            'start' => $event->startTimestamp(),
            'end' => $event->endTimestamp(),
            'coordinates' => $event->coordinates(),
            'private' => '0',
        ];

        return $this->createResponse($this->postFormData("/events/new", $payload));
    }

    /**
     * Use this to retrieve an event data by its id.
     *
     * @param int $id The event id
     *
     * @return array
     */
    public function getEventById($id)
    {
        return $this->createResponse($this->get("/event/{$id}"));
    }

    /**
     * Use this to upload an image to an event.
     *
     * @param int    $id               The event id
     * @param string $absoluteFilePath Absolute file path to an image file. ex: /tmp/test-image.jpg
     *
     * @return array
     */
    public function addAttachment($id, $absoluteFilePath)
    {
        return $this->createResponse(
            $this->uploadFile("/event/{$id}/attachments", new File('attachment', $absoluteFilePath))
        );
    }

    /**
     * This can be used to delete an attachment of an event.
     *
     * @param int $id           An event identifier.
     * @param int $attachmentId An attachment id belong to the above given event.
     *
     * @return array
     */
    public function removeAttachment($id, $attachmentId)
    {
        return $this->createResponse($this->delete("/event/{$id}/attachment/{$attachmentId}"));
    }

    /**
     * This can be used to delete an event.
     *
     * @param int $id An event identifier.
     *
     * @return array
     */
    public function deleteById($id)
    {
        return $this->createResponse($this->delete("/event/{$id}"));
    }
}
