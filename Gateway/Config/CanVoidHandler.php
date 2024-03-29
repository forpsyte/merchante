<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Gateway\Config;

use Merchante\Merchante\Gateway\SubjectReader;
use Magento\Payment\Gateway\Config\ValueHandlerInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class CanVoidHandler
 * @package Merchante\Merchante\Gateway\Config
 */
class CanVoidHandler implements ValueHandlerInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * CanVoidHandler constructor.
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Retrieve method configured value
     *
     * @param array $subject
     * @param int|null $storeId
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handle(array $subject, $storeId = null)
    {
        $paymentDO = $this->subjectReader->readPayment($subject);

        $payment = $paymentDO->getPayment();
        return $payment instanceof Payment && !(bool)$payment->getAmountPaid();
    }
}
