<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Gateway\Request\LevelThree;

use Merchante\Merchante\Gateway\Config\Config;
use Merchante\Merchante\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class VisaPaymentDataBuilder
 * @package Merchante\Merchante\Gateway\Request\LevelThree
 */
class VisaCustomerDataBuilder implements BuilderInterface
{
    const CUSTOMER_TAX_ID = 'customer_tax_id';

    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     * @param SubjectReader $subjectReader
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        Config $config,
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();

        return [
            self::CUSTOMER_TAX_ID => $payment->getAdditionalInformation(self::CUSTOMER_TAX_ID)
        ];
    }
}
