<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Response;

use Magento\Merchantesolutions\Gateway\Http\Data\Response;
use Magento\Sales\Model\Order\Payment;

/**
 * Class RefundHandler
 * @package Magento\Merchantesolutions\Gateway\Response
 */
class RefundHandler extends VoidHandler
{
    /**
     * List of additional details
     * @var array
     */
    protected $additionalInformationMapping = [
        Response::TRANSACTION_ID,
        Response::AUTH_RESPONSE_TEXT,
        Response::AUTH_CODE,
    ];

    /**
     * Whether parent transaction should be closed
     *
     * @param Payment $orderPayment
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function shouldCloseParentTransaction(Payment $orderPayment)
    {
        return !(bool)$orderPayment->getCreditmemo()->getInvoice()->canRefund();
    }
}