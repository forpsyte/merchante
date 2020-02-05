<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Merchantesolutions\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Merchantesolutions\Model\Adminhtml\Source\Environment;
use Psr\Log\LoggerInterface;

/**
 * Class Config
 * @package Magento\Merchantesolutions\Gateway\Config
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    const KEY_PROFILE_ID = 'profile_id';
    const KEY_PROFILE_KEY = 'profile_key';
    const KEY_ACTIVE = 'active';
    const KEY_ENVIRONMENT = 'environment';
    const KEY_USE_SANDBOX = 'use_sandbox';
    const KEY_USE_CVV = 'usecvv';
    const KEY_CC_TYPES = 'cctypes';
    const KEY_CC_TYPES_MERCHANTESOLUTIONS_MAPPER = 'cctypes_merchantesolutions_mapper';
    const KEY_USE_LEVEL_THREE = 'use_level_iii';
    const KEY_ALT_TAX_RATES = 'alt_tax_rates';
    const KEY_COMMODITY_CODE = 'commodity_code';
    const KEY_DEBUG = 'debug';
    const KEY_AVS_RESULT_NON_RISK = 'avs_result_non_risk';
    const KEY_AVS_RESULT_RISK = 'avs_result_risk';
    const PATH_CC_VAULT_ACTIVE = 'payment/merchantesolutions_cc_vault/active';
    const PATH_MERCHANT_VAT_NUMBER = 'general/store_information/merchant_vat_number';

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var string|null
     */
    private $methodCode;

    /**
     * Merchantesolutions config constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param LoggerInterface $logger
     * @param null|string $methodCode
     * @param string $pathPattern
     * @param Json|null $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        LoggerInterface $logger,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN,
        Json $serializer = null
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->encryptor = $encryptor;
        $this->methodCode = $methodCode;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Json::class);
    }

    /**
     * Retrieve profile id
     *
     * @param int|null $storeId
     * @return string
     */
    public function getProfileId($storeId = null)
    {
        $profileId = $this->getValue(self::KEY_PROFILE_ID, $storeId);

        return !empty($profileId) ? $profileId : '';
    }

    /**
     * Retrieve profile key
     *
     * @param int|null $storeId
     * @return string
     */
    public function getProfileKey($storeId = null)
    {
        $profileKey = $this->getValue(self::KEY_PROFILE_KEY, $storeId);

        return !empty($profileKey) ? $profileKey : '';
    }

    /**
     * Retrieve available credit card types
     *
     * @param int|null $storeId
     * @return array
     */
    public function getAvailableCardTypes($storeId = null)
    {
        $ccTypes = $this->getValue(self::KEY_CC_TYPES, $storeId);

        return !empty($ccTypes) ? explode(',', $ccTypes) : [];
    }

    /**
     * Retrieve mapper for Non risk AVS result
     *
     * @return array
     */
    public function getNonRiskAvsResult()
    {
        $result = json_decode(
            $this->getValue(self::KEY_AVS_RESULT_NON_RISK),
            true
        );

        return is_array($result) ? $result : [];
    }

    /**
     * Retrieve mapper for Non risk AVS result
     *
     * @return array
     */
    public function getRiskAvsResult()
    {
        $result = json_decode(
            $this->getValue(self::KEY_AVS_RESULT_RISK),
            true
        );

        return is_array($result) ? $result : [];
    }

    /**
     * Retrieve mapper between Magento and Merchant e-Solutions card types
     *
     * @return array
     */
    public function getCcTypesMapper()
    {
        $result = json_decode(
            $this->getValue(self::KEY_CC_TYPES_MERCHANTESOLUTIONS_MAPPER),
            true
        );

        return is_array($result) ? $result : [];
    }

    /**
     * Gets value of configured environment.
     *
     * Possible values: live, cert or test.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEnvironment($storeId = null)
    {
        return $this->getValue(self::KEY_ENVIRONMENT, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getEnvironmentUrl($storeId = null)
    {
        return Environment::ENVIRONMENT_MAP[$this->getEnvironment()];
    }

    /**
     * Retrieves environment configuration.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function useSandbox($storeId = null)
    {
        return in_array(
            $this->getEnvironment($storeId),
            [
                Environment::ENVIRONMENT_TEST,
                Environment::ENVIRONMENT_CERT
            ]
        );
    }

    /**
     * @param string $scope
     * @param null $scopeCode
     * @return mixed
     */
    public function getMerchantVatNumber($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::PATH_MERCHANT_VAT_NUMBER, $scope, $scopeCode);
    }

    /**
     * Gets Payment configuration status.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_ACTIVE, $storeId);
    }

    /**
     * @param string $scope
     * @param null $scopeCode
     * @return bool
     */
    public function isVaultActive($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return (bool) $this->scopeConfig->getValue(self::PATH_CC_VAULT_ACTIVE, $scope, $scopeCode);
    }

    /**
     * Gets the configured commodity code.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getCommodityCode($storeId = null)
    {
        return $this->getValue(self::KEY_COMMODITY_CODE);
    }

    /**
     * Checks if cvv field is enabled.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isCvvEnabled($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_USE_CVV, $storeId);
    }

    /**
     * Get Level II/III configuration status
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isLevelThreeEnabled($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_USE_LEVEL_THREE, $storeId);
    }

    /**
     * Gets configuration debug status.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDebug($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_DEBUG, $storeId);
    }

    /**
     * @param null $storeId
     * @return array
     */
    public function getAlternativeTaxRates($storeId = null)
    {
        return explode(',', $this->getValue(self::KEY_ALT_TAX_RATES, $storeId));
    }

    /**
     * @param $message
     * @param array $vars
     */
    public function debug($message, $vars = [])
    {
        if (!$this->isDebug()) {
            return;
        }
        $this->logger->debug($message, $vars);
    }
}
