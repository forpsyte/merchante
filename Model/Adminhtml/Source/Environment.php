<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Model\Adminhtml\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Environment
 * @package Merchante\Merchante\Model\Adminhtml\Source
 */
class Environment implements OptionSourceInterface
{
    const ENVIRONMENT_LIVE = 'live';
    const ENVIRONMENT_CERT = 'cert';
    const ENVIRONMENT_TEST = 'test';
    const ENVIRONMENT_LIVE_ENDPOINT = 'https://api.merchante-solutions.com/mes-api/tridentApi';
    const ENVIRONMENT_CERT_ENDPOINT = 'https://cert.merchante-solutions.com/mes-api/tridentApi';
    const ENVIRONMENT_TEST_ENDPOINT = 'https://test.merchante-solutions.com/mes-api/tridentApi';
    const ENVIRONMENT_MAP = [
        self::ENVIRONMENT_LIVE => self::ENVIRONMENT_LIVE_ENDPOINT,
        self::ENVIRONMENT_CERT => self::ENVIRONMENT_CERT_ENDPOINT,
        self::ENVIRONMENT_TEST => self::ENVIRONMENT_TEST_ENDPOINT
    ];

    /**
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENVIRONMENT_LIVE,
                'label' => 'Live',
            ],
            [
                'value' => self::ENVIRONMENT_CERT,
                'label' => 'Cert'
            ],
            [
                'value' => self::ENVIRONMENT_TEST,
                'label' => 'Test'
            ],
        ];
    }
}
