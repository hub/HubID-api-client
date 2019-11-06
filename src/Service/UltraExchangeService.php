<?php
/**
 * @author Tharanga Kothalawala <tharanga.kothalawala@hubculture.com>
 * @since  05-10-2019
 */

namespace Hub\HubAPI\Service;

use InvalidArgumentException;

class UltraExchangeService extends Service
{
    const BASE = '/ultraexchange';

    /**
     * Use this to retrieve all the ultra rates.
     * @see UltraExchangeService::getAssets If you need more information about each asset.
     *
     * @return array
     */
    public function getRates()
    {
        return $this->createResponse($this->get(self::BASE . "/rates"));
    }

    /**
     * Use this to convert a given currency to another currency. This currency can be of any type either Ven or Ultra.
     * You can convert ven to ultra, ultra to ven or ultra to ultra.
     *
     * @param string $fromAssetTicker The currency that you need converting from. This must be the unique currency
     *                                ticker name. The ticker value can be seen in the 'currency_ticker' key sent  in
     *                                the rates API.
     * @param string $toAssetTicker   The destination currency that you need to convert the original currency to.
     * @param float  $amount          [optional] Amount that you need to convert
     *
     * @return float|array
     */
    public function convert($fromAssetTicker, $toAssetTicker, $amount = 1.0)
    {
        if (intval($amount) === 0) {
            throw new InvalidArgumentException('Please specify a conversion amount greater than zero(0)');
        }

        return $this->createResponse($this->get(sprintf(
            self::BASE . "/convert?amount=%s&from=%s&to=%s",
            urlencode($amount),
            $fromAssetTicker,
            $toAssetTicker
        )));
    }

    /**
     * Use this to retrieve all the available ultra asset details.
     *
     * @return array
     */
    public function getAssets()
    {
        return $this->createResponse($this->get(self::BASE . "/assets"));
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
        if (intval($assetId) === 0) {
            throw new InvalidArgumentException('Please specify a valid ultra asset id');
        }

        return $this->createResponse($this->get(self::BASE . "/assets/{$assetId}"));
    }

    /**
     * Use this to purchase a given ultra asset.
     * As long as the authenticated user has the 'Trader' membership level, they should be able to perform a purchase.
     *
     * @param int   $assetId     unique ultra asset identifier.
     * @param float $assetAmount amount of assets that you want to purchase.
     *
     * @return array
     */
    public function purchase($assetId, $assetAmount)
    {
        if (intval($assetId) === 0) {
            throw new InvalidArgumentException('Please specify a valid ultra asset id');
        }

        return $this->createResponse($this->postFormData(
            self::BASE . "/assets/{$assetId}/purchase",
            [
                'amount' => $assetAmount,
            ]
        ));
    }

    /**
     * This returns all the wallets belong to a given user.
     *
     * @param int $userId A valid user id
     *
     * @return array
     */
    public function getUserWallets($userId)
    {
        if (intval($userId) === 0) {
            throw new InvalidArgumentException('Please specify a valid user id');
        }

        return $this->createResponse($this->get(self::BASE . "/wallets/{$userId}"));
    }

    /**
     * This return a user ultra wallet.
     *
     * @param int $userId  A valid user id
     * @param int $assetId A valid ultra asset id
     *
     * @return array
     */
    public function getUserWallet($userId, $assetId)
    {
        if (intval($userId) === 0 || intval($assetId) === 0) {
            throw new InvalidArgumentException('Please specify a valid user id and an asset id');
        }

        return $this->createResponse($this->get(self::BASE . "/wallets/{$userId}/{$assetId}"));
    }
}
