<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Http\Client;

use Magento\Framework\Exception\LocalizedException;
use Magento\Merchantesolutions\Gateway\Http\Data\Response;

/**
 * Class TransactionSale
 * @package Magento\Merchantesolutions\Gateway\Http\Client
 */
class Transaction extends AbstractTransaction
{
    /**
     * @param array $data
     * @return Response
     * @throws LocalizedException
     */
    public function process(array $data)
    {
        $storeId = $data['store_id'] ?? null;
        return $this->adapterFactory->create($storeId)->execute($data);
    }
}
