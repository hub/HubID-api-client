<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2020 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service;

use Hub\HubAPI\Service\Exception\HubIdApiException;
use InvalidArgumentException;

/**
 * This service can be used to manage pavilions. Pavilions are location Hub Cuture hosts various events and many more
 * activities.
 *
 * @package Hub\HubAPI\Service
 */
class PavilionService extends Service
{
    const BASE = '/pavilions';

    /**
     * Use this to create a new pavilion. By creating a new pavilion, it also created a dedicated hub (aka group) with
     * one default project within the group.
     *
     * @param string $name                Name of the new pavilion
     * @param string $localeName          Locale name of the new pavilion.
     * @param string $address             Address of the pavilion. This may be a paragraphs explaining what this is.
     * @param string $timezone            Timezone of the pavilion's location. This must be the standard timezone
     *                                    described in this wikipedia page.
     *                                    https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
     *                                    Ex: Europe/London
     * @param string $longitude           Geo longitude of the pavilion's location. Go to http://www.latlong.net
     * @param string $latitude            Geo latitude of the pavilion's location. Go to http://www.latlong.net
     * @param string $pavilionRelativeUrl This is the slug for the url.
     *                                    Ex: 'london' as in https://hubculture.com/pavilions/london
     *
     * @param string $territory           [optional] This is the territory where this pavilion belongs to.
     *                                    Ex: Emerald City, California State
     *
     * @return array
     */
    public function createPavilion(
        $name,
        $localeName,
        $address,
        $timezone,
        $longitude,
        $latitude,
        $pavilionRelativeUrl,
        $territory = null
    ) {
        return $this->createResponse(
            $this->postFormData(
                self::BASE,
                [
                    'name' => $name,
                    'localename' => $localeName,
                    'address' => $address,
                    'timezone' => $timezone,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'url' => $pavilionRelativeUrl,
                    'territory' => $territory,
                ]
            )
        );
    }

    /**
     * Use this to retrieve a visible pavilion by a given valid id
     *
     * @param int $pavilionId A valid pavilion identifier.
     *
     * @return array
     * @throws HubIdApiException when no pavilion is found or the pavilion is not visible yet
     */
    public function getPavilionById($pavilionId)
    {
        if (intval($pavilionId) === 0) {
            throw new InvalidArgumentException('Please specify a valid pavilion id');
        }

        return $this->get(sprintf('%s/%d', self::BASE, $pavilionId));
    }

    /**
     * Use this to retrieve visible pavilions by a given group id. Multiple pavilions can be related to one group.
     *
     * @param int $groupId A valid pavilion identifier.
     *
     * @return array
     * @throws HubIdApiException when no pavilion is found or the pavilion is not visible yet
     */
    public function getPavilionByGroupId($groupId)
    {
        if (intval($groupId) === 0) {
            throw new InvalidArgumentException('Please specify a valid group id');
        }

        return $this->get(sprintf('%s/group/%d', self::BASE, $groupId));
    }

    /**
     * This returns all the pavilions.
     *
     * @param string|null $territory [optional] This is the territory where a pavilion belongs. Use this for filtering.
     *                               to. Ex: Emerald City, California State
     *
     * @return array
     */
    public function getPavilions($territory = null)
    {
        return $this->get(self::BASE . '/list?territory=' . $territory);
    }
}
