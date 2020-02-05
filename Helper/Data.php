<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Helper;

use Magento\Sales\Api\Data\OrderPaymentInterface;

/**
 * Class Data
 * @package Magento\Merchantesolutions\Helper
 */
class Data
{
    /**
     * List of internal payment methods
     *
     * @var array
     */
    protected $methods;

    /**
     * Data constructor.
     *
     * @param array $methods
     */
    public function __construct(
        array $methods
    ) {
        $this->methods = $methods;
    }

    /**
     * Determine if the payment method is internal (i.e. not Braintree or Authorize.net).
     * This method was created for plugins that modified the payment transaction
     * message but it can be utilized in other plugins if needed.
     *
     * @param OrderPaymentInterface $payment
     * @return bool
     */
    public function isInternalMethod(OrderPaymentInterface $payment)
    {
        return in_array($payment->getMethod(), $this->methods);
    }
}