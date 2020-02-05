<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface TransactionSearchResultsInterface
 * @api
 * @package Magento\Merchantesolutions\Api\Data
 */
interface TransactionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get transactions list.
     *
     * @return \Magento\Merchantesolutions\Api\Data\TransactionInterface[]
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