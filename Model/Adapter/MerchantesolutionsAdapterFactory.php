<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Model\Adapter;

use Magento\Framework\ObjectManagerInterface;
use Magento\Merchantesolutions\Gateway\Config\Config;

/**
 * Class MerchantesolutionsAdapterFactory
 * @package Magento\Merchantesolutions\Model\Adapter
 */
class MerchantesolutionsAdapterFactory
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
     * MerchantesolutionsAdapterFactory constructor.
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
     * Creates instance of Merchantesolutions Adapter.
     * @param int|null $storeId
     * @return MerchantesolutionsAdapter
     */
    public function create($storeId = null)
    {
        return $this->objectManager->create(
            MerchantesolutionsAdapter::class,
            [
                'profileId' => $this->config->getValue(Config::KEY_PROFILE_ID, $storeId),
                'profileKey' => $this->config->getValue(Config::KEY_PROFILE_KEY, $storeId),
                'environmentUrl' => $this->config->getEnvironmentUrl(),
            ]
        );
    }
}
