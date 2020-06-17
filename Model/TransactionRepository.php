<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Merchante\Merchante\Api\Data;
use Merchante\Merchante\Api\TransactionRepositoryInterface;
use Merchante\Merchante\Model\ResourceModel\Transaction as ResourceTransaction;
use Merchante\Merchante\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;

/**
 * Class TransactionRepository
 * @package Merchante\Merchante\Model
 */
class TransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @var ResourceTransaction
     */
    protected $resource;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var TransactionCollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Data\TransactionSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * TransactionRepository constructor.
     * @param ResourceTransaction $resource
     * @param TransactionFactory $transactionFactory
     * @param TransactionCollectionFactory $transactionCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param Data\TransactionSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceTransaction $resource,
        TransactionFactory $transactionFactory,
        TransactionCollectionFactory $transactionCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        Data\TransactionSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->transactionFactory = $transactionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(Data\TransactionInterface $transaction)
    {
        try {
            $this->resource->save($transaction);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the page: %1', $exception->getMessage()),
                $exception
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getById($entityId)
    {
        /** @var Transaction $transaction */
        $transaction = $this->transactionFactory->create();
        $transaction = $this->resource->load($transaction, $entityId, Transaction::ENTITY_ID);
        if (!$transaction->getId()) {
            throw new NoSuchEntityException(__('The Transaction with the "%1" ID doesn\'t exist.', $entityId));
        }
        return $transaction;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var  \Merchante\Merchante\Model\ResourceModel\Transaction\Collection $collection */
        $collection = $this->transactionCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var Data\TransactionSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(Data\TransactionInterface $transaction)
    {
        try {
            $this->resource->delete($transaction);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the transaction: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($entityId)
    {
        try {
            $this->resource->delete($this->getById($entityId));
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the transaction: %1', $exception->getMessage())
            );
        }
        return true;
    }
}
