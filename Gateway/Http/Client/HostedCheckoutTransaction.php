<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Gateway\Http\Client;

use Magento\Framework\Exception\LocalizedException;
use Merchante\Merchante\Gateway\Http\Data\Response;

/**
 * Class TransactionSale
 * @package Merchante\Merchante\Gateway\Http\Client
 */
class HostedCheckoutTransaction extends AbstractTransaction
{
    /**
     * @param array $data
     * @return Response
     * @throws LocalizedException
     */
    public function process(array $data)
    {
        $storeId = $data['store_id'] ?? null;
        return $this->adapterFactory->create($storeId)->buildResponse($data);
    }
}
