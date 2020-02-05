<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Merchantesolutions\Model\Ui\HostedCheckout;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Merchantesolutions\Gateway\Config\Config;
use Magento\Merchantesolutions\Gateway\Config\HostedCheckout\Config as HcConfig;
use Magento\Merchantesolutions\Model\Ui\ConfigProvider as CcConfigProvider;
use Magento\Payment\Model\CcConfig;
use Magento\Payment\Model\MethodInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

/**
 * Class ConfigProvider
 * @package Magento\Merchantesolutions\Model\Ui
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'merchantesolutions_hosted_checkout';
    const CC_VAULT_CODE = 'merchantesolutions_hosted_checkout_cc_vault';
    const AUTH_TOKENIZATION = 'PT';
    const SALE_TOKENIZATION = 'DT';

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
     * @var HcConfig
     */
    protected $hcConfig;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var array
     */
    protected $transactionTypeMapping = [
        MethodInterface::ACTION_AUTHORIZE => self::AUTH_TOKENIZATION,
        MethodInterface::ACTION_AUTHORIZE_CAPTURE => self::SALE_TOKENIZATION,
    ];

    /**
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * ConfigProvider constructor.
     * @param Config $config
     * @param CcConfig $ccConfig
     * @param HcConfig $hcConfig
     * @param UrlInterface $urlBuilder
     * @param SessionManagerInterface $session
     * @param AssetRepository $assetRepository
     */
    public function __construct(
        Config $config,
        CcConfig $ccConfig,
        HcConfig $hcConfig,
        UrlInterface $urlBuilder,
        SessionManagerInterface $session,
        AssetRepository $assetRepository
    ) {
        $this->config = $config;
        $this->ccConfig = $ccConfig;
        $this->hcConfig = $hcConfig;
        $this->urlBuilder = $urlBuilder;
        $this->session = $session;
        $this->assetRepository = $assetRepository;
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
                    'isActive' => $this->hcConfig->isActive($storeId),
                    'ccTypesMapper' => $this->config->getCcTypesMapper(),
                    'availableCardTypes' => $this->config->getAvailableCardTypes($storeId),
                    'useSimulator' => $this->hcConfig->useSandbox($storeId),
                    'profileId' => $this->config->getProfileId($storeId),
                    'ccVaultCode' => self::CC_VAULT_CODE,
                    'transactionType' => $this->getTransactionType(),
                    'returnUrl' => $this->getReturnUrl(),
                    'responseUrl' => $this->getResponseUrl(),
                    'cancelUrl' => $this->getCancelUrl(),
                    'gatewayUrl' => $this->hcConfig->getGatewayUrl(),
                    'verificationUrl' => $this->getVerificationUrl(),
                    'spinnerAssetUrl' => $this->getSpinnerAssetUrl()
                ]
            ],
        ];
    }

    /**
     * Get the transaction type for hosted checkout.
     *
     * @return string
     */
    public function getTransactionType()
    {
        $paymentAction = $this->hcConfig->getValue('payment_action');
        return $this->transactionTypeMapping[$paymentAction];
    }

    /**
     * Get the return url for hosted checkout to forward to when the
     * payment is completed.
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->urlBuilder->getUrl('customer/account', ['_secure' => true]);
    }

    /**
     * Get the return url for hosted checkout to forward to when the
     * payment is completed.
     *
     * @return string
     */
    public function getResponseUrl()
    {
        return $this->urlBuilder->getUrl(CcConfigProvider::CODE . '/payment/place', ['_secure' => true]);
    }

    /**
     * Get the verification url for hosted checkout to verify payment.
     *
     * @return string
     */
    public function getVerificationUrl()
    {
        return $this->urlBuilder->getUrl(CcConfigProvider::CODE . '/payment/verify', ['_secure' => true]);
    }

    /**
     * Get spinner asset url.
     *
     * @return string
     */
    public function getSpinnerAssetUrl()
    {
        try {
            $asset = $this->assetRepository->createAsset('Magento_Merchantesolutions::images/loader-1.gif');
            return $asset->getUrl();
        } catch (LocalizedException $exception) {
            return '';
        }

    }

    /**
     * Get the cancel url for hosted checkout to forward to when the
     * payment is canceled.
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->urlBuilder->getUrl('checkout/cart');
    }
}
