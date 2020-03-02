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
    const DEFAULT_PAGINATION_LIMIT = 10;

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
     * @throws InvalidArgumentException on invalid conversion amount
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
     * @throws InvalidArgumentException on invalid asset id
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
     * @param int   $assetId                         unique ultra asset identifier.
     * @param float $assetAmount                     amount of assets that you want to purchase.
     * @param float $proposedVenAmountForOneAsset    [optional] a user can propose a different rate instead using the
     *                                               market rate. when buying an asset. The buyer is willing to pay
     *                                               this much in Ven for one ULTRA asset.
     *
     * @return array
     * @throws InvalidArgumentException on invalid user inputs
     */
    public function purchase($assetId, $assetAmount, $proposedVenAmountForOneAsset = 0.0)
    {
        if (intval($assetId) === 0) {
            throw new InvalidArgumentException('Please specify a valid ultra asset id');
        }
        if (floatval($assetAmount) === 0.0) {
            throw new InvalidArgumentException('Please specify a purchase amount greater than 0');
        }
        if (floatval($proposedVenAmountForOneAsset) < 0) {
            throw new InvalidArgumentException('Please propose an amount greater than 0');
        }

        return $this->createResponse($this->postFormData(
            self::BASE . "/assets/{$assetId}/purchase",
            ['amount' => $assetAmount, 'proposed_ven_amount_for_one_asset' => floatval($proposedVenAmountForOneAsset)]
        ));
    }

    /**
     * Use this to sell an existing ultra asset.
     * As long as the authenticated user has the 'Trader' membership level, they should be able to place a sell order.
     *
     * @param int   $assetId                         unique ultra asset identifier.
     * @param float $assetAmount                     amount of assets that you want to sell.
     * @param float $proposedVenAmountForOneAsset    [optional] a user can propose a different rate instead using the
     *                                               market rate when selling an asset. This can be used to make some
     *                                               profit. A matching algorithm is used to match the best buy order
     *                                               for your given price.
     *
     * @return array
     * @throws InvalidArgumentException on invalid user inputs
     */
    public function sell($assetId, $assetAmount, $proposedVenAmountForOneAsset = 0.0)
    {
        if (intval($assetId) === 0) {
            throw new InvalidArgumentException('Please specify a valid ultra asset id');
        }
        if (floatval($assetAmount) === 0.0) {
            throw new InvalidArgumentException('Please specify a sell amount greater than 0');
        }
        if (floatval($proposedVenAmountForOneAsset) < 0) {
            throw new InvalidArgumentException('Please propose an amount greater than 0');
        }

        return $this->createResponse($this->postFormData(
            self::BASE . "/assets/{$assetId}/sell",
            ['amount' => $assetAmount, 'proposed_ven_amount_for_one_asset' => floatval($proposedVenAmountForOneAsset)]
        ));
    }

    /**
     * This returns all the wallet transactions done by the current authenticated user.
     * Transactions include purchases & sell orders.
     *
     * @see UltraExchangeService::getWalletPendingTransactions for pending transactions.
     *
     * @param int $offset [optional] offset for pagination
     * @param int $limit  [optional] limit for pagination
     *
     * @return array
     */
    public function getWalletTransactions($offset = 0, $limit = self::DEFAULT_PAGINATION_LIMIT)
    {
        $offset = intval($offset) === 0 ? 0 : intval($offset);
        $limit = intval($limit) === 0 ? self::DEFAULT_PAGINATION_LIMIT : intval($limit);

        return $this->createResponse(
            $this->get(self::BASE . "/wallets/transactions?offset={$offset}&limit={$limit}")
        );
    }

    /**
     * This returns all the pending wallet transactions done by the current authenticated user.
     * This will only return the pending sell / buy orders.
     *
     * @param int $offset [optional] offset for pagination
     * @param int $limit  [optional] limit for pagination
     *
     * @return array
     */
    public function getWalletPendingTransactions($offset = 0, $limit = self::DEFAULT_PAGINATION_LIMIT)
    {
        $offset = intval($offset) === 0 ? 0 : intval($offset);
        $limit = intval($limit) === 0 ? self::DEFAULT_PAGINATION_LIMIT : intval($limit);

        return $this->createResponse(
            $this->get(self::BASE . "/wallets/pending-transactions?offset={$offset}&limit={$limit}")
        );
    }

    /**
     * This returns all the wallet transactions done by the current authenticated user for a given asset.
     * Transactions include purchases & sell orders.
     *
     * @param int $assetId A valid ultra asset id
     * @param int $offset  [optional] offset for pagination
     * @param int $limit   [optional] limit for pagination
     *
     * @return array
     * @throws InvalidArgumentException on invalid asset id
     */
    public function getWalletTransactionsByAssetId($assetId, $offset = 0, $limit = self::DEFAULT_PAGINATION_LIMIT)
    {
        if (intval($assetId) === 0) {
            throw new InvalidArgumentException('Please specify a valid asset id');
        }
        $offset = intval($offset) === 0 ? 0 : intval($offset);
        $limit = intval($limit) === 0 ? self::DEFAULT_PAGINATION_LIMIT : intval($limit);

        $data = $this->createResponse(
            $this->get(self::BASE . "/wallets/transactions/{$assetId}?offset={$offset}&limit={$limit}")
        );

        if (isset($data['items'])) {
            return $data['items'];
        }

        return $data;
    }

    /**
     * This returns all the wallets belong to the current authenticated user.
     *
     * @return array
     */
    public function getUserWallets()
    {
        return $this->createResponse($this->get(self::BASE . "/wallets"));
    }

    /**
     * This return a user ultra wallet.
     *
     * @param int $assetId A valid ultra asset id
     *
     * @return array
     * @throws InvalidArgumentException on invalid asset id
     */
    public function getUserWallet($assetId)
    {
        if (intval($assetId) === 0) {
            throw new InvalidArgumentException('Please specify a valid asset id');
        }

        return $this->createResponse($this->get(self::BASE . "/wallets/asset/{$assetId}"));
    }

    /**
     * This return a user ultra wallet by a public wallet identifier.
     *
     * @param string $walletPublicKey A valid public key of an ultra asset wallet
     *
     * @see https://hubculture.com/markets/my-wallets To get your wallet public keys
     *
     * @return array
     */
    public function getUserWalletByPublicKey($walletPublicKey)
    {
        return $this->createResponse($this->get(self::BASE . "/wallets/wallet/{$walletPublicKey}"));
    }

    /**
     * This return an array of all the currencies showing their overall view in terms of their rates and the rate
     * changes.
     *
     * Use this if you want to show our exchange's current market index prices.
     */
    public function getCurrencyChart()
    {
        return $this->createResponse($this->get(self::BASE . "/currency-chart"));
    }
}
