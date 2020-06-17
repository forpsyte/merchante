<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Merchante\Merchante\Model\HostedCheckout;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderCancellationService
 * @package Merchante\Merchante\Model\HostedCheckout
 */
class OrderCancellationService
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Cancels an order and authorization transaction.
     *
     * @param string $incrementId
     * @return bool
     */
    public function execute(string $incrementId): bool
    {
        $order = $this->getOrder($incrementId);
        if ($order === null) {
            return false;
        }

        $order->cancel();
        $this->orderRepository->save($order);
        return true;
    }

    /**
     * Gets order by increment ID.
     *
     * @param string $incrementId
     * @return OrderInterface|null
     */
    private function getOrder(string $incrementId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(OrderInterface::INCREMENT_ID, $incrementId)
            ->create();

        $items = $this->orderRepository->getList($searchCriteria)
            ->getItems();

        return array_pop($items);
    }
}
