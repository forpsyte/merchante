<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Request\LevelThree;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Merchantesolutions\Gateway\Config\Config;
use Magento\Merchantesolutions\Gateway\SubjectReader;
use Magento\Merchantesolutions\Helper\Tax;
use Magento\Merchantesolutions\Model\TaxCodes;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;

/**
 * Class VisaItemsDataBuilder
 * @package Magento\Merchantesolutions\Gateway\Request\LevelThree
 */
class VisaItemsDataBuilder implements BuilderInterface
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
        $taxAmount = $appliedTaxes['tax_amount'];
        $hasVAT = $taxAmount > 0 && $appliedTaxes['tax_code'] === TaxCodes::ValueAddedTax;

        foreach ($orderItems as $item) {
            $result['visa_line_item'][] = implode('<|>', [
                $item->getProduct()->getData('summary_commodity_code') ?: $this->config->getCommodityCode(),
                $item->getName(),
                $item->getSku(),
                $item->getQtyOrdered(),
                'each',
                $item->getPrice(),
                ($hasVAT ? $item->getTaxAmount() : "0.00"),
                ($hasVAT ? $item->getTaxPercent() : "0"),
                $item->getDiscountAmount(),
                ($item->getPriceInclTax() ?: "0.00"),
                'D'
            ]);
        }
        return $result;
    }
}