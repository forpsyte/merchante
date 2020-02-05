<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Gateway\Request;

use Magento\Merchantesolutions\Gateway\Http\Data\Request;
use Magento\Merchantesolutions\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class AddressDataBuilder
 * @package Magento\Merchantesolutions\Gateway\Request
 */
class AddressDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $billingAddress = $order->getBillingAddress();

        if ($billingAddress) {
            return [
                Request::FIELD_CARDHOLDER_STREET_ADDRESS => $billingAddress->getStreetLine1(),
                Request::FIELD_CARDHOLDER_ZIP => $billingAddress->getPostcode()
            ];
        } else {
            return [];
        }
    }
}
