<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Merchantesolutions\Gateway\Http\Data\Response;
use Magento\Sales\Model\Order\Payment;

/**
 * Class SettleDetailsHandler
 * @package Magento\Merchantesolutions\Gateway\Response
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
