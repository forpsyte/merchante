<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Request\Authorize;

use Magento\Merchantesolutions\Gateway\Config\Config;
use Magento\Merchantesolutions\Gateway\Http\Data\Request;
use Magento\Merchantesolutions\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Magento\Sales\Model\Order\Payment;


/**
 * Class PaymentDataBuilder
 * @package Magento\Merchantesolutions\Gateway\Request
 */
class VaultDataBuilder implements BuilderInterface
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
        $token = $payment->getExtensionAttributes()->getVaultPaymentToken();

        return [
            Request::FIELD_TRANSACTION_TYPE => 'P',
            Request::FIELD_TRANSACTION_AMOUNT => $this->formatPrice($this->subjectReader->readAmount($buildSubject)),
            Request::FIELD_CARD_ID => $token->getGatewayToken(),
            Request::FIELD_MOTO_ECOMMERCE_IND => '7',
            Request::FIELD_INVOICE_NUMBER => $payment->getOrder()->getIncrementId(),
            Request::FIELD_CARD_ON_FILE => 'Yes',
        ];
    }
}