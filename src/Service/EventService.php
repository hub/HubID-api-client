<?php
/**
 * @author : Tharanga Kothalawala <tharanga.kothalawala@tsk-webdevelopment.com>
 * @since  : 16-09-2018
 */

namespace HubID\Service;

class EventService extends Service
{
    public function getEvents($limit = 10)
    {
        return $this->createResponse(
            $this->request("/events/list/group?limit={$limit}")
        );
    }

    public function getEventById($id)
    {
        return $this->createResponse(
            $this->request("/event/{$id}")
        );
    }
}
