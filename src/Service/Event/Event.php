<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@tsk-webdevelopment.com>
 * @since 16-06-2019
 */

namespace Hub\HubAPI\Service\Event;

class Event
{
    private $name;
    private $description;
    private $groupId;
    private $startTimestamp;
    private $endTimestamp;
    private $coordinates;
    private $address;

    /**
     * @param string $name          Name of the event.
     * @param string $description   Long descriotion of the event.
     * @param int $groupId          The group id where this event should belong to. Events are always part of a hub/group.
     *                              @see https://hubculture.com/hubs
     * @param int $startTimestamp   Start date of the event. Pass a unix timestamp.
     * @param int $endTimestamp     End date of the event. Pass a unix timestamp.
     * @param string $coordinates   The geo coordinates of the event.
     *                              ex: 32.2946,64.7859
     */
    public function __construct($name, $description, $groupId, $startTimestamp, $endTimestamp, $coordinates)
    {
        $this->name = $name;
        $this->description = $description;
        $this->groupId = $groupId;
        $this->startTimestamp = $startTimestamp;
        $this->endTimestamp = $endTimestamp;
        $this->coordinates = $coordinates;
    }

    public function name()
    {
        return $this->name;
    }

    public function description()
    {
        return $this->description;
    }

    public function groupId()
    {
        return $this->groupId;
    }

    public function startTimestamp()
    {
        return $this->startTimestamp;
    }

    public function endTimestamp()
    {
        return $this->endTimestamp;
    }

    public function coordinates()
    {
        return $this->coordinates;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function address()
    {
        return $this->address;
    }
}
