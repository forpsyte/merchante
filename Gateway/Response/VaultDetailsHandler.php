<?php
/**
 * Copyright © Visiture, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Merchante\Merchante\Gateway\Response;

use Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Merchante\Merchante\Gateway\Http\Data\Response;
use Merchante\Merchante\Observer\DataAssignObserver;
use Merchante\Merchante\Gateway\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterface;
use Magento\Sales\Api\Data\OrderPaymentExtensionInterfaceFactory;
use Magento\Sales\Model\Order\Payment;
use Magento\Vault\Api\Data\PaymentTokenFactoryInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;


/**
 * Class VaultDetailsHandler
 * @package Merchante\Merchante\Gateway\Response
 */
class VaultDetailsHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @var PaymentTokenFactoryInterface
     */
    protected $paymentTokenFactory;

    /**
     * @var OrderPaymentExtensionInterfaceFactory
     */
    protected $paymentExtensionFactory;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * VaultDetailsHandler constructor.
     * @param SubjectReader $subjectReader
     * @param OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory
     * @param PaymentTokenFactoryInterface $paymentTokenFactory
     * @param Json $serializer
     */
    public function __construct(
        SubjectReader $subjectReader,
        OrderPaymentExtensionInterfaceFactory $paymentExtensionFactory,
        PaymentTokenFactoryInterface $paymentTokenFactory,
        Json $serializer = null
    ) {
        $this->subjectReader = $subjectReader;
        $this->paymentTokenFactory = $paymentTokenFactory;
        $this->paymentExtensionFactory = $paymentExtensionFactory;
        $this->serializer  = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $transaction = $this->subjectReader->readTransaction($response);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();

        // add vault payment token entity to extension attributes
        $paymentToken = $this->getVaultPaymentToken($payment, $transaction);
        if (null !== $paymentToken) {
            $extensionAttributes = $this->getExtensionAttributes($payment);
            $extensionAttributes->setVaultPaymentToken($paymentToken);
        }
    }

    /**
     * Get vault payment token entity
     *
     * @param Payment $payment
     * @param Response $transaction
     * @return PaymentTokenInterface|null
     * @throws Exception
     */
    protected function getVaultPaymentToken(Payment $payment, Response $transaction)
    {
        // Check token existing in gateway response
        $token = isset($transaction[DataAssignObserver::CARD_ID]) ? $transaction[DataAssignObserver::CARD_ID] : null;
        if (empty($token)) {
            return null;
        }

        /** @var PaymentTokenInterface $paymentToken */
        $paymentToken = $this->paymentTokenFactory->create(PaymentTokenFactoryInterface::TOKEN_TYPE_CREDIT_CARD);
        $paymentToken->setGatewayToken($token);
        $paymentToken->setExpiresAt($this->getExpirationDate($payment));

        $paymentToken->setTokenDetails($this->convertDetailsToJSON([
            'type' => $payment->getAdditionalInformation(DataAssignObserver::CC_TYPE),
            'maskedCC' => $payment->getAdditionalInformation(DataAssignObserver::CC_NUMBER),
            'expirationDate' => $payment->getAdditionalInformation(DataAssignObserver::CC_EXP_MONTH) .
                '/' . $payment->getAdditionalInformation(DataAssignObserver::CC_EXP_YEAR)
        ]));

        return $paymentToken;
    }

    /**
     * @param Payment $payment
     * @return string
     * @throws Exception
     */
    private function getExpirationDate(Payment $payment)
    {
        $expDate = new \DateTime(
            $payment->getAdditionalInformation(DataAssignObserver::CC_EXP_YEAR)
            . '-'
            . $payment->getAdditionalInformation(DataAssignObserver::CC_EXP_MONTH)
            . '-'
            . '01'
            . ' '
            . '00:00:00',
            new \DateTimeZone('UTC')
        );
        $expDate->add(new \DateInterval('P1M'));
        return $expDate->format('Y-m-d 00:00:00');
    }

    /**
     * Convert payment token details to JSON
     * @param array $details
     * @return string
     */
    private function convertDetailsToJSON($details)
    {
        $json = $this->serializer->serialize($details);
        return $json ? $json : '{}';
    }

    /**
     * Get payment extension attributes
     *
     * @param Payment $payment
     * @return OrderPaymentExtensionInterface
     */
    private function getExtensionAttributes(Payment $payment)
    {
        $extensionAttributes = $payment->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->paymentExtensionFactory->create();
            $payment->setExtensionAttributes($extensionAttributes);
        }
        return $extensionAttributes;
    }
}
