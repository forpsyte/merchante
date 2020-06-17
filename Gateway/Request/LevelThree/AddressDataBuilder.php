<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Gateway\Request\LevelThree;

use Merchante\Merchante\Gateway\Http\Data\Request;
use Merchante\Merchante\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;

/**
 * Class AddressDataBuilder
 * @package Merchante\Merchante\Gateway\Request
 */
class AddressDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        SubjectReader $subjectReader,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->subjectReader = $subjectReader;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        /** @var Order $order */
        $order = $paymentDO->getOrder();
        $storeId = $order->getStoreId();
        $billingAddress = $order->getBillingAddress();

        if ($billingAddress) {
            return [
                Request::FIELD_CARDHOLDER_STREET_ADDRESS => $billingAddress->getStreet(),
                Request::FIELD_CARDHOLDER_ZIP => $billingAddress->getPostcode(),
                Request::FIELD_SHIP_TO_ZIP => $billingAddress->getPostcode(),
                Request::FIELD_SHIP_FROM_ZIP => $this->scopeConfig->getValue('shipping/origin/postcode'),
                Request::FIELD_DEST_COUNTRY_CODE => $billingAddress->getCountryId()
            ];
        } else {
            return [];
        }
    }
}
