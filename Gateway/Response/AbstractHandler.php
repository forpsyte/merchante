<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Merchantesolutions\Gateway\Http\Data\Response;
use Magento\Merchantesolutions\Gateway\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class AbstractHandler
 * @package Magento\Merchantesolutions\Gateway\Response
 */
abstract class AbstractHandler implements HandlerInterface
{
    /**
     * List of additional details
     * @var array
     */
    protected $additionalInformationMapping = [
        Response::TRANSACTION_ID,
        Response::ERROR_CODE,
        Response::AUTH_RESPONSE_TEXT,
        Response::AVS_RESULT,
        Response::AUTH_CODE,
        Response::CVV2_RESULT,
    ];

    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * AuthDetailsHandler constructor.
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param Response $transaction
     * @param Payment $payment
     * @throws LocalizedException
     * @return void
     */
    protected function updatePaymentInformation(Response $transaction, Payment $payment)
    {
        foreach ($this->additionalInformationMapping as $item) {
            if (!isset($transaction[$item])) {
                continue;
            }
            $payment->setAdditionalInformation($item, $transaction[$item]);
        }
    }
}