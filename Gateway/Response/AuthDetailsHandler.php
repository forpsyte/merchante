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
 * Class AuthDetailsHandler
 * @package Magento\Merchantesolutions\Gateway\Response
 */
class AuthDetailsHandler extends AbstractHandler
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
        $payment->setCcTransId($transaction[Response::TRANSACTION_ID]);
        $payment->setLastTransId($transaction[Response::TRANSACTION_ID]);
        $this->updatePaymentInformation($transaction, $payment);
    }
}
