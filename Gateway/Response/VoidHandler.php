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
 * Class VoidHandler
 * @package Magento\Merchantesolutions\Gateway\Response
 */
class VoidHandler extends TransactionIdHandler
{
    /**
     * @param Payment $orderPayment
     * @param Response $transaction
     * @return void
     */
    protected function setTransactionId(Payment $orderPayment, Response $transaction)
    {
        $orderPayment->setTransactionId($transaction[Response::TRANSACTION_ID]);
    }

    /**
     * Whether transaction should be closed
     *
     * @return bool
     */
    protected function shouldCloseTransaction()
    {
        return true;
    }

    /**
     * Whether parent transaction should be closed
     *
     * @param Payment $orderPayment
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function shouldCloseParentTransaction(Payment $orderPayment)
    {
        return true;
    }
}
