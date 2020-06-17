<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Plugin;

use Merchante\Merchante\Gateway\Http\Data\Response;
use Merchante\Merchante\Helper\Data as Helper;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Status\History;

/**
 * Class ClosedTransactionMessage
 * @package Merchante\Merchante\Plugin
 */
class ClosedTransactionMessage
{
    const CANCEL = 'cancel';
    const VOID = 'void';
    const REFUND = 'refund';

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * List of additional details
     * @var array
     */
    protected $additionalInformationMapping = [
        self::CANCEL => [
            Response::AUTH_RESPONSE_TEXT,
        ],
        self::VOID => [
            Response::AUTH_RESPONSE_TEXT,
        ],
        self::REFUND => [
            Response::AUTH_CODE,
            Response::AUTH_RESPONSE_TEXT,
        ],
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
     * @param Payment $subject
     * @param string|History $message
     * @return string
     */
    public function afterPrependMessage(Payment $subject, $message)
    {
        if (!$this->helper->isInternalMethod($subject)) {
            return $message;
        }

        $result = $message;
        foreach ($this->additionalInformationMapping as $txType => $fields) {
            if (strpos(strtolower($message), $txType) !== false) {
                $result = $message . '<br>';
                $paymentInfo = $subject->getAdditionalInformation();
                foreach ($fields as $field) {
                    if (isset($paymentInfo[$field])) {
                        $result .= __($field) . ": $paymentInfo[$field] <br>";
                    }
                }
            }
        }
        return $result;
    }
}
