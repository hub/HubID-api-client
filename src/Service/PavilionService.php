<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2020 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service;

use Hub\HubAPI\Service\Exception\HubIdApiException;
use Hub\HubAPI\Service\Model\File;
use Hub\HubAPI\Service\Model\Pavilion;
use InvalidArgumentException;

/**
 * This service can be used to manage pavilions. Pavilions are location Hub Cuture hosts various events and many more
 * activities.
 *
 * @package Hub\HubAPI\Service
 */
class PavilionService extends TokenRefreshingService
{
    const BASE = '/pavilions';

    /**
     * Use this to create a new pavilion. As part of the process, it also creates a dedicated hub (aka group) with
     * one default project in it.
     *
     * @param Pavilion $pavilion
     *
     * @return array
     */
    public function createPavilion(Pavilion $pavilion)
    {
        return $this->createResponse($this->postFormData(self::BASE, $pavilion->toArray()));
    }

    /**
     * Use this to update an existing pavilion.
     *
     * @param int      $pavilionId A valid existing pavilion id
     * @param Pavilion $pavilion
     *
     * @return array
     */
    public function editPavilion($pavilionId, Pavilion $pavilion)
    {
        if (intval($pavilionId) === 0) {
            throw new InvalidArgumentException('Please specify a valid pavilion id');
        }

        return $this->createResponse($this->put(self::BASE . '/' . $pavilionId, $pavilion->toArray()));
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
     * Please note that if this is called within the first five minutes of the pavilion creation, this will also delete
     * all the related entity data such as groups, group wikis and group project that it created originally.
     * 'related_entities_deleted' key in the response will state the deletion status of other entities.
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
