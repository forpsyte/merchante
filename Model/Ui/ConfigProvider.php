<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Merchante\Merchante\Gateway\Config\Config;
use Magento\Payment\Model\CcConfig;

/**
 * Class ConfigProvider
 * @package Merchante\Merchante\Model\Ui
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'merchante';
    const CC_VAULT_CODE = 'merchante_cc_vault';
    const RBS_CODE = 'merchante_rbs';

    /**
     * @var Config
     */
    protected $config;
    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @var CcConfig
     */
    protected $ccConfig;

    /**
     * ConfigProvider constructor.
     * @param Config $config
     * @param CcConfig $ccConfig
     * @param SessionManagerInterface $session
     */
    public function __construct(
        Config $config,
        CcConfig $ccConfig,
        SessionManagerInterface $session
    ) {
        $this->config = $config;
        $this->session = $session;
        $this->ccConfig = $ccConfig;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $storeId = $this->session->getStoreId();
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive($storeId),
                    'ccTypesMapper' => $this->config->getCcTypesMapper(),
                    'availableCardTypes' => $this->config->getAvailableCardTypes($storeId),
                    'useCvv' => $this->config->isCvvEnabled($storeId),
                    'environment' => $this->config->getEnvironment($storeId),
                    'environmentUrl' => $this->config->getEnvironmentUrl(),
                    'use_sandbox' => $this->config->useSandbox($storeId),
                    'profile_id' => $this->config->getProfileId($storeId),
                    'profile_key' => $this->config->getProfileKey($storeId),
                    'ccVaultCode' => self::CC_VAULT_CODE,
                ]
            ],
        ];
    }
}
