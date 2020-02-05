<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Request\Refund;

use Magento\Merchantesolutions\Gateway\Config\Config;
use Magento\Merchantesolutions\Gateway\Http\Data\Request;
use Magento\Merchantesolutions\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;


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
        $payment = $paymentDO->getPayment();

        return [
            Request::FIELD_TRANSACTION_TYPE => 'U',
            Request::FIELD_TRANSACTION_AMOUNT => $this->formatPrice($this->subjectReader->readAmount($buildSubject)),
            Request::FIELD_TRANSACTION_ID => $payment->getAdditionalInformation(Request::FIELD_TRANSACTION_ID)
        ];
    }
}
