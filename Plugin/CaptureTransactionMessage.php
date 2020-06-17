<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Plugin;

use Magento\Framework\Phrase;
use Merchante\Merchante\Gateway\Command\CaptureStrategyCommand;
use Merchante\Merchante\Gateway\Http\Data\Response;
use Merchante\Merchante\Helper\Data as Helper;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment\State\CaptureCommand;

/**
 * Class TransactionMessage
 * @package Merchante\Merchante\Plugin
 */
class CaptureTransactionMessage
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
        CaptureStrategyCommand::CAPTURE => [
            Response::AUTH_RESPONSE_TEXT
        ],
        CaptureStrategyCommand::SALE => [
            Response::AUTH_CODE,
            Response::AVS_RESULT,
            Response::CVV2_RESULT,
            Response::AUTH_RESPONSE_TEXT,
        ]
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
     * @param CaptureCommand $subject
     * @param Phrase $result
     * @param OrderPaymentInterface $payment
     * @return Phrase
     */
    public function afterExecute(CaptureCommand $subject, Phrase $result, OrderPaymentInterface $payment)
    {
        if (!$this->helper->isInternalMethod($payment)) {
            return $result;
        }

        $paymentInfo = $payment->getAdditionalInformation();
        $message = $result . "<br>";
        $transactionType = $payment->getAuthorizationTransaction() ?
            CaptureStrategyCommand::CAPTURE :
            CaptureStrategyCommand::SALE;

        foreach ($this->additionalInformationMapping[$transactionType] as $item) {
            $message .= isset($paymentInfo[$item]) ? __($item) . ": $paymentInfo[$item] <br>" : '';
        }
        return __($message);
    }
}
