<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Merchante\Merchante\Gateway\Request;

use Merchante\Merchante\Gateway\Config\Config;
use Merchante\Merchante\Gateway\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Vault Data Builder
 */
class VaultDataBuilder implements BuilderInterface
{
    /**
     * The field that determines whether the token should
     * be stored in the Vault.
     */
    const STORE_CARD = 'store_card';

    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @var Config
     */
    protected $config;

    /**
     * VaultDataBuilder constructor.
     * @param SubjectReader $subjectReader
     * @param Config $config
     */
    public function __construct(
        SubjectReader $subjectReader,
        Config $config
    ) {
        $this->subjectReader = $subjectReader;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        $willStoreCard = !!$payment->getAdditionalInformation(self::STORE_CARD);
        $shouldStoreCard = $this->config->isVaultActive() && $willStoreCard;
        return $shouldStoreCard ? [self::STORE_CARD => 'y'] : [];
    }
}
