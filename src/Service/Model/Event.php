<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  16-06-2019
 */

namespace Hub\HubAPI\Service\Model;

/**
 * This class a represents an event belong to a hub/group in the Hub Culture platform
 * @package Hub\HubAPI\Service\Event
 */
final class Event
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var int */
    private $groupId;

    /** @var int */
    private $startTimestamp;

    /** @var int */
    private $endTimestamp;

    /** @var string */
    private $coordinates;

    /** @var string */
    private $address;

    /**
     * @param string $name           Name of the event.
     * @param string $description    Long descriotion of the event.
     * @param int    $groupId        The group id where this event should belong to. Events are always part of a
     *                               hub/group. @see https://hubculture.com/hubs
     * @param int    $startTimestamp Start date of the event. Pass a unix timestamp.
     * @param int    $endTimestamp   End date of the event. Pass a unix timestamp.
     * @param string $coordinates    The geo coordinates of the event.
     *                               ex: 32.2946,64.7859
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

    /**
     * Returns the event name.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Returns the event description.
     *
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * Returns the event belonging group/hub.
     *
     * @return int
     */
    public function groupId()
    {
        return $this->groupId;
    }

    /**
     * Returns the starting time of the event as in the form of a unix timestamp.
     *
     * @return int
     */
    public function startTimestamp()
    {
        return $this->startTimestamp;
    }

    /**
     * Returns the ending time of the event as in the form of a unix timestamp.
     *
     * @return int
     */
    public function endTimestamp()
    {
        return $this->endTimestamp;
    }

    /**
     * Returns the GEO coordinates of this event.
     *
     * @return string
     */
    public function coordinates()
    {
        return $this->coordinates;
    }

    /**
     * Use this to set a location to this event.
     *
     * @param string $address Address/location of the event. Ex: London, United Kingdom
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Returns the location to this event.
     *
     * @return string
     */
    public function address()
    {
        return $this->address;
    }
}
