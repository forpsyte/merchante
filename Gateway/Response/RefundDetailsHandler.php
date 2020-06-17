<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Merchante\Merchante\Gateway\Http\Data\Response;
use Magento\Sales\Model\Order\Payment;

/**
 * Class SettleDetailsHandler
 * @package Merchante\Merchante\Gateway\Response
 */
class RefundDetailsHandler extends AbstractHandler
{
    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $transaction = $this->subjectReader->readTransaction($response);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        $lastCcTransId = $payment->getCcTransId();
        $payment->setCcTransId($transaction[Response::TRANSACTION_ID]);
        $payment->setLastTransId($lastCcTransId);
        $this->updatePaymentInformation($transaction, $payment);
    }
}
