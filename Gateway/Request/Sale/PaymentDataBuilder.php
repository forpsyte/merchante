<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Gateway\Request\Sale;

use Merchante\Merchante\Gateway\Config\Config;
use Merchante\Merchante\Gateway\Http\Data\Request;
use Merchante\Merchante\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Magento\Sales\Model\Order\Payment;


/**
 * Class PaymentDataBuilder
 * @package Merchante\Merchante\Gateway\Request
 */
class PaymentDataBuilder implements BuilderInterface
{
    use Formatter;

    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @param Config $config
     * @param SubjectReader $subjectReader
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(Config $config, SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        return [
            Request::FIELD_TRANSACTION_TYPE => 'D',
            Request::FIELD_TRANSACTION_AMOUNT => $this->formatPrice($this->subjectReader->readAmount($buildSubject)),
            Request::FIELD_CARD_ID => $payment->getAdditionalInformation(Request::FIELD_CARD_ID),
            Request::FIELD_CVV2 => $payment->getAdditionalInformation(Request::FIELD_CVV2),
            Request::FIELD_TRANSACTION_ID => $payment->getAdditionalInformation(Request::FIELD_TRANSACTION_ID),
            Request::FIELD_MOTO_ECOMMERCE_IND => '7',
            Request::FIELD_INVOICE_NUMBER => $payment->getOrder()->getIncrementId()
        ];
    }
}
