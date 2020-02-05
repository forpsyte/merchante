<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway;

use InvalidArgumentException;
use Magento\Merchantesolutions\Gateway\Http\Data\Response;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper;

/**
 * Class SubjectReader
 * @package Magento\Merchantesolutions\Gateway
 */
class SubjectReader
{
    /**
     * Reads response object from subject
     *
     * @param array $subject
     * @return object
     */
    public function readResponseObject(array $subject)
    {
        $response = Helper\SubjectReader::readResponse($subject);
        if (!isset($response['object']) || !is_object($response['object'])) {
            throw new InvalidArgumentException('Response object does not exist');
        }

        return $response['object'];
    }

    /**
     * Reads payment from subject
     *
     * @param array $subject
     * @return PaymentDataObjectInterface
     */
    public function readPayment(array $subject)
    {
        return Helper\SubjectReader::readPayment($subject);
    }

    /**
     * Reads transaction from the subject.
     *
     * @param array $subject
     * @return Response
     * @throws InvalidArgumentException if the subject doesn't contain transaction details.
     */
    public function readTransaction(array $subject)
    {
        if (!isset($subject['object']) || !is_object($subject['object'])) {
            throw new InvalidArgumentException('Response object does not exist.');
        }

        if (!isset($subject['object'])
            || !$subject['object'] instanceof Response
        ) {
            throw new InvalidArgumentException('The object is not a class TpgTransaction.');
        }

        return $subject['object'];
    }

    /**
     * Reads amount from subject
     *
     * @param array $subject
     * @return mixed
     */
    public function readAmount(array $subject)
    {
        return Helper\SubjectReader::readAmount($subject);
    }

    /**
     * Reads customer id from subject
     *
     * @param array $subject
     * @return int
     */
    public function readCustomerId(array $subject)
    {
        if (!isset($subject['customer_id'])) {
            throw new InvalidArgumentException('The "customerId" field does not exists');
        }

        return (int) $subject['customer_id'];
    }

    /**
     * Reads store's ID, otherwise returns null.
     *
     * @param array $subject
     * @return int|null
     */
    public function readStoreId(array $subject)
    {
        return $subject['store_id'] ?? null;
    }
}