<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  05-10-2019
 */

namespace Hub\HubAPI\Service;

class UltraService extends Service
{
    /**
     * Use this to retrieve all the available ultra asset details.
     *
     * @return array
     */
    public function getAllAssets()
    {
        return $this->createResponse($this->get("/ultraasset/all"));
    }

    /**
     * Use this to retrieve one ultra asset.
     *
     * @param int $assetId unique ultra asset identifier.
     *
     * @return array
     */
    public function getAssetById($assetId)
    {
        return $this->createResponse($this->postFormData("/ultraasset/asset", array('asset_id' => $assetId)));
    }

    /**
     * Use this to purchase a given ultra asset.
     *
     * @param int   $assetId     unique ultra asset identifier.
     * @param float $assetAmount amount of assets that you want to purchase.
     *
     * @return array
     */
    public function purchase($assetId, $assetAmount)
    {
        return $this->createResponse($this->postFormData(
            "/ultraasset/purchase",
            [
                'asset_id' => $assetId,
                'assetAmount' => $assetAmount,
            ]
        ));
    }
}
