<?php
/**
 * @author        Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @copyright (c) 2020 by HubCulture Ltd.
 */

namespace Hub\HubAPI\Service;

use Hub\HubAPI\Service\Model\File;
use InvalidArgumentException;

/**
 * This service can be used to add new items to the marketplace to be sold via the available pavilion stores/locations
 * and to manage the items as a merchant user or a super administrator.
 *
 * And you a api user must, they must have 'Merchant' role assigned to submit/create new store items.
 * As a user who has the 'Super Administrator' role can manage the inventory in terms of approving or refusing the
 * submitted items.
 *
 * @package Hub\HubAPI\Service
 */
class InventoryService extends TokenRefreshingService
{
    const BASE = '/inventory';
    const DEFAULT_PAGINATION_LIMIT = 10;

    /**
     * Use this to submit a new item to the marketplace as a merchant.
     *
     * @param string      $name        Name of the item
     * @param string      $quantity    Available quantity that you are willing to display within the store.
     * @param float       $price       Price of the item in Ven please.
     * @param int[]|null  $locationIds [optional] Location id to mark where this item is available to buy.
     *                                 If none given, this item will be available in all stores online.
     *
     * @see InventoryService::getStoreLocations()
     *
     * @param string|null $description [optional] A brief description about the item.
     *
     * @return array
     */
    public function submitItem($name, $quantity, $price, array $locationIds = array(), $description = null)
    {
        if (intval($quantity) <= 0) {
            throw new InvalidArgumentException('You must provide the available quantity greater than zero(0)');
        }

        $ids = array();
        foreach ($locationIds as $locationId) {
            if (intval($locationId) > 0) {
                $ids[] = $locationId;
            }
        }

        // if no ids detected in the location array, set 0 by default to mark this item as available in all stores.
        if (empty($ids)) {
            $ids[] = 0;
        }

        return $this->createResponse(
            $this->postFormData(
                self::BASE . '/itemcreate',
                array(
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'quantity' => $quantity,
                    'locations' => $ids,
                )
            )
        );
    }

    /**
     * Use this to edit an existing item in the marketplace as a merchant. Editing an item will take it out of the
     * marketplace and will subject to approval.
     *
     * @param int         $itemId      A valid item id.
     * @param string|null $name        [optional] Name of the item
     * @param string|null $quantity    [optional] Available quantity that you are willing to display within the store.
     * @param float|null  $price       [optional] Price of the item in Ven please.
     * @param int[]|null  $locationIds [optional] Location id to mark where this item is available to buy.
     *                                 If none given, this item will be available in all stores online.
     *
     * @see InventoryService::getStoreLocations()
     *
     * @param string|null $description [optional] A brief description about the item.
     *
     * @return array
     */
    public function editItem(
        $itemId,
        $name = null,
        $quantity = null,
        $price = null,
        array $locationIds = array(),
        $description = null
    ) {
        if (intval($quantity) < 0) {
            throw new InvalidArgumentException('You must provide the available quantity greater than zero(0)');
        }

        $payload = array();
        if (!empty($name)) {
            $payload['name'] = $name;
        }
        if (!empty($quantity)) {
            $payload['quantity'] = $quantity;
        }
        if (!empty($price)) {
            $payload['price'] = $price;
        }
        if (!empty($description)) {
            $payload['description'] = $description;
        }
        $ids = array();
        foreach ($locationIds as $locationId) {
            if (intval($locationId) > 0) {
                $ids[] = $locationId;
            }
        }
        if (empty($ids)) {
            $payload['locations'] = $ids;
        }

        return $this->createResponse($this->postFormData(self::BASE . "/item/{$itemId}", $payload));
    }

    /**
     * Use this to retrieve all the items available to manage as a administrator user.
     *
     * The authenticated user must have the 'Super Administrator' role.
     *
     * @param int $offset [optional] offset for pagination
     * @param int $limit  [optional] limit for pagination
     *
     * @return array
     */
    public function getItemsToManage($offset = 0, $limit = self::DEFAULT_PAGINATION_LIMIT)
    {
        $offset = intval($offset) === 0 ? 0 : intval($offset);
        $limit = intval($limit) === 0 ? self::DEFAULT_PAGINATION_LIMIT : intval($limit);

        return $this->createResponse($this->get(self::BASE . "/items/management?offset={$offset}&limit={$limit}"));
    }

    /**
     * Use this to perform a management action on a valid marketplace item.
     *
     * Actions:
     * refuse - the item will be marked as moderated and will be invisible
     * approve - the item will be visible in the marketplace
     *
     * @param int    $itemId A valid item id.
     * @param string $action The action to perform against. Possible values are: approve, refuse
     *
     * @return array
     */
    public function manageByAction($itemId, $action)
    {
        return $this->createResponse($this->put(self::BASE . "/item/{$itemId}/action", array('action' => $action)));
    }

    /**
     * Use this to retrieve all the items submitted by the current authenticated user.
     *
     * The authenticated user must have the 'Merchant' role.
     *
     * @param int $offset [optional] offset for pagination
     * @param int $limit  [optional] limit for pagination
     *
     * @return array
     */
    public function getItems($offset = 0, $limit = self::DEFAULT_PAGINATION_LIMIT)
    {
        $offset = intval($offset) === 0 ? 0 : intval($offset);
        $limit = intval($limit) === 0 ? self::DEFAULT_PAGINATION_LIMIT : intval($limit);

        return $this->createResponse($this->get(self::BASE . "/items?offset={$offset}&limit={$limit}"));
    }

    /**
     * Use this to get an item by its id as a merchant.
     *
     * @param int $itemId A valid item id.
     *
     * @return array
     */
    public function getItem($itemId)
    {
        return $this->createResponse($this->get(self::BASE . "/item/{$itemId}"));
    }

    /**
     * Use this to get an item's images by its id as a merchant.
     *
     * @param int $itemId A valid item id.
     *
     * @return array
     */
    public function getItemImages($itemId)
    {
        return $this->createResponse($this->get(self::BASE . "/item/{$itemId}/images"));
    }

    /**
     * Use this to get an item's images by its id as a merchant.
     *
     * @param int    $itemId           A valid item id.
     * @param string $absoluteFilePath Absolute file path to an image file. ex: /tmp/test-image.jpg
     *
     * @return array
     */
    public function uploadItemImage($itemId, $absoluteFilePath)
    {
        return $this->createResponse(
            $this->uploadFile(self::BASE . "/item/{$itemId}/image", new File('attachment', $absoluteFilePath))
        );
    }

    /**
     * Use this to get all the stores where the whole inventory is distributed as a merchant.
     *
     * @return array
     */
    public function getStoreLocations()
    {
        return $this->createResponse($this->get(self::BASE . '/locations'));
    }

    /**
     * Use this to delete an item by its id as a merchant.
     *
     * @param int $itemId A valid item id.
     *
     * @return array
     */
    public function removeItem($itemId)
    {
        return $this->createResponse($this->delete(self::BASE . "/item/{$itemId}/delete"));
    }
}
