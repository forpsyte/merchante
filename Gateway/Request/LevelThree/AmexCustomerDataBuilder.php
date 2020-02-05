<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Request\LevelThree;

use Magento\Merchantesolutions\Gateway\Config\Config;
use Magento\Merchantesolutions\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order;

/**
 * Class VisaPaymentDataBuilder
 * @package Magento\Merchantesolutions\Gateway\Request\LevelThree
 */
class AmexCustomerDataBuilder implements BuilderInterface
{
    const REQUESTER_NAME = 'requester_name';

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

        /** @var Order $order */
        $order = $payment->getOrder();

        return [
            self::REQUESTER_NAME => $order->getCustomerName()
        ];
    }
}