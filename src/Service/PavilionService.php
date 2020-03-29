<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2020 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service;

use Hub\HubAPI\Service\Exception\HubIdApiException;
use Hub\HubAPI\Service\Model\File;
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
     * Use this to create a new pavilion. As part of the process, it also creates a dedicated hub (aka group) with
     * one default project in it.
     *
     * @param string $name                Name of the new pavilion
     * @param string $localeName          Locale name of the new pavilion. This can be the location for example.
     * @param string $address             Address of the pavilion. This may be a paragraphs explaining what this is.
     * @param string $timezone            Timezone of the pavilion's location. This must be the standard timezone
     *                                    described in this wikipedia page.
     *                                    https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
     *                                    Ex: Europe/London
     * @param string $longitude           Geo longitude of the pavilion's location. Go to http://www.latlong.net
     * @param string $latitude            Geo latitude of the pavilion's location. Go to http://www.latlong.net
     * @param string $pavilionRelativeUrl This is the url slug for the hub culture website.
     *                                    Ex: 'london' as in https://hubculture.com/pavilions/london
     *
     * @param string $territory           [optional] This is the territory where this pavilion belongs to.
     *                                    Ex: Emerald City, California State
     *
     * @param bool   $isVisible           [optional] flag to make this new pavilion visible just after the creation
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
        $territory = null,
        $isVisible = false
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
                    'territory' => is_null($territory) ? '' : $territory,
                    'visible' => ($isVisible) ? 1 : 0,
                ]
            )
        );
    }

    /**
     * Use this to upload an image as the logo to the pavilion
     *
     * @param int    $pavilionId       A valid pavilion identifier.
     * @param string $absoluteFilePath Absolute file path to an image file. ex: /tmp/test-image.jpg
     *
     * @return array
     */
    public function uploadLogo($pavilionId, $absoluteFilePath)
    {
        if (intval($pavilionId) === 0) {
            throw new InvalidArgumentException('Please specify a valid pavilion id');
        }

        return $this->uploadFile(
            sprintf('%s/%d/logo', self::BASE, $pavilionId),
            new File('logo', $absoluteFilePath)
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
     * @param int $groupId A valid group identifier.
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

    /**
     * This hard deletes a given pavilion by its id. You can only deletes the pavilions that you have created.
     *
     * @param int $pavilionId A valid pavilion identifier.
     *
     * @return array
     */
    public function deletePavilions($pavilionId)
    {
        return $this->delete(self::BASE . '/' . $pavilionId);
    }
}
