<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Request\HostedCheckout\Authorize;

use Magento\Merchantesolutions\Api\Data\TransactionInterface as Transaction;
use Magento\Merchantesolutions\Gateway\Config\Config;
use Magento\Merchantesolutions\Gateway\Http\Data\Request;
use Magento\Merchantesolutions\Gateway\Http\Data\Response;
use Magento\Merchantesolutions\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Magento\Sales\Model\Order\Payment;


/**
 * Class PaymentDataBuilder
 * @package Magento\Merchantesolutions\Gateway\Request
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
            Request::FIELD_TRANSACTION_TYPE => 'P',
            Request::FIELD_TRANSACTION_AMOUNT => $this->formatPrice($this->subjectReader->readAmount($buildSubject)),
            Request::FIELD_CARD_ID => $payment->getAdditionalInformation(Transaction::CARD_ID),
            Request::FIELD_TRANSACTION_ID => $payment->getAdditionalInformation(Response::TRANSACTION_ID),
            Response::ERROR_CODE => $payment->getAdditionalInformation(Response::ERROR_CODE),
            Response::AUTH_RESPONSE_TEXT => $payment->getAdditionalInformation(Response::AUTH_RESPONSE_TEXT),
            Request::FIELD_MOTO_ECOMMERCE_IND => '7',
            Request::FIELD_INVOICE_NUMBER => $payment->getOrder()->getIncrementId()
        ];
    }
}
