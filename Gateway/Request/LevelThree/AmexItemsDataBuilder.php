<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Request\LevelThree;

use Magento\Merchantesolutions\Gateway\Config\Config;
use Magento\Merchantesolutions\Gateway\SubjectReader;
use Magento\Merchantesolutions\Helper\Tax;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;

/**
 * Class VisaItemsDataBuilder
 * @package Magento\Merchantesolutions\Gateway\Request\LevelThree
 */
class AmexItemsDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Tax
     */
    protected $taxHelper;

    /**
     * @param Config $config
     * @param SubjectReader $subjectReader
     * @param Tax $taxHelper
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        Config $config,
        SubjectReader $subjectReader,
        Tax $taxHelper
    ) {
        $this->subjectReader = $subjectReader;
        $this->config = $config;
        $this->taxHelper = $taxHelper;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();

        /** @var Order $order */
        $order = $payment->getOrder();

        /** @var Item[] $orderItems */
        $orderItems = $order->getAllVisibleItems();
        $result = [];
        $result['line_item_count'] = count($orderItems);

        foreach ($orderItems as $item) {
            $result['mc_line_item'][] = implode('<|>', [
                $item->getName(),
                $item->getQtyOrdered(),
                $item->getPrice()
            ]);
        }
        return $result;
    }
}