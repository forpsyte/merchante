<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\SearchResultInterface;
use Merchante\Merchante\Model\Transaction;

/**
 * Interface TransactionRepositoryInterface
 * @api
 * @package Merchante\Merchante\Api
 */
interface TransactionRepositoryInterface
{
    /**
     * Save transaction.
     *
     * @param Data\TransactionInterface|Transaction $transaction
     * @return Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\TransactionInterface $transaction);

    /**
     * Retrieve transaction.
     *
     * @param int $entityId
     * @return Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId);

    /**
     * Retrieve transactions specified by search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete transaction.
     *
     * @param Data\TransactionInterface|Transaction $transaction
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\TransactionInterface $transaction);

    /**
     * Delete transaction by ID.
     *
     * @param int $quoteId.
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($quoteId);
}
