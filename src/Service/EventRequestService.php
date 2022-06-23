<?php
/**
 * @author  Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since   16-09-2018
 */

namespace Hub\HubAPI\Service;

use Hub\HubAPI\Service\Model\Event;
use Hub\HubAPI\Service\Model\File;

class EventRequestService extends TokenRefreshingService
{
    const BASE = '/event';
    const DEFAULT_PAGINATION_LIMIT = 10;

    public function addEventInvitation($event_id, $recipient_id)
    {
        $event_id = intval($event_id);
        $recipient_id = intval($recipient_id);
        return $this->createResponse($this->postFormData(self::BASE."/{$event_id}/invite/{$recipient_id}"));
    }

    public function approveEventInvitation($event_id)
    {
        $event_id = intval($event_id);
        return $this->createResponse($this->put(self::BASE . "/{$event_id}/invite"));
    }

    public function requestEventParticipation($event_id)
    {
        $event_id = intval($event_id);
        return $this->createResponse($this->postFormData(self::BASE . "/{$event_id}/request"));
    }
    
    public function getEventRequestInvitation($group_id)
    {
        $group_id = intval($group_id);
        return $this->createResponse($this->get(self::BASE . "/request-and-invites/list/group/{$group_id}"));
    }

    public function approveEventInvitationRequest($event_id, $recipient_id)
    {
        $event_id = intval($event_id);
        $recipient_id = intval($recipient_id);
        return $this->createResponse($this->put(self::BASE . "/{$event_id}/request/{$recipient_id}"));
    }

    public function declineEventInvitationRequest($event_id, $recipient_id)
    {
        $event_id = intval($event_id);
        $recipient_id = intval($recipient_id);
        return $this->createResponse($this->delete(self::BASE . "/{$event_id}/request/{$recipient_id}"));
    }
}
