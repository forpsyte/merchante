<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Helper;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Merchante\Merchante\Gateway\Config\Config;
use Merchante\Merchante\Model\TaxCodes;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order;
use Magento\Tax\Api\Data\OrderTaxDetailsAppliedTaxInterface;
use Magento\Tax\Api\Data\TaxRateInterface;
use Magento\Tax\Api\OrderTaxManagementInterface;
use Magento\Tax\Api\TaxRateRepositoryInterface;

/**
 * Class Tax
 * @package Merchante\Merchante\Helper
 */
class Tax extends AbstractHelper
{
    /**
     * @var OrderTaxManagementInterface
     */
    protected $orderTaxManagement;

    /**
     * @var Config
     */
    protected $paymentConfig;

    /**
     * @var TaxCodes
     */
    protected $taxCodes;

    /**
     * @var TaxRateRepositoryInterface
     */
    protected $taxRateRepository;

    /**
    * @var FilterBuilder
    */
    protected $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Tax constructor.
     *
     * @param Context $context
     * @param Config $paymentConfig
     * @param FilterBuilder $filterBuilder
     * @param OrderTaxManagementInterface $orderTaxManagement
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TaxCodes $taxCodes
     * @param TaxRateRepositoryInterface $taxRateRepository
     */
    public function __construct(
        Context $context,
        Config $paymentConfig,
        FilterBuilder $filterBuilder,
        OrderTaxManagementInterface $orderTaxManagement,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TaxCodes $taxCodes,
        TaxRateRepositoryInterface $taxRateRepository
    ) {
        $this->paymentConfig = $paymentConfig;
        $this->filterBuilder = $filterBuilder;
        $this->orderTaxManagement = $orderTaxManagement;
        $this->taxCodes = $taxCodes;
        $this->taxRateRepository = $taxRateRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);

    }

    /**
     * @param InfoInterface $payment
     * @return OrderTaxDetailsAppliedTaxInterface[]
     * @throws NoSuchEntityException
     */
    public function getAppliedTaxes(InfoInterface $payment)
    {
        /** @var Order $order */
        $order = $payment->getOrder();
        $extensionAttributes = $order->getExtensionAttributes();

        if ($order->getEntityId() && ($extensionAttributes === null || !$extensionAttributes->getAppliedTaxes())) {
            $appliedTaxes = $this->orderTaxManagement->getOrderTaxDetails($order->getEntityId())->getAppliedTaxes();
        } else {
            if (!$extensionAttributes) {
                $appliedTaxes = [];
            } else {
                $appliedTaxes = $extensionAttributes->getAppliedTaxes();
            }
        }

        return $appliedTaxes;
    }

    /**
     * @param InfoInterface $payment
     * @return array
     * @throws NoSuchEntityException
     * @throws InputException
     */
    public function getAppliedTaxInformation(InfoInterface $payment)
    {
        $appliedTaxInfo = [
            'tax_code' => null,
            'tax_amount' => null,
        ];

        $altTaxRates = $this->paymentConfig->getAlternativeTaxRates();
        $appliedTaxes = $this->getAppliedTaxes($payment);
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('tax_calculation_rate_id', $altTaxRates, 'in')
            ->create();

        /** @var TaxRateInterface[] $result */
        $result = $this->taxRateRepository
            ->getList($searchCriteria)
            ->getItems();

        $altTaxCodes = array_map(function ($item) {
            /** @var  TaxRateInterface $item */
            return $item->getCode();
        }, $result);

        foreach ($appliedTaxes as $appliedTax) {
            $currentTaxCode = $appliedTax->getCode();

            if (in_array($currentTaxCode, $altTaxCodes)) {
                $matchTaxCode = $this->getTaxCodeFromTaxRule($currentTaxCode);
                if (!$matchTaxCode) {
                    continue;
                }

                if (!$this->taxCodes->isValidTaxCode($matchTaxCode)) {
                    continue;
                }

                $appliedTaxInfo['tax_code'] = $matchTaxCode;
                $appliedTaxInfo['tax_amount'] = $appliedTax->getAmount();
                break;
            }
        }

        return $appliedTaxInfo;
    }

    /**
     * @param string $taxId
     * @return bool|mixed
     */
    public function getTaxCodeFromTaxRule($taxId)
    {
        $matches = [];
        $matchTaxCode = preg_match("/^([A-Za-z]+)(?: .*)?/", $taxId, $matches);

        return $matchTaxCode ? $matches[1] : false;
    }
}
