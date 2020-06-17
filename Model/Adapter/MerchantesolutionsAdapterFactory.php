<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Model\Adapter;

use Magento\Framework\ObjectManagerInterface;
use Merchante\Merchante\Gateway\Config\Config;

/**
 * Class MerchanteAdapterFactory
 * @package Merchante\Merchante\Model\Adapter
 */
class MerchanteAdapterFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * MerchanteAdapterFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param Config $config
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
    }

    /**
     * Creates instance of Merchante Adapter.
     * @param int|null $storeId
     * @return MerchanteAdapter
     */
    public function create($storeId = null)
    {
        return $this->objectManager->create(
            MerchanteAdapter::class,
            [
                'profileId' => $this->config->getValue(Config::KEY_PROFILE_ID, $storeId),
                'profileKey' => $this->config->getValue(Config::KEY_PROFILE_KEY, $storeId),
                'environmentUrl' => $this->config->getEnvironmentUrl(),
            ]
        );
    }
}
