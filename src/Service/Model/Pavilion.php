<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2020 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service\Model;

/**
 * This represents a pavilion entity on Hub Culture platform.
 *
 * @package Hub\HubAPI\Service\Model
 */
final class Pavilion
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string Name of the new pavilion
     */
    private $name;

    /**
     * @var string Description of the new pavilion.
     */
    private $description;

    /**
     * @var string Address of the pavilion. This may be a paragraphs explaining what this is.
     */
    private $address;

    /**
     * @var string Timezone of the pavilion's location. This must be the standard timezone described in this wikipedia
     *      page. https://en.wikipedia.org/wiki/List_of_tz_database_time_zones Ex: Europe/London
     */
    private $timezone;

    /**
     * @var string Geo longitude of the pavilion's location. Go to http://www.latlong.net
     */
    private $longitude;

    /**
     * @var string Geo latitude of the pavilion's location. Go to http://www.latlong.net
     */
    private $latitude;

    /**
     * @var string This is the url slug for the hub culture website.
     *      Ex: 'london' as in https://hubculture.com/pavilions/london
     */
    private $pavilionRelativeUrl;

    /**
     * @var null|string This is the territory where this pavilion belongs to. Ex: Emerald City, California State
     */
    private $territory;

    /**
     * @var bool flag to make this new pavilion visible or hidden just after the creation
     */
    private $isVisible;

    /**
     * @var bool flag to make this new pavilion a private. Even if the visibility is set to true, marking a pavilion as
     *      private will prevent it from appearing on user interfaces.
     *      Ex: https://hubculture.com/pavilions
     */
    private $isPrivate;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id A valid pavilion identifier
     *
     * @return Pavilion
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Pavilion
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Pavilion
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return Pavilion
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     *
     * @return Pavilion
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     *
     * @return Pavilion
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     *
     * @return Pavilion
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @return string
     */
    public function getPavilionRelativeUrl()
    {
        return $this->pavilionRelativeUrl;
    }

    /**
     * @param string $pavilionRelativeUrl
     *
     * @return Pavilion
     */
    public function setPavilionRelativeUrl($pavilionRelativeUrl)
    {
        $this->pavilionRelativeUrl = $pavilionRelativeUrl;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getTerritory()
    {
        return $this->territory;
    }

    /**
     * @param null|string $territory
     *
     * @return Pavilion
     */
    public function setTerritory($territory)
    {
        $this->territory = $territory;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * @param bool $isVisible
     *
     * @return Pavilion
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * @param bool $isPrivate
     *
     * @return Pavilion
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = array(
            'name' => $this->getName(),
            'localename' => $this->getDescription(),
            'address' => $this->getAddress(),
            'timezone' => $this->getTimezone(),
            'longitude' => $this->getLongitude(),
            'latitude' => $this->getLatitude(),
            'url' => $this->getPavilionRelativeUrl(),
            'territory' => $this->getTerritory(),
            'visible' => $this->isVisible(),
            'is_private' => $this->isPrivate(),
        );

        if (!is_null($this->getId())) {
            $data['id'] = $this->getId();
        }
        if (!is_null($this->isVisible())) {
            $data['visible'] = ($this->isVisible()) ? 1 : 0;
        }
        if (!is_null($this->isPrivate())) {
            $data['is_private'] = ($this->isPrivate()) ? 1 : 0;
        }

        return $data;
    }
}
