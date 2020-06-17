<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface TransactionSearchResultsInterface
 * @api
 * @package Merchante\Merchante\Api\Data
 */
interface TransactionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get transactions list.
     *
     * @return \Merchante\Merchante\Api\Data\TransactionInterface[]
     */
    public function getItems();

    /**
     * Set transactions list.
     *
     * @param array $items
     * @return $this
     */
    public function setItems(array $items);
}
