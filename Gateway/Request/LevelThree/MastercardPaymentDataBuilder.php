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
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;

/**
 * Class VisaPaymentDataBuilder
 * @package Magento\Merchantesolutions\Gateway\Request\LevelThree
 */
class MastercardPaymentDataBuilder implements BuilderInterface
{
    use Formatter;

    const TRANSACTION_ID = 'transaction_id';
    const DUTY_AMOUNT = 'duty_amount';
    const ALT_TAX_AMOUNT = 'alt_tax_amount';
    const ALT_TAX_AMOUNT_INDICATOR = 'alt_tax_amount_indicator';

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

        return [
            self::DUTY_AMOUNT => 0.00,
            self::ALT_TAX_AMOUNT => $this->formatPrice($taxAmount), //TODO: Update for vat amount
            self::ALT_TAX_AMOUNT_INDICATOR => $taxAmount > 0 ? 'Y' : 'N'
        ];
    }
}
