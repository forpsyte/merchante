<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Request\LevelThree;

use DateTime;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Merchantesolutions\Gateway\Config\Config;
use Magento\Merchantesolutions\Gateway\Http\Data\Request;
use Magento\Merchantesolutions\Gateway\SubjectReader;
use Magento\Merchantesolutions\Helper\Tax;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Magento\Sales\Model\Order;

/**
 * Class VisaPaymentDataBuilder
 * @package Magento\Merchantesolutions\Gateway\Request\LevelThree
 */
class VisaPaymentDataBuilder implements BuilderInterface
{
    use Formatter;

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
        $appliedTaxes = $this->taxHelper->getAppliedTaxInformation($payment);
        $taxAmount = $appliedTaxes['tax_amount'];

        /** @var Order $order */
        $order = $payment->getOrder();
        $poNumber = $payment->getAdditionalInformation(Request::FIELD_PO_NUMBER) ?: $order->getIncrementId();

        return [
            Request::FIELD_MERCHANT_TAX_ID => $this->config->getMerchantVatNumber($order->getStoreId()),
            Request::FIELD_SUMMARY_COMMODITY_CODE => $this->config->getValue(Request::FIELD_SUMMARY_COMMODITY_CODE),
            Request::FIELD_DISCOUNT_AMOUNT => $this->formatPrice($order->getDiscountAmount()),
            Request::FIELD_SHIPPING_AMOUNT => $this->formatPrice($order->getShippingAmount()),
            Request::FIELD_DUTY_AMOUNT => 0.00,
            Request::FIELD_VAT_INVOICE_NUMBER => $poNumber,
            Request::FIELD_ORDER_DATE => (new DateTime())->format('ymd'),
            Request::FIELD_VAT_AMOUNT => $this->formatPrice($taxAmount), //TODO: Update for vat amount
        ];
    }
}
