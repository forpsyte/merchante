<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Plugin;

use Magento\Framework\Phrase;
use Merchante\Merchante\Gateway\Http\Data\Response;
use Merchante\Merchante\Helper\Data as Helper;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment\State\AuthorizeCommand;

/**
 * Class TransactionMessage
 * @package Merchante\Merchante\Plugin
 */
class AuthorizationTransactionMessage
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * List of additional details
     * @var array
     */
    protected $additionalInformationMapping = [
        Response::AUTH_CODE,
        Response::AVS_RESULT,
        Response::CVV2_RESULT,
        Response::AUTH_RESPONSE_TEXT,
    ];

    /**
     * AuthorizationTransactionMessage constructor.
     *
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param AuthorizeCommand $subject
     * @param Phrase $result
     * @param OrderPaymentInterface $payment
     * @return Phrase
     */
    public function afterExecute(AuthorizeCommand $subject, Phrase $result, OrderPaymentInterface $payment)
    {
        if (!$this->helper->isInternalMethod($payment)) {
            return $result;
        }

        $paymentInfo = $payment->getAdditionalInformation();
        $message = $result . "<br>";

        foreach ($this->additionalInformationMapping as $item) {
            $message .= isset($paymentInfo[$item]) ? __($item) . ": $paymentInfo[$item] <br>" : '';
        }
        return __($message);
    }
}
