<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Model\Adminhtml\Source;

use Magento\Payment\Model\MethodInterface;

/**
 * Class PaymentAction
 * @package Merchante\Merchante\Model\Adminhtml\Source
 */
class PaymentAction
{
    /**
     * Possible actions on order place
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => MethodInterface::ACTION_AUTHORIZE,
                'label' => __('Pre-Authorization'),
            ],
            [
                'value' => MethodInterface::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Sale'),
            ]
        ];
    }
}
