<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Gateway\Request\LevelThree;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Merchante\Merchante\Gateway\Config\Config;
use Merchante\Merchante\Gateway\SubjectReader;
use Merchante\Merchante\Helper\Tax;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;

/**
 * Class VisaItemsDataBuilder
 * @package Merchante\Merchante\Gateway\Request\LevelThree
 */
class MastercardItemsDataBuilder implements BuilderInterface
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
     * @throws InputException
     * @throws NoSuchEntityException
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
        $appliedTaxes = $this->taxHelper->getAppliedTaxInformation($payment);

        foreach ($orderItems as $item) {
            $result['mc_line_item'][] = implode('<|>', [
                $item->getName(),
                $item->getSku(),
                $item->getQtyOrdered(),
                'each',
                '0000',
                $item->getTaxPercent(),
                $appliedTaxes['tax_code'] ?: "",
                $item->getTaxAmount(),
                $item->getDiscountAmount() > 0 ? "Y" : "N",
                $item->getTaxAmount() > 0 ? "Y" : "N",
                $item->getPriceInclTax(),
                'D',
                $item->getDiscountAmount()
            ]);
        }
        return $result;
    }
}
